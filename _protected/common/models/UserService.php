<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use Exception;

/**
 * Class UserService
 * @package common\models
 */
class UserService extends base\UserService
{
    /**
     * Setting some attributes before insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    /**
     * Updates user service model data
     *
     * @param int $user_id
     * @param array $posted_params
     */
    public static function updateUserService($user_id, $posted_params){
        
        $transaction = Yii::$app->db->beginTransaction();
        
        try {
            
            UserService::deleteAll(['user_id'=>$user_id]);
        
            if (!empty($posted_params['userService'])){
                foreach ($posted_params['userService'] as $service_id){
                    $model = new UserService();
                    $user_service = [
                        'user_id' => $user_id,
                        'service_id' => (int)$service_id,
                    ];
                    $model->setAttributes($user_service);
                    $model->save();
                }
            }
            
            $transaction->commit();
        } catch (Exception $ex) {
            $transaction->rollBack();
            echo $ex;
        }
        
    }
}
