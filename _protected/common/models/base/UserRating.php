<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\RatingDetail;
use common\models\queries\UserRatingQuery;

/**
 * This is the model class for table "{{%user_rating}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $current_rating
 * @property int $star1
 * @property int $star2
 * @property int $star3
 * @property int $star4
 * @property int $star5
 *
 * @property User $user
 * @property RatingDetail[] $ratingDetails
 * @property Order[] $ordersByProviderRating
 * @property Order[] $ordersByCustomerRating
 */
class UserRating extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_rating}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'star1', 'star2', 'star3', 'star4', 'star5'], 'integer'],
            [['current_rating'], 'number'],
            [['current_rating'], 'default', 'value'=> 0.0],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
            [['current_rating', 'user_id', 'star1', 'star2', 'star3', 'star4', 'star5'], 'safe', 'on' => 'create'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'current_rating' => Yii::t('app', 'Current Rating'),
            'star1' => Yii::t('app', 'Star1'),
            'star2' => Yii::t('app', 'Star2'),
            'star3' => Yii::t('app', 'Star3'),
            'star4' => Yii::t('app', 'Star4'),
            'star5' => Yii::t('app', 'Star5'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userRating');
    }

    /**
     * @return ActiveQuery
     */
    public function getRatingDetails()
    {
        return $this->hasMany(RatingDetail::class, ['user_id' => 'user_id'])->inverseOf('userRating');
    }

//    /**
//     * @return ActiveQuery
//     */
//    public function getOrdersByProviderRating()
//    {
//        return $this->hasMany(Order::class, ['provider_id' => 'user_id'])->via('ratingDetails')->inverseOf('providerRating');
//    }
//
//    /**
//     * @return ActiveQuery
//     */
//    public function getOrdersByCustomerRating()
//    {
//        return $this->hasMany(Order::class, ['customer_id' => 'user_id'])->via('ratingDetails')->inverseOf('customerRating');
//    }

    /**
     * @inheritdoc
     * @return UserRatingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserRatingQuery(get_called_class());
    }
}
