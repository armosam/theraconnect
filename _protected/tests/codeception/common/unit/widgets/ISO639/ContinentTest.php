<?php

namespace tests\codeception\common\unit\widgets\ISO639;

use Codeception\Specify;
use common\widgets\ISO639\Continent;
use tests\codeception\common\unit\TestCase;

/**
 * Class ContinentTest
 * @package tests\codeception\common\unit\widgets\ISO639
 * @group ISO639WidgetTest
 */
class ContinentTest extends TestCase
{
    use Specify;

    public function testAllEnglishContinents()
    {
        $this->specify("allEnglish method returns correct data", function () {
            expect('method returns array', Continent::allEnglish())->array();
            expect('method returns array with element name', Continent::allEnglish())->hasKey('EU');
            expect('method returns array with element name', Continent::allEnglish()['EU'])->equals('Europe');
            expect('method returns array with count ', Continent::allEnglish())->count(7);
        });
    }

    public function testEnglishNameByCode()
    {
        $this->specify("englishByCode method returns correct string", function () {
            expect('method returns string', Continent::englishNameByCode('EU'))->string();
            expect('method returns correct english name', Continent::englishNameByCode('eu'))->equals('Europe');
        });
    }

}