<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Visit fixture.
 */
class VisitFixture extends PostgresActiveFixture
{
    public $tableName = 'visit';
    public $modelClass = 'common\models\Visit';
    public $dataFile = '@console/fixtures/data/visit.php';
    public $depends = [
        'console\fixtures\OrderFixture'
    ];
}
