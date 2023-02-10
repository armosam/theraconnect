<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteEval;

/**
 * This is the ActiveQuery class for [[\common\models\NoteEval]].
 *
 * @see \common\models\NoteEval
 */
class NoteEvalQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteEval[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteEval|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
