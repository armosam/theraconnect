<?php

namespace common\models\base;

use Yii;
use DateInterval;
use yii\db\Expression;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\User;
use common\models\Order;
use common\models\UserOrder;
use common\models\NoteEval;
use common\helpers\ConstHelper;
use common\models\NoteProgress;
use common\models\NoteRouteSheet;
use common\models\NoteSupplemental;
use common\models\NoteCommunication;
use common\models\NoteDischargeOrder;
use common\models\NoteDischargeSummary;
use common\models\queries\OrderQuery;
use common\models\queries\VisitQuery;
use common\models\queries\NoteEvalQuery;
use common\models\queries\NoteProgressQuery;
use common\models\queries\NoteRouteSheetQuery;
use common\models\queries\NoteSupplementalQuery;
use common\models\queries\NoteCommunicationQuery;
use common\models\queries\NoteDischargeOrderQuery;
use common\models\queries\NoteDischargeSummaryQuery;

/**
 * This is the model class for table "{{%visit}}".
 *
 * @property int $id
 * @property int $order_id
 * @property string|null $visited_at
 * @property string|null $comment
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 *
 * @property Order $order
 * @property UserOrder $visitProvider
 * @property User $createdBy
 * @property User $updatedBy
 * @property NoteEval $evalNote
 * @property NoteProgress $progressNote
 * @property NoteRouteSheet $routeSheetNote
 * @property NoteSupplemental $supplementalNote
 * @property NoteCommunication $communicationNote
 * @property NoteDischargeOrder $dischargeOrderNote
 * @property NoteDischargeSummary $dischargeSummaryNote
 */
class Visit extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%visit}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'visited_at'], 'required'],
            [['order_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            //[['visited_at'], 'date', 'format'=>'php:Y-m-d H:i:s'],
            [['visited_at'], 'date', 'format'=>'php:Y-m-d H:i:s', 'min' => ConstHelper::dateTime()->add(new DateInterval('P1D'))->format('Y-m-d H:i:s'), 'tooSmall' => '{attribute} could not be passed date.'],
            [['comment'], 'string', 'max' => 255],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Order::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'order_id' => Yii::t('app', 'Service Request'),
            'visited_at' => Yii::t('app', 'Visit Date'),
            'comment' => Yii::t('app', 'Comment'),
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
            /*'visited_at' => [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => 'visited_at',
                    ActiveRecord::EVENT_BEFORE_INSERT => 'visited_at',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'visited_at',
                ],
                'value' => function ($event) {
                    $format = 'Y-m-d H:i:s';
                    if ($event->name === ActiveRecord::EVENT_AFTER_FIND) {
                        $format = 'm/d/Y H:i:s';
                    }
                    return ($this->visited_at && ConstHelper::dateTime($this->visited_at)) ? ConstHelper::dateTime($this->visited_at)->format($format) : null;
                },
            ],*/
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Order::class, ['id' => 'order_id'])->inverseOf('visits');
    }

    /**
     * Gets query for [[userOrder]].
     *
     * @return ActiveQuery|OrderQuery
     */
    public function getVisitProvider()
    {
        return $this->hasOne(UserOrder::class, ['order_id' => 'order_id'])->inverseOf('visits');
    }

    /**
     * Gets query for [[NoteCommunication]].
     *
     * @return ActiveQuery|NoteCommunicationQuery
     */
    public function getCommunicationNote()
    {
        return $this->hasOne(NoteCommunication::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Gets query for [[NoteDischargeOrder]].
     *
     * @return ActiveQuery|NoteDischargeOrderQuery
     */
    public function getDischargeOrderNote()
    {
        return $this->hasOne(NoteDischargeOrder::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Gets query for [[NoteDischargeSummary]].
     *
     * @return ActiveQuery|NoteDischargeSummaryQuery
     */
    public function getDischargeSummaryNote()
    {
        return $this->hasOne(NoteDischargeSummary::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Gets query for [[NoteEval]].
     *
     * @return ActiveQuery|NoteEvalQuery
     */
    public function getEvalNote()
    {
        return $this->hasOne(NoteEval::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Gets query for [[NoteProgress]].
     *
     * @return ActiveQuery|NoteProgressQuery
     */
    public function getProgressNote()
    {
        return $this->hasOne(NoteProgress::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Gets query for [[NoteRouteSheet]].
     *
     * @return ActiveQuery|NoteRouteSheetQuery
     */
    public function getRouteSheetNote()
    {
        return $this->hasOne(NoteRouteSheet::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Gets query for [[NoteSupplemental]].
     *
     * @return ActiveQuery|NoteSupplementalQuery
     */
    public function getSupplementalNote()
    {
        return $this->hasOne(NoteSupplemental::class, ['visit_id' => 'id', 'order_id' => 'order_id'])->inverseOf('visit');
    }

    /**
     * Relation with [[user]]
     *
     * @return ActiveQuery|User
     */
    public function getCreatedBy(){
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Relation with [[user]]
     *
     * @return ActiveQuery|User
     */
    public function getUpdatedBy(){
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return VisitQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new VisitQuery(get_called_class());
    }
}
