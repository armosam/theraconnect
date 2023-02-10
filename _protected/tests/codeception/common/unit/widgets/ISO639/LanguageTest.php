<?php

namespace tests\codeception\common\unit\widgets\ISO639;

use Codeception\Specify;
use common\widgets\ISO639\Language;
use tests\codeception\common\unit\TestCase;

/**
 * Class LanguageTest
 * @package tests\codeception\common\unit\widgets\ISO639
 * @group ISO639WidgetTest
 */
class LanguageTest extends TestCase
{
    use Specify;

    public function testAllEnglishLanguages()
    {
        $this->specify("allEnglish method returns correct data", function () {
            expect('method returns array', Language::allEnglish())->array();
            expect('method returns array with element name', Language::allEnglish())->hasKey('hy');
            expect('method returns array with element name', Language::allEnglish()['hy'])->equals('Armenian');
            expect('method returns array with count ', Language::allEnglish())->count(185);
        });
    }

    public function testAllNativeLanguages()
    {
        $this->specify("allNative method returns correct data", function () {
            expect('method returns array', Language::allNative())->array();
            expect('method returns array with element name', Language::allNative())->hasKey('hy');
            expect('method returns array with element name', Language::allNative()['hy'])->equals('Հայերեն');
            expect('method returns array with count ', Language::allNative())->count(185);
        });
    }

    public function testEnglishNameByCode()
    {
        $this->specify("englishByCode method returns correct string", function () {
            expect('method returns string', Language::englishNameByCode('hy'))->string();
            expect('method returns correct english name', Language::englishNameByCode('HY'))->equals('Armenian');
        });
    }

    public function testNativeNameByCode()
    {
        $this->specify("nativeByCode method returns correct string", function () {
            expect('method returns string', Language::nativeNameByCode('hy'))->string();
            expect('method returns correct native name', Language::nativeNameByCode('Hy'))->equals('Հայերեն');
        });
    }
}