<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Order fixture.
 */
class OrderFixture extends PostgresActiveFixture
{
    public $tableName = 'order';
    public $modelClass = 'common\models\Order';
    public $dataFile = '@console/fixtures/data/order.php';
    public $depends = [
        'console\fixtures\PatientFixture'
    ];
}
