<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\UserAvatar;

/**
 * UserAvatar fixture.
 */
class UserAvatarFixture extends PostgresActiveFixture
{
    public $tableName = 'user_avatar';
    public $modelClass = UserAvatar::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/user_avatar.php';
}
