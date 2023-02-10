<?php

namespace tests\codeception\common\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;
use common\models\UserRating;

/**
 * UserRating fixture.
 */
class UserRatingFixture extends PostgresActiveFixture
{
    public $tableName = 'user_rating';
    public $modelClass = UserRating::class;
    public $dataFile = '@tests/codeception/common/fixtures/data/user_rating.php';
}
