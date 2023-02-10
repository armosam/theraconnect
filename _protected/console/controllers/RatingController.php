<?php

namespace console\controllers;

use \yii\console\Controller;
use Yii;

class RatingController extends Controller
{
    /**
     * This command will close all open requests for user ratings when expired
     * Expired request means older than period set in configuration
     *
     * @return string
     */
    public function actionClose()
    {

        return $this->render('close');
    }

    /**
     * This command will check rating_details table and selectively
     * send notifications to customers and providers to rate each other.
     *
     * @return string
     */
    public function actionRequest()
    {
        return $this->render('request');
    }

}
