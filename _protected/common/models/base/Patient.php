<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\Order;
use common\helpers\ConstHelper;
use common\models\queries\UserQuery;
use common\models\queries\OrderQuery;
use common\models\queries\PatientQuery;

/**
 * This is the model class for table "{{%patient}}".
 *
 * @property int $id
 * @property int $customer_id
 * @property string $patient_number
 * @property string $start_of_care
 * @property string $first_name
 * @property string $middle_name
 * @property string $last_name
 * @property string|null $gender
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $country
 * @property string|null $zip_code
 * @property string|null $birth_date
 * @property string|null $ssn
 * @property string|null $phone_number
 * @property string|null $preferred_language
 * @property string|null $preferred_gender
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_number
 * @property string|null $emergency_contact_relationship
 * @property string $status
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $customer
 * @property Order[] $orders
 * @property Order[] $pendingOrders
 * @property Order[] $activeOrders
 * @property User $createdBy
 * @property User $updatedBy
 */
class Patient extends ActiveRecord
{
    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%patient}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['patient_number', 'start_of_care', 'first_name', 'last_name', 'birth_date', 'customer_id', 'emergency_contact_name', 'emergency_contact_number', 'emergency_contact_relationship'], 'required', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_UPDATE]],
            [['created_at', 'updated_at'], 'safe'],
            [['customer_id', 'created_by', 'updated_by'], 'integer'],
            [['first_name', 'middle_name', 'last_name', 'address', 'city', 'state', 'country', 'ssn', 'emergency_contact_name', 'emergency_contact_relationship'], 'string', 'max' => 255],
            [['zip_code', 'phone_number', 'patient_number', 'preferred_language', 'emergency_contact_number'], 'string', 'max' => 15],
            [['preferred_gender', 'gender'], 'string', 'max' => 1],
            [['birth_date', 'start_of_care'], 'date', 'format'=>'php:m/d/Y'],
            ['status', 'in', 'range' => [ConstHelper::STATUS_ACTIVE,ConstHelper::STATUS_PASSIVE,ConstHelper::STATUS_DELETED]],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['customer_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'customer_id' => Yii::t('app', 'Agency Name'),
            'patient_number' => Yii::t('app', 'MR#'),
            'start_of_care' => Yii::t('app', 'Start of Care (SOC)'),
            'first_name' => Yii::t('app', 'First Name'),
            'middle_name' => Yii::t('app', 'Middle Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'gender' => Yii::t('app', 'Gender'),
            'address' => Yii::t('app', 'Street Address'),
            'city' => Yii::t('app', 'City'),
            'state' => Yii::t('app', 'State'),
            'country' => Yii::t('app', 'Country'),
            'zip_code' => Yii::t('app', 'Zip Code'),
            'birth_date' => Yii::t('app', 'Birth Date'),
            'ssn' => Yii::t('app', 'SSN'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'preferred_language' => Yii::t('app', 'Preferred Language'),
            'preferred_gender' => Yii::t('app', 'Preferred Gender'),
            'emergency_contact_name' => Yii::t('app', 'Emergency Contact Name'),
            'emergency_contact_number' => Yii::t('app', 'Emergency Contact Number'),
            'emergency_contact_relationship' => Yii::t('app', 'Contact Relationship'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Returns a list of behaviors that this component behave for
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_by',
                ],
                'defaultValue' => User::USER_SYSTEM_ADMIN_ID,
                'skipUpdateOnClean' => false
            ],
            'birth_date' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'birth_date',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'birth_date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'birth_date',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y';
                    }
                    return ($this->birth_date && ConstHelper::dateTime($this->birth_date)) ? ConstHelper::dateTime($this->birth_date)->format($format) : null;
                },
            ],
            'start_of_care' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'start_of_care',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'start_of_care',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'start_of_care',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y';
                    }
                    return ($this->start_of_care && ConstHelper::dateTime($this->start_of_care)) ? ConstHelper::dateTime($this->start_of_care)->format($format) : null;
                },
            ],
            'phone_number' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'phone_number',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'phone_number',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'phone_number',
                ],
                'value' => function ($event) {
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        return $this->phone_number ? Yii::$app->formatter->asPhone($this->phone_number) : null;
                    }
                    return $this->phone_number ? '+1'.preg_replace('/\D/', '', $this->phone_number) : null;
                },
            ],
            'emergency_contact_number' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'emergency_contact_number',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'emergency_contact_number',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'emergency_contact_number',
                ],
                'value' => function ($event) {
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        return $this->emergency_contact_number ? Yii::$app->formatter->asPhone($this->emergency_contact_number) : null;
                    }
                    return $this->emergency_contact_number ? '+1'.preg_replace('/\D/', '', $this->emergency_contact_number) : null;
                },
            ],
            'ssn' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'ssn',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'ssn',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'ssn',
                ],
                'value' => function ($event) {
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        return !empty($this->ssn) ? base64_decode($this->ssn) : null;
                    }
                    return !empty($this->ssn) ? base64_encode($this->ssn) : null;
                },
            ],
        ];
    }

    /**
     * Relation with User model
     *
     * @return ActiveQuery|UserQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(User::class, ['id' => 'customer_id'])->inverseOf('customerPatients');
    }

    /**
     * Relation with Order model
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::class, ['patient_id' => 'id'])->orderBy(['created_at' => SORT_DESC])->inverseOf('patient');
    }

    /**
     * Relation with Order model with active status
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getActiveOrders()
    {
        return $this->hasMany(Order::class, ['patient_id' => 'id'])->andOnCondition(['order.status' => [Order::ORDER_STATUS_SUBMITTED, Order::ORDER_STATUS_ACCEPTED]])->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Relation with Order model with pending status
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getPendingOrders()
    {
        return $this->hasMany(Order::class, ['patient_id' => 'id'])->andOnCondition(['order.status' => Order::ORDER_STATUS_PENDING])->orderBy(['created_at' => SORT_DESC]);
    }

    /**
     * Get credential created user
     *
     * @return ActiveQuery|UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Get credential updated user
     *
     * @return ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return PatientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PatientQuery(get_called_class());
    }
}
