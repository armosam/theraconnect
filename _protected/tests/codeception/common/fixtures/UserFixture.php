<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\User;

/**
 * User fixture.
 */
class UserFixture extends PostgresActiveFixture
{
    public $tableName = 'user';
    public $modelClass = User::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/user.php';
}
