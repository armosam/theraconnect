<?php

namespace frontend\models\forms;

use Yii;
use yii\base\Model;
use common\models\ChangeHistory;

/**
 * Phone Number Verification form.
 * @property string $verification_code
 */
class PhoneVerificationForm extends Model
{
    /**
     * @var string $verification_code
     */
    public $verification_code;

    /**
     * Returns the validation rules for attributes.
     * @return array
     */
    public function rules()
    {
        return [
            [['verification_code'], 'filter', 'filter' => 'trim'],
            [['verification_code'], 'required'],
            [['verification_code'], 'validateCode'],
        ];
    }

    /**
     * Returns the attribute labels.
     *
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'verification_code' => Yii::t('app', 'Verification Code'),
        ];
    }

    /**
     * Validates verification code
     * @param string $attribute
     */
    public function validateCode($attribute)
    {
        if (ChangeHistory::findByVerificationCode(trim($this->$attribute)) === null){
            $this->addError($attribute, Yii::t('app', 'Incorrect Verification Code'));
        }
    }
}
