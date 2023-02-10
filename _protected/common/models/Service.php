<?php

namespace common\models;

use Yii;
use common\helpers\ArrayHelper;
use common\helpers\ConstHelper;

/**
 * Class Service
 * @package common\models
 */
class Service extends base\Service
{

    /**
     * Return Service list for drop downs
     * @return array
     */
    public static function serviceList()
    {
        return ArrayHelper::map(self::find()->active(true)->all(), 'id', 'service_name');
    }

    /**
     * Disables service
     */
    public function disable()
    {
        $this->setAttribute('status', ConstHelper::STATUS_PASSIVE);
        $this->save(false);
    }

    /**
     * Enables service
     */
    public function enable()
    {
        $this->setAttribute('status', ConstHelper::STATUS_ACTIVE);
        $this->save(false);
    }

    /**
     * Setting some attributes automatically before insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            return true;
        }
        return false;
    }

    /**
     * Setting some attributes automatically after insert or update of the table
     *
     * @param bool $insert
     * @param array $changedAttributes
     * @return void
     */
    public function afterSave($insert, $changedAttributes)
    {
        /*$transaction = Yii::$app->db->beginTransaction();
        try {
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            echo $e->getTraceAsString();
        }*/
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * After finding record it will load translations into particular parameters of model
     */
    public function afterFind()
    {
        parent::afterFind();
    }

}
