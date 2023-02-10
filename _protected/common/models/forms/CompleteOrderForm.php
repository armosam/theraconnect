<?php

namespace common\models\forms;

use Yii;
use common\models\Order;
use common\helpers\ArrayHelper;

/**
 * Class CompleteOrderForm
 * @package common\models\forms
 */
class CompleteOrderForm extends Order
{
    /** @var int $provider_id */
    public $provider_id;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return arrayhelper::merge(parent::rules(), [
            [['provider_id'], 'integer'],
        ]);
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'provider_id' => Yii::t('app', 'Therapist Name'),
        ]);
    }
}