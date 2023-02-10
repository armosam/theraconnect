<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * NoteCommunication fixture.
 */
class NoteCommunicationFixture extends PostgresActiveFixture
{
    public $tableName = 'note_communication';
    public $modelClass = 'common\models\NoteCommunication';
    public $dataFile = '@console/fixtures/data/note_communication.php';
    public $depends = [
        'console\fixtures\VisitFixture',
        'console\fixtures\OrderFixture',
        'console\fixtures\UserFixture'
    ];
}
