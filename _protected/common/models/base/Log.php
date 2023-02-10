<?php

namespace common\models\base;

use Yii;
use \yii\db\ActiveRecord;

/**
 * This is the model class for table "log".
 *
 * @property int $id
 * @property int $level
 * @property string $category
 * @property double $log_time
 * @property string $prefix
 * @property string $message
 *
 */
class Log extends ActiveRecord
{
    /**
     * Max length of message field, uses in views.
     */
    const MAX_MESSAGE_LENGTH = 200;
    /**
     * Log level error.
     */
    const LEVEL_ERROR = 1;
    /**
     * Log level warning.
     */
    const LEVEL_WARNING = 2;
    /**
     * Log level info.
     */
    const LEVEL_INFO = 4;
    /**
     * Log level trace.
     */
    const LEVEL_TRACE = 8;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'level' => Yii::t('app', 'Level'),
            'category' => Yii::t('app', 'Category'),
            'log_time' => Yii::t('app', 'Log Time'),
            'prefix' => Yii::t('app', 'Prefix'),
            'message' => Yii::t('app', 'Message'),
            'level_label' => Yii::t('app', 'Level'),
        ];
    }
}
