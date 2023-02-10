<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\models\User;
use common\models\Order;
use common\models\Prospect;
use common\models\UserService;
use common\models\queries\ServiceQuery;
use common\helpers\ConstHelper;

/**
 * This is the model class for table "{{%service}}".
 *
 * @property int $id
 * @property string $service_name
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 * @property int $status
 * @property int $ordering
 *
 * @property UserService[] $userServices
 * @property Prospect[] $prospects
 * @property User[] $users
 * @property User[] $rptUsers
 * @property User[] $ptaUsers
 * @property Order[] $orders
 * @property User $createdBy
 * @property User $updatedBy
 */
class Service extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_name'], 'required'],
            [['service_name'], 'trim'],
            [['service_name'], 'string', 'max' => 200],
            [['created_by', 'updated_by', 'ordering'], 'integer'],
            [['status'], 'in', 'range' => [ConstHelper::STATUS_DELETED, ConstHelper::STATUS_ACTIVE, ConstHelper::STATUS_PASSIVE]],
            [['created_at', 'updated_at'], 'date', 'format'=>'yyyy-MM-dd HH:mm:ssZ'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'service_name' => Yii::t('app', 'Service Name'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'status' => Yii::t('app', 'Status'),
            'ordering' => Yii::t('app', 'Ordering'),
        ];
    }

    /**
     * Special behavior of model translation for configured fields
     * @return array Array of translation configuration
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
        ];
    }

    /**
     * @return ActiveQuery|UserService[]
     */
    public function getUserServices()
    {
        return $this->hasMany(UserService::class, ['service_id' => 'id'])->inverseOf('service');
    }

    /**
     * Relation with users via junction relation userService for RPTs
     *
     * @return ActiveQuery|User[]
     */
    public function getRptUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->andOnCondition(['title' => User::USER_TITLE_RPT])->via('userServices');
    }

    /**
     * Relation with users via junction relation userService for PTA
     *
     * @return ActiveQuery|User[]
     */
    public function getPtaUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->andOnCondition(['title' => User::USER_TITLE_PTA])->via('userServices');
    }

    /**
     * Relation with users via junction relation userService
     *
     * @return ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'id'])->via('userServices')->inverseOf('service');
    }

    /**
     * Relation with prospect relation
     *
     * @return ActiveQuery|Prospect[]
     */
    public function getProspects()
    {
        return $this->hasMany(Prospect::class, ['id' => 'service_id'])->inverseOf('service');
    }

    /**
     * Relation with order model
     *
     * @return ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['service_id' => 'id'])->inverseOf('service');
    }

    /**
     * Get order created user
     *
     * @return ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get order updated user
     *
     * @return ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @inheritdoc
     * @return ServiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ServiceQuery(get_called_class());
    }
}
