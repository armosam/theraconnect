<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteDischargeOrder fixture.
 */
class NoteDischargeOrderFixture extends PostgresActiveFixture
{
    public $tableName = 'note_discharge_order';
    public $modelClass = 'common\models\NoteDischargeOrder';
    public $dataFile = '@console/fixtures/data/note_discharge_order.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
