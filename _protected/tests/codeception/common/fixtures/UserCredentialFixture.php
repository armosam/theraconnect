<?php

namespace tests\codeception\common\fixtures;

use common\models\UserCredential;
use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * UserCredential fixture.
 */
class UserCredentialFixture extends PostgresActiveFixture
{
    public $tableName = 'user_credential';
    public $modelClass = UserCredential::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/user_credential.php';
    public $depends = ['tests\codeception\common\fixtures\CredentialTypeFixture'];
}
