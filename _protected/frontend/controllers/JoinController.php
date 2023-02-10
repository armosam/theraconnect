<?php

namespace frontend\controllers;

use Yii;
use Exception;
use yii\base\ErrorException;
use common\models\Prospect;
use common\helpers\ConstHelper;

/**
 * Class JoinController
 * @package frontend\controllers
 */
class JoinController extends FrontendController
{
    public function actionApplicationForm()
    {
        Yii::$app->meta
            ->setTitle(Yii::t('app', 'Application Form'))
            ->setDescription("THERA Connect Fill application form and join us.")
            ->setKeywords('THERA Connect,application,form,join,fill')
            ->setImage(ConstHelper::getImgPath())
            ->register(Yii::$app->getView());

        $model = new Prospect();
        $model->setScenario(Prospect::SCENARIO_CREATE);

        if($model->load(Yii::$app->request->post()) && $model->validate()) {
            try {
                //$model->setAttribute('language', json_encode($model->getAttribute('language')));
                if (!$model->save()) {
                    throw new ErrorException('Your application is failed to save.');
                }

                Yii::$app->session->addFlash('success', Yii::t('app', 'Thank you, your application form submitted successfully.'));
                return $this->redirect(['success', 'q' => base64_encode(time())]);

            } catch (Exception $e) {
                Yii::error(sprintf('Application Form failed to submit:  Error: %s', $e->getMessage()), 'Join::'.__FUNCTION__);
                Yii::$app->session->addFlash('error', Yii::t('app', 'Your application form failed to submit. There are missing information on the application form.'));
            }
        }

        return $this->render('application-form', ['model' => $model]);
    }

    public function actionIndex () {
        return $this->redirect(['join/application-form']);
    }

    public function actionSuccess ()
    {
        $q = Yii::$app->request->get('q', null);
        if(empty($q) || (time() - (int)(base64_decode($q)) > 60)) {
            return $this->redirect(['join/application-form']);
        }
        return $this->render('success');
    }
}
