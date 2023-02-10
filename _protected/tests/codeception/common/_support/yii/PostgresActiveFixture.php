<?php

namespace tests\codeception\common\_support\yii;

use yii\base\NotSupportedException;
use yii\test\ActiveFixture;

class PostgresActiveFixture extends ActiveFixture
{
    /**
     * Runs after fixture load and resets sequence for postgres database
     * @throws NotSupportedException
     */
    public function load()
    {
        parent::load();

        if ($this->db->driverName === 'pgsql' && !empty($this->tableName)) {
            if (!empty($this->tableSchema->sequenceName)){
                $this->db->createCommand()->executeResetSequence($this->tableName);
            }
        }
    }
}