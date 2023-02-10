<?php

namespace tests\codeception\common\fixtures;

use common\models\CredentialType;
use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Credential Type Fixture.
 */
class CredentialTypeFixture extends PostgresActiveFixture
{
    public $tableName = 'credential_type';
    public $modelClass = CredentialType::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/credential_type.php';
}
