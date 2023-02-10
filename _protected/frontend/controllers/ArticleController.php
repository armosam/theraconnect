<?php
namespace frontend\controllers;

use Yii;
use yii\db\Expression;
use yii\db\StaleObjectException;
use yii\helpers\ArrayHelper;
use yii\base\ErrorException;
use yii\queue\db\Queue;
use yii\web\JsExpression;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use DateTime;
use DateTimeZone;
use Exception;
use Throwable;
use common\models\User;
use common\models\Article;
use common\helpers\ConstHelper;
use common\models\searches\ArticleSearch;
use common\jobs\ArticleEmailSenderJob;
use common\jobs\ArticleSmsSenderJob;

/**
 * ArticleController implements the CRUD actions for Article model.
 */
class ArticleController extends FrontendController
{
    /**
     * Lists all Article models.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'News'))
            ->setDescription("Articles, News, discounts ant much more.")
            ->setKeywords('THERA Connect,certified,certified specialist,specialist,news,articles,discounts,sale')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        /**
         * How many articles we want to display per page.
         * @var integer
         */
        $pageSize = 2;

        /**
         * Articles have to be published.
         * @var boolean
         */
        /*$published = true;

        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize, $published);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);*/


        $articles = Article::find()
            ->where(['status' => Article::STATUS_PUBLISHED])
            ->andWhere(new JsExpression('start_date IS NULL OR start_date <= current_date'))
            ->andWhere(new JsExpression('end_date IS NULL OR end_date >= current_date'))
            ->orderBy(['start_date' => SORT_DESC])->all();
        $grouped_by_category = ArrayHelper::map($articles,'id', function($item){return $item;}, 'category');

        return $this->render('index', ['articles' => $grouped_by_category]);
    }

    /**
     * Displays a single Article model for admin.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle($model->title)
            ->setDescription($model->summary)
            ->setKeywords(ConstHelper::extractKeyWords($model->summary))
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Article model on the public.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionItem($id)
    {
        $model = $this->findModel($id);
        Yii::$app->meta
            ->setTitle($model->title)
            ->setDescription($model->summary)
            ->setKeywords(ConstHelper::extractKeyWords($model->summary))
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        return $this->render('item', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Article model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionCreate()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Create Article'))
            ->setDescription(Yii::t('app', 'Create Article'))
            ->setKeywords(ConstHelper::extractKeyWords('Create new article, news'))
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new Article();
        $model->setAttribute('user_id', Yii::$app->has('user') ? Yii::$app->user->id : User::USER_SYSTEM_ADMIN_ID);

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();

            try {
                $currentTimeZone = new DateTimeZone(Yii::$app->timeZone);

                if(!empty($model->start_date)) {
                    $start_date = new DateTime($model->start_date, $currentTimeZone);
                    $model->setAttribute('start_date', $start_date->format('Y-m-d'));
                }

                if(!empty($model->end_date)) {
                    $end_date = new DateTime($model->end_date, $currentTimeZone);
                    $model->setAttribute('end_date', $end_date->format('Y-m-d'));
                }

                if (empty($model->user_id)) {
                    throw new ErrorException(Yii::t('app', 'User Id can not be empty.'));
                }

                if ($model->status === Article::STATUS_PUBLISHED && $model->send_email === Article::SEND_EMAIL) {
                    $model->setAttribute('email_sent_at', new Expression('NOW()'));
                    $model->setAttribute('email_sent_by', Yii::$app->getUser()->getId());
                }else{
                    $model->setAttribute('email_sent_at', new Expression('NULL'));
                    $model->setAttribute('email_sent_by', new Expression('NULL'));
                }

                $model->upload_file = UploadedFile::getInstance($model, 'upload_file');

                /*if ( !($model->upload_file = UploadedFile::getInstance($model, 'upload_file')) ) {
                    if(empty($model->file_name) || !is_readable(Yii::getAlias(Yii::$app->params['articleImage']['destination_prefix'].$model->file_name))){
                        $model->addError('upload_file', 'Please upload a file.');
                        return $this->render('update', ['model' => $model]);
                    }
                }*/

                if(!$model->save()){
                    Yii::error('Article '.$model->id.' not saved');
                    throw new ErrorException(Yii::t('app', 'Data Not Saved'));
                }

                if ($model->status === Article::STATUS_PUBLISHED && $model->send_email === Article::SEND_EMAIL) {
                    /** @var Queue $YiiQueueEmail */
                    $YiiQueueEmail = Yii::$app->queueEmail;
                    /** @var Queue $YiiQueueSms */
                    $YiiQueueSms = Yii::$app->queueSms;
                    $users = User::find()->active()->all();
                    foreach ($users as $user) {
                        if ($user->userDetail->note_email_news_and_promotions === 1) {
                            if (is_numeric($user->username) && empty($user->email)){
                                $YiiQueueSms->delay(1 * 60)->push(new ArticleSmsSenderJob([
                                    'user_id' => $user->id,
                                    'article_id' => $model->id
                                ]));
                            }
                            $YiiQueueEmail->delay(1 * 60)->push(new ArticleEmailSenderJob([
                                'user_id' => $user->id,
                                'article_id' => $model->id
                            ]));
                        }
                    }
                }

                $transaction->commit();

                return $this->redirect(['view', 'id' => $model->id]);

            } catch (Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->addFlash('error', $e->getMessage());
                //throw new ExitException(0, $e->getMessage(), $e->getCode());
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Article model.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @param int $id
     * @return string|Response
     * @throws MethodNotAllowedHttpException
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        Yii::$app->meta
            ->setTitle($model->title)
            ->setDescription($model->summary)
            ->setKeywords(ConstHelper::extractKeyWords($model->summary))
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        if (Yii::$app->user->can('manageArticle', ['model' => $model]))
        {
            if ($model->load(Yii::$app->request->post())) {

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    $currentTimeZone = new DateTimeZone(Yii::$app->timeZone);

                    if(isset($model->dirtyAttributes['start_date'])){
                        $start_date = new DateTime($model->start_date, $currentTimeZone);
                        $model->setAttribute('start_date', $start_date->format('Y-m-d'));
                    }

                    if(isset($model->dirtyAttributes['end_date'])){
                        if (!empty($model->end_date)) {
                            $end_date = new DateTime($model->end_date, $currentTimeZone);
                            $model->setAttribute('end_date', $end_date->format('Y-m-d'));
                        }
                    }

                    if ($model->status === Article::STATUS_PUBLISHED && $model->send_email === Article::SEND_EMAIL) {
                        $model->setAttribute('email_sent_at', new Expression('NOW()'));
                        $model->setAttribute('email_sent_by', Yii::$app->getUser()->getId());
                    }else{
                        $model->setAttribute('email_sent_at', new Expression('NULL'));
                        $model->setAttribute('email_sent_by', new Expression('NULL'));
                    }

                    $model->upload_file = UploadedFile::getInstance($model, 'upload_file');

                    /*if ( !($model->upload_file = UploadedFile::getInstance($model, 'upload_file')) ) {
                        if(empty($model->file_name) || !is_readable(Yii::getAlias(Yii::$app->params['articleImage']['destination_prefix'].$model->file_name))){
                            $model->addError('upload_file', 'Please upload a file.');
                            return $this->render('update', ['model' => $model]);
                        }
                    }*/

                    if (!$model->save()) {
                        Yii::error('Article '.$model->id.' not saved');
                        throw new ErrorException(Yii::t('app', 'Data Not Saved'));
                    }

                    if ($model->status === Article::STATUS_PUBLISHED) {
                        /** @var Queue $YiiQueueEmail */
                        $YiiQueueEmail = Yii::$app->queueEmail;
                        $YiiQueueEmail->clear();
                        /** @var Queue $YiiQueueSms */
                        $YiiQueueSms = Yii::$app->queueSms;
                        $YiiQueueSms->clear();
                        if($model->send_email === Article::SEND_EMAIL) {
                            $users = User::find()->active()->all();
                            foreach ($users as $user) {
                                if ($user->userDetail->note_email_news_and_promotions === 1 && !empty($user->email)) {
                                    $YiiQueueEmail->delay(1 * 60)->push(new ArticleEmailSenderJob([
                                        'user_id' => $user->id,
                                        'article_id' => $model->id
                                    ]));
                                }
                                if ($user->userDetail->note_sms_news_and_promotions === 1
                                    && (ConstHelper::isPhoneNumber($user->username) && empty($user->email))
                                ) {
                                    $YiiQueueSms->delay(1 * 60)->push(new ArticleSmsSenderJob([
                                        'user_id' => $user->id,
                                        'article_id' => $model->id
                                    ]));
                                }
                            }
                        }
                    }

                    $transaction->commit();

                    return $this->redirect(['view', 'id' => $model->id]);

                } catch (Exception $e) {
                    $transaction->rollBack();
                    Yii::error(sprintf('%s.%s: There was an issue to update article record: %s', __CLASS__, __METHOD__, $e->getMessage()));
                    Yii::$app->session->addFlash('error', Yii::t('app', $e->getMessage()));
                }
            }

            return $this->render('update', ['model' => $model]);

        } else {
            throw new MethodNotAllowedHttpException(Yii::t('app', 'You are not allowed to access this page.'));
        } 
    }

    /**
     * Deletes an existing Article model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param int $id
     * @return Response
     * @throws NotFoundHttpException
     * @throws Exception
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect('admin');
    }

    /**
     * Manage Articles.
     * 
     * @return mixed
     */
    public function actionAdmin()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'News'))
            ->setDescription("Administration page for Articles, News, Discounts and much more.")
            ->setKeywords(ConstHelper::extractKeyWords("Administration page for Articles, News, Discounts ant much more."))
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        /**
         * How many articles we want to display per page.
         * @var integer
         */
        $pageSize = 11;

        /**
         * Only admin+ roles can see everything.
         * Editors will be able to see only published articles and their own drafts @see: search(). 
         * @var boolean
         */
        $published = (Yii::$app->user->can('admin')) ? false : true ;

        $searchModel = new ArticleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $pageSize, $published);

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Finds the Article model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * 
     * @param integer  $id
     * @return Article The loaded model.
     * 
     * @throws NotFoundHttpException if the model cannot be found.
     */
    protected function findModel($id)
    {
        if (($model = Article::findOne($id)) === null){
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
        return $model;
    }
}
