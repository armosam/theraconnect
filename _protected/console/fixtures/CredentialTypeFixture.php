<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Credential Type fixture.
 */
class CredentialTypeFixture extends PostgresActiveFixture
{
    public $tableName = 'credential_type';
    public $modelClass = 'common\models\CredentialType';
    public $dataFile = '@console/fixtures/data/credential_type.php';
}
