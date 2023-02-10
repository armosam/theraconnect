<?php

namespace tests\codeception\api;

use Codeception\Actor;
use Codeception\Lib\Friend;
use yii\test\FixtureTrait;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions,
        FixtureTrait;

    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [

        ];
    }
}
