<?php

namespace frontend\controllers;

use Yii;
use Exception;
use yii\web\Response;
use yii\base\ExitException;
use yii\web\NotFoundHttpException;
use common\models\Order;
use common\helpers\ConstHelper;
use common\models\OrderDocument;
use common\exceptions\OrderException;
use common\models\searches\OrderSearch;

/**
 * Class ProviderOrderController
 * @package frontend\controllers
 */
class ProviderOrderController extends FrontendController
{
    /**
     * List orders for logged in provider
     * @return string
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'My Patients'))
            ->setDescription('THERA Connect List My Patients.')
            ->setKeywords('THERA Connect,list,patients')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $searchModel = new OrderSearch();
        $searchModel->provider_id =  Yii::$app->user->id;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if(!empty($dataProvider->models)) {
            $models = [];
            foreach ($dataProvider->models as $model) {
                if (Yii::$app->user->can('manageOrder', ['model' => $model])) {
                    $models[] = $model;
                }
            }
            $dataProvider->setModels($models);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id Order ID
     * @return mixed
     */
    public function actionView($id)
    {
        try {
            $model = $this->findModel($id);

            Yii::$app->meta
                ->setTitle(Yii::t('app', '{name} - View', ['name' => $model->patient_name]))
                ->setDescription("THERA Connect View Service Request.")
                ->setKeywords('THERA Connect,view,Service,Request')
                ->setImage(ConstHelper::getImgPath())
                ->register(Yii::$app->getView());

        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('view', [
                'model' => $model,
            ]);
        }

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param int $id Order ID
     * @return mixed
     */
    public function actionAllowProviderTransfer($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Transfer {name} to a new therapist', ['name' => $model->patient_name]))
            ->setDescription("THERA Connect Transfer to a new therapist.")
            ->setKeywords('THERA Connect,view,transfer,new therapist')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        try {
            if($model->load(Yii::$app->request->post())) {

                if (!$model->allowProviderTransfer()) {
                    throw new OrderException('Therapist transfer is failed.');
                }

                Yii::$app->session->addFlash('success', Yii::t('app', 'Therapist transfer allowed successfully.'));
                return $this->redirect(['index']);
            }

        } catch (Exception $e) {
            Yii::error(sprintf('Order: #%s failed to set allow transfer to another provider by provider %s.  Error: %s', $model->id, Yii::$app->user->id, $e->getMessage()), 'ProviderOrder::'.__FUNCTION__);
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['index']);
        }

        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('allow-transfer', [
                'model' => $model,
            ]);
        }

        return $this->render('allow-transfer', [
            'model' => $model,
        ]);
    }

    /**
     * Returns order document resource to download for provider
     * @param int $id
     * @return Response|void
     * @throws NotFoundHttpException
     * @throws ExitException
     */
    public function actionDocument(int $id)
    {
        if (($model = OrderDocument::find()->joinWith(['order', 'userOrder'])->where(['[[order_document.id]]' => $id, '[[user_order.user_id]]' => Yii::$app->user->id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'The requested document not found.'));
        }

        if(is_file(Yii::getAlias($model->file_content_uri)) && is_readable(Yii::getAlias($model->file_content_uri))) {
            $file_name = Yii::getAlias($model->file_content_uri);
        } elseif (is_resource($model->file_content)){
            $file_name = tempnam('/tmp', 'order_doc_');
            file_put_contents($file_name, $model->file_content);
        } else {
            Yii::$app->session->setFlash('error', 'Document not found to download');
            return $this->redirect(Yii::$app->request->referrer );
        }

        Yii::$app->response->sendFile($file_name, $model->file_name);
        Yii::$app->end();
    }

    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = Order::find()->joinWith('orderUsers')->where(['id' => $id, 'user_order.user_id' => Yii::$app->user->id])->one()) === null) {
            throw new NotFoundHttpException(Yii::t('app', 'Service Request not found.'));
        }
        return $model;
    }

}
