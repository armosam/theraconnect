<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\rbac\models\Role;

/**
 * Role fixture
 */
class RoleFixture extends PostgresActiveFixture
{
    public $tableName = 'auth_assignment';
    public $modelClass = Role::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/auth_assignment.php';
    /*public $depends = [
        'tests\codeception\common\fixtures\UserFixture'
    ];*/
}