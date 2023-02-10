<?php

namespace common\models;

use common\helpers\ConstHelper;

/**
 * Class CredentialType
 * @package common\models
 */
class CredentialType extends base\CredentialType
{
    /**
     * Setting some attributes automatically after an insert or update of the table
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

    /**
     * Disables credential type
     */
    public function disable()
    {
        $this->setAttribute('status', ConstHelper::STATUS_PASSIVE);
        $this->save(false);
    }

    /**
     * Enables credential type
     */
    public function enable()
    {
        $this->setAttribute('status', ConstHelper::STATUS_ACTIVE);
        $this->save(false);
    }
}
