<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteSupplemental fixture.
 */
class NoteSupplementalFixture extends PostgresActiveFixture
{
    public $tableName = 'note_supplemental';
    public $modelClass = 'common\models\NoteSupplemental';
    public $dataFile = '@console/fixtures/data/note_supplemental.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
