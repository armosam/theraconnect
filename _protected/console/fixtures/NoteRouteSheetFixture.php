<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteRouteSheet fixture.
 */
class NoteRouteSheetFixture extends PostgresActiveFixture
{
    public $tableName = 'note_route_sheet';
    public $modelClass = 'common\models\NoteRouteSheet';
    public $dataFile = '@console/fixtures/data/note_route_sheet.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
