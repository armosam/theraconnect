<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * UserOrder fixture.
 */
class UserOrderFixture extends PostgresActiveFixture
{
    public $tableName = 'user_order';
    public $modelClass = 'common\models\UserOrder';
    public $dataFile = '@console/fixtures/data/user_order.php';
    public $depends = [
        'console\fixtures\UserFixture',
        'console\fixtures\OrderFixture'
    ];
}
