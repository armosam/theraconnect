<?php

namespace tests\codeception\common\unit;

/**
 * @inheritdoc
 */
class DbTestCase extends \tests\codeception\common\_support\yii\DbTestCase
{
    public $appConfig = '@tests/codeception/config/common/unit.php';
}
