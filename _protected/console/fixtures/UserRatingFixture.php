<?php

namespace console\fixtures;

use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * UserRating fixture.
 */
class UserRatingFixture extends PostgresActiveFixture
{
    public $tableName = 'user_rating';
    public $modelClass = 'common\models\UserRating';
    public $dataFile = '@console/fixtures/data/user_rating.php';
    public $depends = [
        'console\fixtures\UserFixture',
        //'console\fixtures\RatingDetailFixture'
    ];
}
