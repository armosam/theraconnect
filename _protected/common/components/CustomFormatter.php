<?php

namespace common\components;

use Yii;
use InvalidArgumentException;
use NumberFormatter;
use yii\base\InvalidConfigException;
use yii\i18n\Formatter;
use kartik\datecontrol\Module;

/**
 * This class is representing a custom formatting functionality
 * we can use it to format values in our app
 *
 * @property string $rawDateFormat
 * @property string $rawDatetimeFormat
 */
class CustomFormatter extends Formatter
{
    /**
     * Value to be used to convert milliseconds to seconds.
     */
    const MILLISECONDS = 1000;

    /**
     * @var string Datetime with microseconds delimiter
     */
    public $datetimeWithMicrosecondsDelimiter = ' ';

    public function init()
    {
        parent::init();
        switch ($this->language) {
            case 'ru-RU':
                Yii::$app->params['dateControlDisplay'] = [
                    Module::FORMAT_DATE => 'php:j M Y г.',
                    Module::FORMAT_TIME => 'php:G:i:s',
                    Module::FORMAT_DATETIME => 'php:j M Y г., G:i:00',
                ];
                break;
            case 'hy-AM':
                Yii::$app->params['dateControlDisplay'] = [
                    Module::FORMAT_DATE => 'php:d M, Y թ.',
                    Module::FORMAT_TIME => 'php:H:i:s',
                    Module::FORMAT_DATETIME => 'php:d M, Y թ., H:i:00',
                ];
                break;
            case 'en-US':
                Yii::$app->params['dateControlDisplay'] = [
                    Module::FORMAT_DATE => 'php:M j, Y',
                    Module::FORMAT_TIME => 'php:g:i:s A',
                    Module::FORMAT_DATETIME => 'php:M j, Y, g:i:00 A',
                ];
                break;
            default:
                Yii::$app->params['dateControlDisplay'] = [
                    Module::FORMAT_DATE => 'php:Y-m-d',
                    Module::FORMAT_TIME => 'php:H:i:s',
                    Module::FORMAT_DATETIME => 'php:Y-m-d, H:i:00',
                ];
        }
    }

    /**
     * Formats a date, time or datetime in a float number as UNIX timestamp with milliseconds.
     * @param int|string|\DateTime $value the value to be formatted. The following type of values supported.
     *
     * - an integer representing a UNIX timestamp
     * - a string that can be [parsed to create a DateTime object](http://php.net/manual/en/datetime.formats.php).
     *   The timestamp assumed to be in [[defaultTimeZone]] unless a time zone explicitly given.
     * - a PHP [DateTime](http://php.net/manual/en/class.datetime.php) object
     *
     * @return string the formatted result.
     */
    public function asMillisecondTimestamp($value)
    {
        $millisecondTimestamp = Yii::$app->formatter->asTimestamp($value) * self::MILLISECONDS;

        return $millisecondTimestamp;
    }

    /**
     * Formats the value as a datetime with milliseconds.
     * @param int|string|\DateTime $value the value to be formatted. The following
     * types of value are supported:
     *
     * - an integer representing a UNIX timestamp. A UNIX timestamp is always in UTC by its definition.
     * - a string that can be [parsed to create a DateTime object](http://php.net/manual/en/datetime.formats.php).
     *   The timestamp assumed to be in [[defaultTimeZone]] unless a time zone explicitly given.
     * - a PHP [DateTime](http://php.net/manual/en/class.datetime.php) object. You may set the time zone
     *   for the DateTime object to specify the source time zone.
     *
     * The formatter will convert date values according to [[timeZone]] before formatting it.
     * If no timezone conversion should be performed, you need to set [[defaultTimeZone]] and [[timeZone]] to the same value.
     *
     * @param string $format the format used to convert the value into a date string.
     * If null, [[datetimeFormat]] will be used.
     *
     * This can be "short", "medium", "long", or "full", which represents a preset format of different lengths.
     * It can also be a custom format as specified in the [ICU manual](http://userguide.icu-project.org/formatparse/datetime).
     *
     * Alternatively this can be a string prefixed with `php:` representing a format that can be recognized by the
     * PHP [date()](http://php.net/manual/en/function.date.php)-function.
     *
     * @return string the formatted result.
     * @throws InvalidArgumentException if the input value can not be evaluated as a date value.
     * @throws InvalidConfigException if the date format is invalid.
     * @see datetimeFormat
     */
    public function asDatetimeWithMilliseconds($value, $format = null)
    {
        $millisecondsTime = $this->getMillisecondsWithZeroTime($value);

        $secondsTime = (int)($value / self::MILLISECONDS);

        $formattedDateTime = Yii::$app->formatter->asDatetime($secondsTime, $format) .
            $this->datetimeWithMicrosecondsDelimiter . $millisecondsTime;

        return $formattedDateTime;
    }

    /**
     * Transforms float milliseconds time in string milliseconds time with zeros:
     *
     * ```
     * 1523534316.88 => '880'
     * 1523534316.8 => '800'
     * 1523534316.0 => '000'
     * ```
     *
     * @param float $value
     *
     * @return string
     */
    protected function getMillisecondsWithZeroTime($value)
    {
        $millisecondsWithZeroTime = (string)($value % self::MILLISECONDS);

        if (strlen($millisecondsWithZeroTime) < 2) {
            $millisecondsWithZeroTime = '00' . $millisecondsWithZeroTime;
        } elseif (strlen($millisecondsWithZeroTime) < 3) {
            $millisecondsWithZeroTime = '0' . $millisecondsWithZeroTime;
        }

        return $millisecondsWithZeroTime;
    }

    /**
     * Returns formatted phone number
     * @param string $value
     * @param int $format
     * @return null|string
     */
    public function asPhone($value, $format = \libphonenumber\PhoneNumberFormat::NATIONAL) : string
    {
        if(empty($value)){
            return '';
        }
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $phoneNumber = $phoneUtil->parse($value);
            $value = $phoneUtil->format($phoneNumber, $format);
        } catch (\libphonenumber\NumberParseException $e) {
            Yii::error("Phone number $value failed to format.");
        }

//        $value = preg_replace('/[^0-9]/', '', $value);
//        $len = strlen($value);
//        if($len == 6) $value = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{3})/', '$1 $2 $2', $value);
//        elseif($len == 7) $value = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{3})/', '$1 $2 $3', $value);
//        elseif($len == 8) $value = preg_replace('/([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/', '$1 $2 $3 $4', $value);
//        elseif($len == 9) $value = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{2})([0-9]{2})/', '$1 - $2 $3 $4', $value);
//        elseif($len == 10) $value = preg_replace('/([0-9]{3})([0-9]{3})([0-9]{4})/', '($1) $2 - $3', $value);
//        elseif($len == 11 && substr($value, 0, 3) == '374') $value = preg_replace('/([0-9]{3})([0-9]{2})([0-9]{6})/', '($1) $2 $3', $value);
//        elseif($len == 11) $value = preg_replace('/([0-9]{1})([0-9]{3})([0-9]{3})([0-9]{4})/', '+$1 ($2) $3 - $4', $value);

        return $value;
    }

    /**
     * @param mixed $value
     * @param null $currency
     * @param array $options
     * @param array $textOptions
     * @return string
     * @throws InvalidConfigException
     */
    public function asCurrency($value, $currency = null, $options = [], $textOptions = []) : string
    {
        // This hardcoded solution is a result of icu and intl framework misconfiguration
        // when by changing language and locale it understand changing currency too.
        // This is a solution for currency symbol of Armenian Dram and Russian
        // It is working only when business is in Armenia and in armenian dram.
        // So it is translation of armenian currency in 3 allowed languages when locale is changing.

        switch($currency){
            case 'AMD':
                switch(Yii::$app->getFormatter()->locale){
                    case 'hy-AM':
                        $currency_symbol = 'դր․';
                        break;
                    case 'ru-RU':
                        $currency_symbol = 'др․';
                        break;
                    case 'en-US':
                        $currency_symbol = '֏';
                        break;
                    default:
                        $currency_symbol = '֏';
                }
                break;
            case 'RUB':
                switch(Yii::$app->getFormatter()->locale){
                    case 'hy-AM':
                        $currency_symbol = 'ռուբ․';
                        break;
                    case 'ru-RU':
                        $currency_symbol = 'руб․';
                        break;
                    case 'en-US':
                        $currency_symbol = '₽';
                        break;
                    default:
                        $currency_symbol = '₽';
                }
                break;
            default:
                $currency_symbol = '$';
        }
        $this->numberFormatterSymbols = [
            NumberFormatter::CURRENCY_SYMBOL => $currency_symbol
        ];

        return parent::asCurrency($value, $currency, $options, $textOptions);
    }
}