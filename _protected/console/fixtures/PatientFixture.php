<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Patient fixture.
 */
class PatientFixture extends PostgresActiveFixture
{
    public $tableName = 'patient';
    public $modelClass = 'common\models\Patient';
    public $dataFile = '@console/fixtures/data/patient.php';
}
