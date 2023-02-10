<?php

namespace tests\codeception\frontend\unit;

/**
 * Class DbTestCase
 * 
 * @package tests\codeception\frontend\unit
 */
class DbTestCase extends \tests\codeception\common\_support\yii\DbTestCase
{
    public $appConfig = '@tests/codeception/config/frontend/unit.php';
}
