<?php

namespace common\models\base;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use common\models\User;
use common\models\Service;
use common\models\queries\UserServiceQuery;

/**
 * This is the model class for table "{{%user_service}}".
 *
 * @property int $user_id
 * @property int $service_id
 *
 * @property Service $service
 * @property User $user
 */
class UserService extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'service_id'], 'required'],
            [['user_id', 'service_id'], 'integer'],
            [['user_id', 'service_id'], 'unique', 'targetAttribute' => ['user_id', 'service_id']],
            [['service_id'], 'exist', 'skipOnError' => true, 'targetClass' => Service::class, 'targetAttribute' => ['service_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'Therapist'),
            'service_id' => Yii::t('app', 'Therapist Service')
        ];
    }

    /**
     * Relation with service
     *
     * @return ActiveQuery
     */
    public function getService()
    {
        return $this->hasOne(Service::class, ['id' => 'service_id'])->inverseOf('serviceUsers');
    }

    /**
     * Relation with user
     *
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->inverseOf('userService');
    }

    /**
     * @inheritdoc
     * @return UserServiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserServiceQuery(get_called_class());
    }
}
