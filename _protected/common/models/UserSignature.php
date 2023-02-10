<?php

namespace common\models;

use Yii;

/**
 * Class UserSignature
 * @package common\models
 */
class UserSignature extends base\UserSignature
{
    /**
     * @param int $user_id Logged User ID
     * @param string $signature_data Signature Data in Base30 encryption
     * @return bool
     */
    public static function saveSignature($user_id, $signature_data)
    {
        if(empty($user_id) || empty($signature_data)) {
            return false;
        }
        UserSignature::deleteAll(['user_id' => $user_id]);
        $userSignature = new UserSignature();
        $userSignature->setAttributes(['user_id' => $user_id, 'signature' => $signature_data]);
        return $userSignature->save();
    }

    /**
     * Returns User Signature data
     * @param int $user_id User ID
     * @return string|null
     */
    public static function getSignature($user_id)
    {
        $userSignature = UserSignature::findOne(['user_id' => $user_id]);
        return empty($userSignature->signature) ? null : $userSignature->signature;
    }

}
