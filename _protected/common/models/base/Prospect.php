<?php

namespace common\models\base;

use Yii;
use DateInterval;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\Service;
use common\helpers\ConstHelper;
use common\models\queries\UserQuery;
use common\models\queries\ProspectQuery;

/**
 * This is the model class for table "{{%prospect}}".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property int $service_id
 * @property string $phone_number
 * @property string|null $address
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip_code
 * @property string|null $country
 * @property string $license_type
 * @property string $license_number
 * @property string $license_expiration_date
 * @property string|null $language
 * @property string|null $covered_county
 * @property string|null $covered_city
 * @property string|null $ip_address
 * @property string $timezone
 * @property string|null $lat
 * @property string|null $lng
 * @property string|null $note
 * @property int|null $accepted_by
 * @property string|null $accepted_at
 * @property int|null $rejected_by
 * @property string|null $rejected_at
 * @property string|null $rejection_reason
 * @property string $status
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property User $acceptedBy
 * @property User $rejectedBy
 * @property User $updatedBy
 * @property Service $service
 */
class Prospect extends ActiveRecord
{
    public const PROSPECTIVE_STATUS_PENDING = 'P';
    public const PROSPECTIVE_STATUS_ACCEPTED = 'A';
    public const PROSPECTIVE_STATUS_REJECTED = 'R';

    public const PERSPECTIVE_REJECTION_REASON_LOCATION = 'not_supported_location';
    public const PERSPECTIVE_REJECTION_REASON_FAKE = 'fake_application';
    public const PERSPECTIVE_REJECTION_REASON_NOT_DOCUMENTED = 'not_documented';

    public const SCENARIO_CREATE = 'create';
    public const SCENARIO_ACCEPT = 'accept';
    public const SCENARIO_REJECT = 'reject';

    public $agreed;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%prospect}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'service_id', 'phone_number', 'license_type', 'license_number', 'license_expiration_date'], 'required'],

            ['email', 'email', 'message' => Yii::t('app', 'The email address {value} is incorrect.')],
            ['email', 'unique', 'message' => Yii::t('app', 'The email address {value} has already been used.')],
            ['email', 'unique', 'targetClass' => User::class, 'targetAttribute' => ['email'], 'message' => Yii::t('app', 'The email address {value} has already been used.')],
            ['license_number', 'unique', 'message' => Yii::t('app', 'The license number {value} has already been provided by another account.')],
            [['first_name', 'last_name', 'email', 'address', 'city', 'country', 'license_type', 'license_number', 'timezone', 'note', 'rejection_reason'], 'string', 'max' => 255],
            [['phone_number', 'state', 'zip_code', 'ip_address'], 'string', 'max' => 15],
            [['service_id', 'created_by', 'updated_by', 'accepted_by', 'rejected_by'], 'integer'],
            [['accepted_at', 'rejected_at'], 'safe'],
            [['language', 'covered_county', 'covered_city'], 'each', 'rule' => ['string', 'max' => 20]],
            [['rejected_by', 'accepted_by'], 'default', 'value' => null],
            ['ip_address', 'ip'],
            [['lat', 'lng'], 'number'],
            [['status'], 'in', 'range' => [self::PROSPECTIVE_STATUS_PENDING, self::PROSPECTIVE_STATUS_REJECTED, self::PROSPECTIVE_STATUS_ACCEPTED]],
            [['license_expiration_date'], 'date', 'format'=>'php:m/d/Y', 'min' => ConstHelper::dateTime()->add(new DateInterval('P2M'))->format('m/d/Y'), 'tooSmall' => '{attribute} should not be in 2 months or before {min}.'],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::class, 'targetAttribute' => ['service_id' => 'id']],

            ['agreed', 'required', 'on' => self::SCENARIO_CREATE, 'requiredValue' => 1, 'message' => Yii::t('app', 'You are requested to agree with Terms of Service and Privacy Policy')],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'email' => Yii::t('app', 'Email Address'),
            'service_id' => Yii::t('app', 'Providing Service'),
            'phone_number' => Yii::t('app', 'Phone Number'),
            'address' => Yii::t('app', 'Street Address'),
            'city' => Yii::t('app', 'City'),
            'state' => Yii::t('app', 'State'),
            'zip_code' => Yii::t('app', 'Zip Code'),
            'country' => Yii::t('app', 'Country'),
            'license_type' => Yii::t('app', 'License Type'),
            'license_number' => Yii::t('app', 'License Number'),
            'license_expiration_date' => Yii::t('app', 'Expiration Date'),
            'language' => Yii::t('app', 'Speaking Languages'),
            'covered_county' => Yii::t('app', 'Covered County'),
            'covered_city' => Yii::t('app', 'Covered City'),
            'ip_address' => Yii::t('app', 'Ip Address'),
            'timezone' => Yii::t('app', 'Time Zone'),
            'lat' => Yii::t('app', 'Latitude'),
            'lng' => Yii::t('app', 'Longitude'),
            'note' => Yii::t('app', 'Note'),
            'accepted_by' => Yii::t('app', 'Accepted By'),
            'accepted_at' => Yii::t('app', 'Accepted At'),
            'rejected_by' => Yii::t('app', 'Rejected By'),
            'rejected_at' => Yii::t('app', 'Rejected At'),
            'rejection_reason' => Yii::t('app', 'Rejection Reason'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
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
            'license_expiration_date' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'license_expiration_date',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'license_expiration_date',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'license_expiration_date',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y';
                    }
                    return ($this->license_expiration_date && ConstHelper::dateTime($this->license_expiration_date)) ? ConstHelper::dateTime($this->license_expiration_date)->format($format) : null;
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
            ]
        ];
    }

    /**
     * Relation with Service model via junction relation userService
     *
     * @return ActiveQuery|Service
     */
    public function getService() {
        return $this->hasOne(Service::class, ['id' => 'service_id'])->inverseOf('prospects');
    }

    /**
     * Get model of accepted user
     *
     * @return ActiveQuery|UserQuery
     */
    public function getAcceptedBy()
    {
        return $this->hasOne(User::class, ['id' => 'accepted_by']);
    }

    /**
     * Get model of rejected user
     *
     * @return ActiveQuery|UserQuery
     */
    public function getRejectedBy()
    {
        return $this->hasOne(User::class, ['id' => 'rejected_by']);
    }

    /**
     * Get model of updated user
     *
     * @return ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return ProspectQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ProspectQuery(get_called_class());
    }
}
