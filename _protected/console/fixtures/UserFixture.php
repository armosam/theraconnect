<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * User fixture.
 */
class UserFixture extends PostgresActiveFixture
{
    public $tableName = 'user';
    public $modelClass = 'common\models\User';
    public $dataFile = '@console/fixtures/data/user.php';
    /*public $depends = [
    ];*/
}
