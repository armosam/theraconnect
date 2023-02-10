<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\LoginAttempt;

/**
 * This is the ActiveQuery class for [[\common\models\LoginAttempt]].
 * @see \common\models\LoginAttempt
 */
class LoginAttemptQuery extends ActiveQuery
{
    public function byIP($ip)
    {
        $this->andWhere(['ip' => $ip]);
        return $this;
    }

    /**
     * @inheritdoc
     * @return LoginAttempt|array|null 
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }
}
