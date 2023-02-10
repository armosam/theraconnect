<?php

namespace common\models\forms;

use Yii;
use common\models\Order;
use common\helpers\ArrayHelper;

/**
 * Class ApproveFrequencyForm
 * @package common\models\forms
 */
class ApproveFrequencyForm extends Order
{
    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return arrayhelper::merge(parent::rules(), [
            [['service_frequency', 'frequency_status'], 'required', 'on' => self::ORDER_SCENARIO_APPROVE_FREQUENCY]
        ]);
    }
}