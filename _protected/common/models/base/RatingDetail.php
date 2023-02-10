<?php

namespace common\models\base;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use common\models\User;
use common\models\Order;
use common\models\UserRating;
use common\models\queries\RatingDetailQuery;

/**
 * This is the model class for table "{{%rating_detail}}".
 *
 * @property int $id
 * @property string $access_token
 * @property int $user_id
 * @property int $patient_id
 * @property int $review_rate
 * @property string $review_content
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 * @property int $status
 *
 * @property UserRating $userRating
 * @property Order $orderByProviderRatingDetail
 * @property Order $orderByCustomerRatingDetail
 */
class RatingDetail extends ActiveRecord
{
    public const SCENARIO_DEFAULT = 'default';
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';
    public const SCENARIO_REVIEW = 'review';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%rating_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'patient_id'], 'required'],
            [['access_token', 'review_rate'], 'required', 'on' => self::SCENARIO_REVIEW],
            [['access_token'], 'unique'],
            [['user_id', 'patient_id', 'review_rate', 'created_by', 'updated_by', 'status'], 'integer'],
            [['review_rate'], 'integer', 'min' => 1, 'max' => 5],
            [['created_at', 'updated_at'], 'safe'],
            [['access_token'], 'string', 'max' => 40],
            [['review_content'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => UserRating::class, 'targetAttribute' => ['user_id' => 'user_id']],
            [['user_id', 'patient_id'], 'unique', 'targetAttribute' => ['user_id', 'patient_id'], 'message' => Yii::t('app', 'The combination of User ID and Patient ID has already been taken.')],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'access_token' => Yii::t('app', 'Access Token'),
            'user_id' => Yii::t('app', 'User ID'),
            'review_rate' => Yii::t('app', 'Review Rate'),
            'review_content' => Yii::t('app', 'Comments'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
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
                'skipUpdateOnClean' => false
            ],
            'integer' => [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'created_by',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
                'defaultValue' => User::USER_SYSTEM_ADMIN_ID,
                'skipUpdateOnClean' => false
            ],
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUserRating()
    {
        return $this->hasOne(UserRating::class, ['user_id' => 'user_id'])->inverseOf('ratingDetails');
    }

//    /**
//     * @return ActiveQuery
//     */
//    public function getOrderByProviderRatingDetail()
//    {
//        return $this->hasOne(Patient::class, ['id' => 'patient_id', 'provider_id' => 'user_id'])->inverseOf('providerRatingDetail');
//    }
//
//    /**
//     * @return ActiveQuery
//     */
//    public function getOrderByCustomerRatingDetail()
//    {
//        return $this->hasOne(Patient::class, ['id' => 'patient_id', 'customer_id' => 'user_id'])->inverseOf('customerRatingDetail');
//    }

    /**
     * @inheritdoc
     * @return RatingDetailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new RatingDetailQuery(get_called_class());
    }
}
