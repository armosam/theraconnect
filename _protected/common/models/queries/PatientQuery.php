<?php

namespace common\models\queries;

use common\models\Patient;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Patient]].
 *
 * @see \common\models\Patient
 */
class PatientQuery extends ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Patient[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Patient|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
