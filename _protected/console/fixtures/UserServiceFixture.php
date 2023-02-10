<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * UserService fixture.
 */
class UserServiceFixture extends PostgresActiveFixture
{
    public $tableName = 'user_service';
    public $modelClass = 'common\models\UserService';
    public $dataFile = '@console/fixtures/data/user_service.php';
    public $depends = [
        'console\fixtures\UserFixture',
        'console\fixtures\ServiceFixture'
    ];
}
