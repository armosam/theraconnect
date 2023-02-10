<?php

namespace api\tests\unit;

/**
 * Class DbTestCase
 * @package backend\tests\unit
 */
class DbTestCase extends \tests\codeception\common\_support\yii\DbTestCase
{
    public $appConfig = '@tests/codeception/config/api/unit.php';
}
