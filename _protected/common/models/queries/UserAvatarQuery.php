<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\UserAvatar;
use common\helpers\ConstHelper;

/**
 * This is the ActiveQuery class for [[\common\models\UserAvatar]].
 *
 * @see \common\models\UserAvatar
 */
class UserAvatarQuery extends ActiveQuery
{
    public function active($state = true)
    {
        $status = ($state===true) ? ConstHelper::STATUS_ACTIVE : ConstHelper::STATUS_PASSIVE;
        return $this->andWhere(['[[user_avatar.status]]' => $status]);
    }

    /**
     * @inheritdoc
     * @return UserAvatar|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return UserAvatar[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
}
