<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\Service;

/**
 * Service fixture.
 */
class ServiceFixture extends PostgresActiveFixture
{
    public $tableName = 'service';
    public $modelClass = Service::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/service.php';
}
