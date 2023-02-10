<?php

namespace common\models\queries;

use yii\db\ActiveQuery;
use common\models\NoteRouteSheet;

/**
 * This is the ActiveQuery class for [[\common\models\NoteRouteSheet]].
 *
 * @see \common\models\NoteRouteSheet
 */
class NoteRouteSheetQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoteRouteSheet[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoteRouteSheet|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
