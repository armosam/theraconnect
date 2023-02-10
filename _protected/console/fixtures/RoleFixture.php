<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * Role fixture
 */
class RoleFixture extends PostgresActiveFixture
{
    public $tableName = 'auth_assignment';
    public $modelClass = 'common\rbac\models\Role';
    public $dataFile = '@console/fixtures/data/auth_assignment.php';
    public $depends = [
        'console\fixtures\ProspectFixture',
        'console\fixtures\UserCredentialFixture',
        'console\fixtures\UserAvatarFixture',
        'console\fixtures\UserOrderFixture',
        'console\fixtures\OrderDocumentFixture',
        'console\fixtures\UserServiceFixture',
        'console\fixtures\NoteCommunicationFixture',
        'console\fixtures\NoteDischargeOrderFixture',
        'console\fixtures\NoteDischargeSummaryFixture',
        'console\fixtures\NoteEvalFixture',
        'console\fixtures\NoteProgressFixture',
        'console\fixtures\NoteRouteSheetFixture',
        'console\fixtures\NoteSupplementalFixture',
        'console\fixtures\UserRatingFixture',
    ];
}