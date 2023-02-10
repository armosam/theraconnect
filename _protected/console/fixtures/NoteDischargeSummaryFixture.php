<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteDischargeSummary fixture.
 */
class NoteDischargeSummaryFixture extends PostgresActiveFixture
{
    public $tableName = 'note_discharge_summary';
    public $modelClass = 'common\models\NoteDischargeSummary';
    public $dataFile = '@console/fixtures/data/note_discharge_summary.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
