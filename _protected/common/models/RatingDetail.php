<?php

namespace common\models;

use common\helpers\ConstHelper;

/**
 * Class RatingDetail
 * @package common\models
 */
class RatingDetail extends base\RatingDetail
{
    /**
     * Setting some attributes automatically before insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {
            if($insert){

            } else {
                $this->setAttributes([
                    'status' => ConstHelper::STATUS_PASSIVE
                ]);
            }
            return true;
        } else {
            return false;
        }
    }
}
