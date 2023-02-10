<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\Patient;

/**
 * Patient fixture.
 */
class PatientFixture extends PostgresActiveFixture
{
    public $tableName = 'patient';
    public $modelClass = Patient::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/patient.php';
    public $depends = [
        //'tests\codeception\common\fixtures\RoleFixture',
    ];
}
