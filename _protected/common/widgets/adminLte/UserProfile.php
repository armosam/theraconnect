<?php

namespace common\widgets\adminLte;

use Yii;
use yii\bootstrap\Widget;
use common\models\User;

/**
 * Class UserProfile
 * @package common\widgets\adminLte
 */
class UserProfile extends Widget
{
    /**
     * @return null|string
     */
    public function run()
    {
        if(!Yii::$app->user->isGuest){
            $model = User::findOne([Yii::$app->user->id]);
            if (!empty($model)){
                return $this->render('user-profile', [
                    'model' => $model
                ]);
            }
        }
        return null;
    }
}