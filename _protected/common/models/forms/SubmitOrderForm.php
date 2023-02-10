<?php

namespace common\models\forms;

use Yii;
use common\models\Order;
use common\helpers\ArrayHelper;

/**
 * SubmitOrderForm is the model.
 */
class SubmitOrderForm extends Order
{
    /** @var int $rpt_provider_id */
    public $rpt_provider_id;

    /** @var int $pta_provider_id */
    public $pta_provider_id;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return arrayhelper::merge(parent::rules(), [
            [['rpt_provider_id', 'pta_provider_id'], 'integer'],
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
            'rpt_provider_id' => Yii::t('app', 'RPT Therapist Name'),
            'pta_provider_id' => Yii::t('app', 'PTA Therapist Name'),
        ]);
    }
}
