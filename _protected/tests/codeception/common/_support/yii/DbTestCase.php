<?php

namespace tests\codeception\common\_support\yii;

use yii\test\InitDbFixture;

/**
 * Base class for database test cases
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class DbTestCase extends TestCase
{
    /**
     * @inheritdoc
     */
    public function globalFixtures()
    {
        return [
            InitDbFixture::class,
        ];
    }
}
