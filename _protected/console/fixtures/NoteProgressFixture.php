<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteProgress fixture.
 */
class NoteProgressFixture extends PostgresActiveFixture
{
    public $tableName = 'note_progress';
    public $modelClass = 'common\models\NoteProgress';
    public $dataFile = '@console/fixtures/data/note_progress.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
