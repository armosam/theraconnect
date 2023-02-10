<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\UserLanguage;

/**
 * UserLanguage fixture.
 */
class UserLanguageFixture extends PostgresActiveFixture
{
    public $tableName = 'user_language';
    public $modelClass = UserLanguage::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/user_language.php';
}
