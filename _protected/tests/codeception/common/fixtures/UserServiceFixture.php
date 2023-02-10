<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\UserService;

/**
 * UserService fixture.
 */
class UserServiceFixture extends PostgresActiveFixture
{
    public $tableName = 'user_service';
    public $modelClass = UserService::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/user_service.php';
}
