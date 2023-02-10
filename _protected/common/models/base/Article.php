<?php

namespace common\models\base;

use Yii;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\traits\ImageCropTrait;
use common\models\queries\ArticleQuery;

/**
 * This is the model class for table "{{%article}}".
 *
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $summary
 * @property string $content
 * @property string $embed_content
 * @property string $start_date
 * @property string $end_date
 * @property int $category
 * @property int $status
 * @property string $created_by
 * @property string $created_at
 * @property string $updated_by
 * @property string $updated_at
 * @property string $email_sent_by
 * @property string $email_sent_at
 *
 * @property ActiveQuery $createdByUser
 * @property ActiveQuery $emailSentBy
 * @property ActiveQuery $updatedBy
 * @property User $user
 */
class Article extends ActiveRecord
{
    use ImageCropTrait;

    public const DONT_SEND_EMAIL = 0;
    public const SEND_EMAIL = 1;

    public const STATUS_DRAFT = 'D';
    public const STATUS_PUBLISHED = 'P';

    public const CATEGORY_NEWS = 'N';
    public const CATEGORY_SOCIETY = 'S';
    public const CATEGORY_DISCOUNT = 'D';

    /** EVENTS */
    public const EVENT_NEWS_CREATED = 'news_created';

    /** NOTIFICATIONS */
    public const NOTIFICATION_NEWS_CREATED = 'newsCreated';

    /**
     * Temporary attribute for storing an uploaded file
     * @var ArticleFile
     */
    public $upload_file;

    /**
     * if true email should be sent to users
     * @var bool
     */
    public $send_email;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'title', 'summary', 'content', 'embed_content', 'status'], 'required'],
            [['user_id', 'send_email', 'created_by', 'updated_by'], 'integer'],
            [['title', 'summary', 'content', 'embed_content', 'start_date', 'end_date'], 'trim'],
            [['title', 'summary', 'content', 'embed_content', 'start_date', 'end_date'], 'string'],
            [['status', 'category', ], 'string', 'max' => 1],
            [['title_en','title_ru', 'title_hy'], 'string', 'max' => 255],
            [['email_sent_by', 'email_sent_at'], 'safe'],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, bmp, gif, jpeg'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'Author'),
            'title' => Yii::t('app', 'Title'),
            'summary' => Yii::t('app', 'Summary'),
            'content' => Yii::t('app', 'Content'),
            'embed_content' => Yii::t('app', 'Embedded Content'),
            'start_date' => Yii::t('app', 'Start Date'),
            'end_date' => Yii::t('app', 'End Date'),
            'category' => Yii::t('app', 'Category'),
            'status' => Yii::t('app', 'Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'email_sent_by' => Yii::t('app', 'Email Sent By'),
            'email_sent_at' => Yii::t('app', 'Email Sent At'),
            'send_email' => Yii::t('app', 'Send Email News'),
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
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
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
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * Get order created user
     * Note: Because in the child class Article already used createdBy we used createdByUser here.
     *
     * @return ActiveQuery
     */
    public function getCreatedByUser()
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
     * Get order created user
     *
     * @return ActiveQuery
     */
    public function getEmailSentBy()
    {
        return $this->hasOne(User::class, ['id' => 'email_sent_by']);
    }

    /**
     * @inheritdoc
     * @return ArticleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ArticleQuery(get_called_class());
    }
}
