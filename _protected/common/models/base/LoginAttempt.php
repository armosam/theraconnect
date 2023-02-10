<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use common\models\queries\LoginAttemptQuery;

/**
 * This is the model class for table "{{%login_attempt}}".
 *
 * @property int $id
 * @property string $ip
 * @property int $failed_attempts
 * @property string $created_at
 * @property string $updated_at
 */
class LoginAttempt extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%login_attempt}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ip'], 'required'],
            [['failed_attempts'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['ip'], 'ip'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'ip' => Yii::t('app', 'Ip'),
            'failed_attempts' => Yii::t('app', 'Failed Attempts'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Returns a list of behaviors that this component should behave as.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     * @return LoginAttemptQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LoginAttemptQuery(get_called_class());
    }
}
