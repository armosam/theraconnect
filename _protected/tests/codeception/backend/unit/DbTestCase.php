<?php

namespace tests\codeception\backend\unit;

/**
 * Class DbTestCase
 * @package backend\tests\unit
 */
class DbTestCase extends \tests\codeception\common\_support\yii\DbTestCase
{
    public $appConfig = '@tests/codeception/config/backend/unit.php';
}
