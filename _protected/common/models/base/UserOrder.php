<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Order;
use common\models\Visit;
use common\helpers\ConstHelper;
use common\models\queries\OrderQuery;
use common\models\queries\UserOrderQuery;
use common\models\queries\UserQuery;

/**
 * This is the model class for table "{{%user_order}}".
 *
 * @property int $user_id
 * @property int $order_id
 * @property string $created_at
 * @property string $status
 *
 * @property Order $order
 * @property Visit[] $visits
 * @property User $user
 */
class UserOrder extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_order}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'order_id'], 'required'],
            [['user_id', 'order_id'], 'integer'],
            [['created_at'], 'safe'],
            ['status', 'in', 'range' => [ConstHelper::STATUS_ACTIVE,ConstHelper::STATUS_PASSIVE,ConstHelper::STATUS_DELETED]],
            [['user_id', 'order_id'], 'unique', 'targetAttribute' => ['user_id', 'order_id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'order_id' => Yii::t('app', 'Order ID'),
            'created_at' => Yii::t('app', 'Assigned At'),
            'status' => Yii::t('app', 'Is Active'),
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('orderUsers');
    }

    /**
     * Gets query for [[Visit]].
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::class, ['order_id' => 'order_id'])->inverseOf('visitProvider');
    }

    /**
     * Gets query for [[User]].
     *
     * @return ActiveQuery|UserQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userOrders');
    }

    /**
     * {@inheritdoc}
     * @return UserOrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserOrderQuery(get_called_class());
    }
}
