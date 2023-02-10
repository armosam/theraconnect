<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\PatientDocument;

/**
 * PatientDocument fixture.
 */
class PatientDocumentFixture extends PostgresActiveFixture
{
    public $tableName = 'patient_document';
    public $modelClass = PatientDocument::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/patient_document.php';
    public $depends = ['tests\codeception\common\fixtures\PatientFixture'];
}
