<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteEval fixture.
 */
class NoteEvalFixture extends PostgresActiveFixture
{
    public $tableName = 'note_eval';
    public $modelClass = 'common\models\NoteEval';
    public $dataFile = '@console/fixtures/data/note_eval.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
