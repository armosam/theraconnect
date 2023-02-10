<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Prospect fixture.
 */
class ProspectFixture extends PostgresActiveFixture
{
    public $tableName = 'prospect';
    public $modelClass = 'common\models\Prospect';
    public $dataFile = '@console/fixtures/data/prospect.php';
    public $depends = [
        //'console\fixtures\UserFixture',
    ];
}
