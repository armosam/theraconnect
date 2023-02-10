<?php

namespace _protected\tests\codeception\common\unit\helpers;

use Yii;
use Codeception\Stub;
use Codeception\Specify;
use common\helpers\ConstHelper;
use tests\codeception\common\unit\TestCase;

/**
 * Class ConstHelperTest
 * @group constHelper
 * @package _protected\tests\codeception\common\unit\helpers
 */
class ConstHelperTest extends TestCase
{
    use Specify;

    public function testCalculateExperienceRange()
    {
        $expected = Yii::t('app', 'Experience: {from} - {to} years', [
            'from' => 3,
            'to' => 10
        ]);
        $message = ConstHelper::calculateExperienceRange([3,5,10]);
        $this->assertEquals($expected, $message);

        $expected = Yii::t('app', 'Experience: {from,plural,=0{less than a year} =1{1 year} other{# years}}', [
            'from' => 5
        ]);
        $message = ConstHelper::calculateExperienceRange([5,5,5]);
        $this->assertEquals($expected, $message);
    }

    public function testCalculateExperienceInYears()
    {
        $date = date_create('2018-10-10', new \DateTimeZone(Yii::$app->timeZone));
        $now = date_create('now', new \DateTimeZone(Yii::$app->timeZone));
        $interval = $date->diff($now);
        $interval_in_years = (int)$interval->format('%y');
        $expected = 1;
        if ($interval_in_years > 1) {
            $expected = $interval_in_years;
        }
        if($interval->format('%m') >= 6) {
            //for example if experience is 2 years and 6 or more months then it'll be 3 years
            ++$expected;
        }
        $experience = ConstHelper::calculateAgeInYears($date);
        $this->assertEquals($expected, $experience);
    }

    public function testLastYearsList()
    {
        $this->assertEquals(5, count(ConstHelper::LastYearsList(4)));
        $this->assertArrayHasKey(date('Y'), ConstHelper::LastYearsList(4));
    }

    public function testGetMonthNames()
    {
        $this->assertEquals(Yii::t('app', 'January'), ConstHelper::getMonthNames()[1]);
        $this->assertEquals(Yii::t('app', 'February'), ConstHelper::getMonthNames()[2]);
        $this->assertEquals(Yii::t('app', 'March'), ConstHelper::getMonthNames()[3]);
        $this->assertEquals(Yii::t('app', 'April'), ConstHelper::getMonthNames()[4]);
        $this->assertEquals(Yii::t('app', 'May'), ConstHelper::getMonthNames()[5]);
        $this->assertEquals(Yii::t('app', 'June'), ConstHelper::getMonthNames()[6]);
        $this->assertEquals(Yii::t('app', 'July'), ConstHelper::getMonthNames()[7]);
        $this->assertEquals(Yii::t('app', 'August'), ConstHelper::getMonthNames()[8]);
        $this->assertEquals(Yii::t('app', 'September'), ConstHelper::getMonthNames()[9]);
        $this->assertEquals(Yii::t('app', 'October'), ConstHelper::getMonthNames()[10]);
        $this->assertEquals(Yii::t('app', 'November'), ConstHelper::getMonthNames()[11]);
        $this->assertEquals(Yii::t('app', 'December'), ConstHelper::getMonthNames()[12]);
        $this->assertEquals(12, count(ConstHelper::getMonthNames()));
    }

    public function testCalculateUsernameFromEmailAddress()
    {
        $security = Stub::make(
            'yii\base\Security',
            [ 'generateRandomString' => 'testuser']
        );
        Yii::$app->set('security', $security);

        $this->assertEquals('usernametestusergmailcom', ConstHelper::calculateUsernameFromEmailAddress('username@gmail.com'));
        $this->assertEquals('usernametestusergmailcom', ConstHelper::calculateUsernameFromEmailAddress('username123@gmail.com'));
        $this->assertEquals('usernametestusergmailcom', ConstHelper::calculateUsernameFromEmailAddress('!$%_username123@gmail.com'));
        $this->assertEquals('testusergmailcom', ConstHelper::calculateUsernameFromEmailAddress('123456@gmail.com'));
        $this->assertEquals('testusergmailcom', ConstHelper::calculateUsernameFromEmailAddress('$%^&*()@gmail.com'));
    }

    public function testGetTimeZoneList()
    {
        $expected = include __DIR__.'/data/time_zones.php';
        $this->specify('ensure timeZoneList method returns correct list of time zones', function() use ($expected) {
            expect('getTimeZoneList method returns non empty', ConstHelper::getTimeZoneList())->notEmpty();
            expect('getTimeZoneList method returns array', ConstHelper::getTimeZoneList())->array();
            expect('getTimeZoneList returns correct expected data', ConstHelper::getTimeZoneList())->equals($expected);
            expect('getTimeZoneList returns time zone of given value', ConstHelper::getTimeZoneList('Asia/Yerevan'))->equals('(GMT+04:00) Asia, Yerevan');
            expect('getTimeZoneList returns time zone of given value', ConstHelper::getTimeZoneList('America/Los_Angeles'))->equals('(GMT-07:00) America, Los Angeles');
        });
    }

    public function testIsPhoneNumber()
    {
        $this->assertTrue(ConstHelper::isPhoneNumber('8189991234'));
        $this->assertFalse(ConstHelper::isPhoneNumber('(818) 999-1234'));
    }

    public function testUnderscoreBase()
    {
        $expected = 'ARMEN_BABLANYAN';
        $this->assertEquals(ConstHelper::underscoreBase('Armen Bablanyan'), $expected);
    }

    public function testExtractKeyWords()
    {
        $expected = 'this,great,day';
        $this->assertEquals($expected, ConstHelper::extractKeyWords('This is a great day'));
    }

    public function testIsSupportsSenderID()
    {
        $this->specify('ensure phone number supports sender ID', function() {
            expect('returns false result for empty number', ConstHelper::isSupportSenderID(''))->false();
            expect('returns true result for number +37410471159', ConstHelper::isSupportSenderID('+37410471159'))->true();
            expect('returns true result for number +71047111823', ConstHelper::isSupportSenderID('+71047111823'))->true();
            expect('returns true result for number 71047111823', ConstHelper::isSupportSenderID('71047111823'))->false();
            expect('returns false result for number +82898778991', ConstHelper::isSupportSenderID('+82898778991'))->false();
        });
    }

    public function testServiceDurations()
    {
        $this->assertEquals(24, count(ConstHelper::serviceDurations()));
        for($i = 0.5; $i<=12; $i+=0.5){
            $key = (string)($i * 3600);
            $this->assertEquals(Yii::t('app', '{time,plural,=0.5{# hour} =1{# hour} other{# hours}}', ['time' => $i]), ConstHelper::serviceDurations($key));
            $this->assertArrayHasKey($key, ConstHelper::serviceDurations());
        }
    }

    public function testGetOrderCancelReasonList()
    {
        $expected = [
            ConstHelper::ORDER_CANCELLATION_REASON_OUT_OF_CITY => Yii::t('app', 'Currently Out of city'),
            ConstHelper::ORDER_CANCELLATION_REASON_FAR => Yii::t('app', 'It is too far'),
            ConstHelper::ORDER_CANCELLATION_REASON_SICK => Yii::t('app', 'Health Problems'),
            ConstHelper::ORDER_CANCELLATION_REASON_DOUBLE_BOOKING => Yii::t('app', 'Double Request'),
            ConstHelper::ORDER_CANCELLATION_REASON_THE_CLIENT_REQUESTED_TO_CANCEL => Yii::t('app', 'The client requested'),
            ConstHelper::ORDER_CANCELLATION_REASON_OTHER_REASONS => Yii::t('app', 'Other Reasons')
        ];
        $this->assertEquals($expected, ConstHelper::getOrderCancelReasonList());
        $this->assertEquals($expected[ConstHelper::ORDER_CANCELLATION_REASON_OUT_OF_CITY], ConstHelper::getOrderCancelReasonList(ConstHelper::ORDER_CANCELLATION_REASON_OUT_OF_CITY));
        $this->assertEquals(6, count(ConstHelper::getOrderCancelReasonList()));
    }

    public function testGetOrderRejectReasonList()
    {
        $expected = [
            ConstHelper::ORDER_REJECTION_REASON_OUT_OF_CITY => Yii::t('app', 'Currently Out of city'),
            ConstHelper::ORDER_REJECTION_REASON_FAR => Yii::t('app', 'It is too far'),
            ConstHelper::ORDER_REJECTION_REASON_SICK => Yii::t('app', 'Health Problems'),
            ConstHelper::ORDER_REJECTION_REASON_DOUBLE_BOOKING => Yii::t('app', 'Double Request'),
            ConstHelper::ORDER_REJECTION_REASON_OTHER_REASONS => Yii::t('app', 'Other Reasons')
        ];
        $this->assertEquals($expected, ConstHelper::getOrderRejectReasonList());
        $this->assertEquals($expected[ConstHelper::ORDER_REJECTION_REASON_OUT_OF_CITY], ConstHelper::getOrderRejectReasonList(ConstHelper::ORDER_REJECTION_REASON_OUT_OF_CITY));
        $this->assertEquals(5, count(ConstHelper::getOrderRejectReasonList()));
    }

    public function testGetStatusList()
    {
        $expected = [ConstHelper::STATUS_ACTIVE => Yii::t('app', 'Active'), ConstHelper::STATUS_PASSIVE => Yii::t('app', 'Passive'), ConstHelper::STATUS_DELETED => Yii::t('app', 'Deleted')];
        $this->assertEquals(ConstHelper::getStatusList(), $expected);
        $this->assertEquals(ConstHelper::getStatusList(ConstHelper::STATUS_ACTIVE), $expected[ConstHelper::STATUS_ACTIVE]);
        $this->assertEquals(ConstHelper::getStatusList(ConstHelper::STATUS_PASSIVE), $expected[ConstHelper::STATUS_PASSIVE]);
        $this->assertEquals(ConstHelper::getStatusList(ConstHelper::STATUS_DELETED), $expected[ConstHelper::STATUS_DELETED]);
    }

    /**
     * @ignore
     */
    public function testGetYesNoList()
    {
        $expected = [ConstHelper::FLAG_YES => Yii::t('app', 'Yes'), ConstHelper::FLAG_NO => Yii::t('app', 'No')];
        $this->assertEquals(ConstHelper::getYesNoList(), $expected);
        $this->assertEquals(ConstHelper::getYesNoList(ConstHelper::FLAG_YES), $expected[ConstHelper::FLAG_YES]);
        $this->assertEquals(ConstHelper::getYesNoList(ConstHelper::FLAG_NO), $expected[ConstHelper::FLAG_NO]);
    }

    public function testGetCurrencyList()
    {
        $expected = [
            'USD' => Yii::t('app', 'US Dollar'),
            'GBP' => Yii::t('app', 'British Pound'),
            'RUB' => Yii::t('app', 'Russian Ruble'),
            //'AMD' => Yii::t('app', 'Armenian Dram')
        ];
        $this->assertEquals($expected, ConstHelper::getCurrencyList());
        $this->assertEquals(ConstHelper::getCurrencyList('USD'), $expected['USD']);
        $this->assertEquals(ConstHelper::getCurrencyList('GBP'), $expected['GBP']);
        $this->assertEquals(ConstHelper::getCurrencyList('RUB'), $expected['RUB']);
        //$this->assertEquals(ConstHelper::getCurrencyList('AMD'), $expected['AMD']);
    }

    /**
     * Ensure GetImgPath returns correct path
     */
    public function testGetImgPath()
    {
        $this->specify('ensure getImgPath method returns correct path from uploads folder instead of theme/img folder as it will be called from non theme application', function(){

            expect('getImgPath method returns relative path to the default logo file from uploads', ConstHelper::getImgPath())
                ->equals('/uploads/img/logo.png');
            expect('getImgPath method returns full path to the default logo file from uploads', ConstHelper::getImgPath(true))
                ->equals('/vagrant/www/public_html/uploads/img/logo.png');

            expect('getImgPath method returns relative path to the custom file from uploads', ConstHelper::getImgPath(false, 'small_logo.png'))
                ->equals('/uploads/img/small_logo.png');
            expect('getImgPath method returns fill path to the custom file from uploads', ConstHelper::getImgPath(true, 'small_logo.png'))
                ->equals('/vagrant/www/public_html/uploads/img/small_logo.png');

            expect('getImgPath method returns relative path to the non existing custom file from uploads', ConstHelper::getImgPath(false, 'test.png'))
                ->equals('/uploads/no_image.png');
            expect('getImgPath method returns full path to the non existing custom file from uploads', ConstHelper::getImgPath(true, 'test.png'))
                ->equals('/vagrant/www/public_html/uploads/no_image.png');
        });
    }
}
