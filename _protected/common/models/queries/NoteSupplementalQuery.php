<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteSupplemental;

/**
 * This is the ActiveQuery class for [[\common\models\NoteSupplemental]].
 *
 * @see \common\models\NoteSupplemental
 */
class NoteSupplementalQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteSupplemental[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteSupplemental|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
