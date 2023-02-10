<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteProgress;

/**
 * This is the ActiveQuery class for [[\common\models\NoteProgress]].
 *
 * @see \common\models\NoteProgress
 */
class NoteProgressQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteProgress[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteProgress|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
