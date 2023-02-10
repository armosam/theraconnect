<?php

namespace common\models\base;

use Yii;
use Exception;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\queries\UserQuery;
use common\widgets\signature\Signature;
use common\models\queries\UserSignatureQuery;

/**
 * This is the model class for table "{{%user_signature}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string|null $signature
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $status
 *
 * @property User $user
 */
class UserSignature extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_signature}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'created_by', 'updated_by'], 'integer'],
            [['signature'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['status'], 'in', 'range' => [ConstHelper::STATUS_ACTIVE, ConstHelper::STATUS_PASSIVE, ConstHelper::STATUS_DELETED]],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'signature' => Yii::t('app', 'Signature'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
        ];
    }

    /**
     * Special behaviors
     * @return array THis is an array of behaviors
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
                'skipUpdateOnClean' => false
            ],
            'integer' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
                'defaultValue' => User::USER_SYSTEM_ADMIN_ID,
                'skipUpdateOnClean' => false
            ],
            /*'signature' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'signature',
                ],
                'value' => function ($event) {
                    try {
                        $svgSignature = null;
                        if(empty($this->signature)) {
                            throw new Exception('No User Signature to fetch');
                        }
                        $svgSignature = Signature::getSignatureService()->base30ToSVG($this->signature) ?: null;

                    } catch (Exception $e) {
                        $svgSignature = null;
                    }
                    return $svgSignature;
                },
            ],*/
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return UserSignatureQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserSignatureQuery(get_called_class());
    }
}
