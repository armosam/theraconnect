<?php

namespace tests\codeception\console\unit;

/**
 * @inheritdoc
 */
class DbTestCase extends \tests\codeception\common\_support\yii\DbTestCase
{
    public $appConfig = '@tests/codeception/config/console/unit.php';
}
