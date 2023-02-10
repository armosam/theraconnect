<?php

namespace tests\codeception\common\unit\models;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use tests\codeception\common\unit\DbTestCase;
use tests\codeception\common\fixtures\PatientFixture;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\fixtures\UserFixture;
use common\models\User;

/**
 * Class OrderTest
 * @package tests\codeception\common\unit\models
 * @group OrderTest
 */
class OrderTest extends DbTestCase
{
    use Specify;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Clean up the objects against which you tested.
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIsOrderSubmitToProviderAllowed()
    {
        $this->specify('ensure method countOfRejectedOrders returns correct numbers of rejected orders to the given provider', function () {
            $model = User::findOne([5]);
            expect('method should return 0 result', $model->countOfRejectedOrders(4))->equals(0);

            $model = User::findOne([3]);
            expect('method returns 3 result', $model->countOfRejectedOrders(4))->equals(3);
        });
    }


    /**
     * Declares the fixtures that are needed by the current test case.
     *
     * @return array
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user.php'
            ],
            'role' => [
                'class' => RoleFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/auth_assignment.php'
            ],
            'order' => [
                'class' => PatientFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/order.php'
            ],
        ];
    }
}