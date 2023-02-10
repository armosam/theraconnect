<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Service fixture.
 */
class ServiceFixture extends PostgresActiveFixture
{
    public $tableName = 'service';
    public $modelClass = 'common\models\Service';
    public $dataFile = '@console/fixtures/data/service.php';
}
