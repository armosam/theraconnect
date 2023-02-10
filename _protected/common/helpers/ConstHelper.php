<?php

namespace common\helpers;

use Yii;
use yii\base\InvalidArgumentException;
use common\exceptions\FileNotFoundException;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Imagine\Image\ImageInterface;
use GeoIp2\Exception\AddressNotFoundException;
use GeoIp2\Model\City;
use GeoIp2\Database\Reader;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

/**
 * Constant helper class.
 */
class ConstHelper
{
    public const EMPTY_STRING = '';
    public const FLAG_YES = 'Y';
    public const FLAG_NO = 'N';

    public const STATUS_ACTIVE = 'A';
    public const STATUS_PASSIVE = 'P';
    public const STATUS_DELETED = 'D';

    public const NOTE_STATUS_PENDING = 'P';
    public const NOTE_STATUS_SUBMITTED = 'S';
    public const NOTE_STATUS_SUBMITTED_NO_RPT = 'N';
    public const NOTE_STATUS_ACCEPTED = 'A';


    public const ORDER_CANCELLATION_REASON_OUT_OF_CITY = 'out_of_city';
    public const ORDER_CANCELLATION_REASON_FAR = 'too_far';
    public const ORDER_CANCELLATION_REASON_SICK = 'health_problems';
    public const ORDER_CANCELLATION_REASON_DOUBLE_BOOKING  = 'double_booking';
    public const ORDER_CANCELLATION_REASON_OTHER_REASONS = 'other_reasons';
    public const ORDER_CANCELLATION_REASON_THE_CLIENT_REQUESTED_TO_CANCEL = 'the_client_requested_to_cancel';
    public const ORDER_CANCELLATION_REASON_NOT_ACTIVE_PROVIDER = 'provider_is_deactivated';
    public const ORDER_CANCELLATION_REASON_SERVICE_REMOVED = 'service_is_removed';
    public const ORDER_CANCELLATION_REASON_ORDER_ISSUE = 'order_has_issue';

    public const ORDER_REJECTION_REASON_OUT_OF_CITY = 'out_of_city';
    public const ORDER_REJECTION_REASON_FAR = 'too_far';
    public const ORDER_REJECTION_REASON_SICK = 'health_problems';
    public const ORDER_REJECTION_REASON_DOUBLE_BOOKING  = 'double_booking';
    public const ORDER_REJECTION_REASON_OTHER_REASONS = 'other_reasons';

    public const SEARCH_ORDERING_BASED_ON_DISTANCE_ASC = 'distance_asc';
    public const SEARCH_ORDERING_BASED_ON_DISTANCE_DESC = 'distance_desc';

    public const IMAGE_SIZE_HOMEPAGE_CATEGORY = 'homePageImage';
    public const IMAGE_SIZE_PROFILE_AVATAR = 'avatarImage';
    public const IMAGE_SIZE_GALLERY_PHOTO = 'galleryImage';
    public const IMAGE_SIZE_ARTICLE_PHOTO = 'articleImage';


    /**
     * Returns the possible values of status fields.
     *
     * @param null|string $selected
     * @return array|string Array of possible states of status.
     */
    public static function getStatusList($selected = null)
    {
        $data = [
            self::STATUS_ACTIVE => Yii::t('app', 'Active'),
            self::STATUS_PASSIVE => Yii::t('app', 'Passive'),
            self::STATUS_DELETED => Yii::t('app', 'Deleted'),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the possible values of note status field.
     *
     * @param null|string $selected
     * @return array|string Array of possible states of status.
     */
    public static function getNoteStatusList($selected = null)
    {
        $data = [
            self::NOTE_STATUS_PENDING => Yii::t('app', 'Pending'),
            self::NOTE_STATUS_SUBMITTED => Yii::t('app', 'Submitted'),
            self::NOTE_STATUS_SUBMITTED_NO_RPT => Yii::t('app', 'Submitted By PTA'),
            self::NOTE_STATUS_ACCEPTED => Yii::t('app', 'Accepted'),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the possible values of YesNo fields.
     *
     * @param null|string $selected
     * @return array|string Array for YesNo drop downs.
     */
    public static function getYesNoList($selected = null)
    {
        $data = [
            self::FLAG_YES => Yii::t('app', 'Yes'),
            self::FLAG_NO => Yii::t('app', 'No'),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the possible values of Service Radius fields.
     *
     * @param null|string $selected
     * @return array|string Array for radius drop downs.
     */
    public static function getServiceRadiusList($selected = null)
    {
        $data = [
            10000 => Yii::t('app', ''),
            10 => Yii::t('app', '{0,number}', 10),
            50 => Yii::t('app', '{0,number}', 50),
            100 => Yii::t('app', '{0,number}', 100),
            200 => Yii::t('app', '{0,number}', 200),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the array of possible reasons of canceling order.
     * @param null|string $selected
     * @return array|string
     */
    public static function getOrderCancelReasonList($selected = null)
    {
        $data = [
            self::ORDER_CANCELLATION_REASON_OUT_OF_CITY => Yii::t('app', 'Currently Out of city'),
            self::ORDER_CANCELLATION_REASON_FAR => Yii::t('app', 'It is too far'),
            self::ORDER_CANCELLATION_REASON_SICK => Yii::t('app', 'Health Problems'),
            self::ORDER_CANCELLATION_REASON_DOUBLE_BOOKING => Yii::t('app', 'Double Request'),
            self::ORDER_CANCELLATION_REASON_THE_CLIENT_REQUESTED_TO_CANCEL => Yii::t('app', 'The client requested'),
            self::ORDER_CANCELLATION_REASON_OTHER_REASONS => Yii::t('app', 'Other Reasons')
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the array of possible reasons of reject order.
     * @param null|string $selected
     * @return array|string
     */
    public static function getOrderRejectReasonList($selected = null)
    {
        $data = [
            self::ORDER_REJECTION_REASON_OUT_OF_CITY => Yii::t('app', 'Currently Out of city'),
            self::ORDER_REJECTION_REASON_FAR => Yii::t('app', 'It is too far'),
            self::ORDER_REJECTION_REASON_SICK => Yii::t('app', 'Health Problems'),
            self::ORDER_REJECTION_REASON_DOUBLE_BOOKING => Yii::t('app', 'Double Request'),
            self::ORDER_REJECTION_REASON_OTHER_REASONS => Yii::t('app', 'Other Reasons')
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns the array to order providers by price, distance, rating and experience on provider search page.
     * @param null|string $selected
     * @return array|string
     */
    public static function getProviderSearchOrderByList($selected = null)
    {
        $data = [
            '' => Yii::t('app', 'Order By:'),
            self::SEARCH_ORDERING_BASED_ON_DISTANCE_ASC => Yii::t('app', 'Nearest Patients'),
            self::SEARCH_ORDERING_BASED_ON_DISTANCE_DESC => Yii::t('app', 'Farther Patients'),
        ];
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Generates unique ID
     *
     * @param int $length Length of generated ID
     * @return string Unique ID
     * @throws Exception
     */
    public static function uid_gen($length = 13) {
        if (function_exists('random_bytes')) {
            $bytes = random_bytes(ceil($length / 2));
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes(ceil($length / 2)) ?: '';
        } else {
            throw new Exception('No cryptographically secure random function available to generate unique ID');
        }
        return substr(bin2hex($bytes), 0, $length);
    }

    /**
     * Generates content for service duration drop down box
     * It shows hours and value will be saved in seconds
     *
     * @param string|null $selected
     * @return array|string
     */
    public static function serviceDurations($selected = null)
    {
        $data = [];
        for($i = 0.5; $i<=12; $i+=0.5){
            $data[($i * 3600)] = Yii::t('app', '{time,plural,=0.5{# hour} =1{# hour} other{# hours}}', ['time' => $i]);
        }
        if($selected !== null){
            return $data[$selected] ?? null;
        }
        return $data;
    }

    /**
     * Returns a list of available time zones
     * @param null|string $selected
     * @return array|string
     */
    public static function getTimeZoneList($selected = null)
    {
        static $timezones = null;

        if ($timezones === null) {
            $timezones = [];
            $offsets = [];
            $now = new DateTime('now', new DateTimeZone('UTC'));

            $format_GMT_offset = function($offs){
                $hours = (int)($offs / 3600);
                $minutes = abs((int)($offs % 3600 / 60));
                return 'GMT' . ($offs ? sprintf('%+03d:%02d', $hours, $minutes) : '');
            };

            $format_timezone_name = function($tz){
                return str_replace(array('/', '_', 'St '), array(', ', ' ', 'St. '), $tz);
            };

            foreach (DateTimeZone::listIdentifiers() as $timezone) {
                $now->setTimezone(new DateTimeZone($timezone));
                $offsets[] = $offset = $now->getOffset();
                $timezones[$timezone] = '(' . $format_GMT_offset($offset) . ') ' . $format_timezone_name($timezone);
            }

            array_multisort($timezones);
            ksort($timezones);

        }

        if($selected !== null){
            return $timezones[$selected] ?? '';
        }

        return $timezones;
    }

    /**
     * Returns a list of available currencies
     * @param null|string $selected
     * @return array|string
     */
    public static function getCurrencyList($selected = null){
        $data = [
            'USD' => Yii::t('app', 'US Dollar'),
            'GBP' => Yii::t('app', 'British Pound'),
            'RUB' => Yii::t('app', 'Russian Ruble')
            //'AMD' => Yii::t('app', 'Armenian Dram'),
        ];

        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Returns filtered out string. It will replace all spaces to underscores. Then it will make uppercase
     * @param string $string
     * @return mixed|string
     */
    public static function underscoreBase($string){
        $string = strtolower($string);
        $string = preg_replace('/[^a-zA-Z0-9_ ]+/', '', $string); // Removes special chars.
        $string = preg_replace('/\s+/', '_', $string); // Replaces multiple white spaces with single underscore.
        $string = strtoupper($string);
        return $string;
    }

    /**
     * Returns filtered and normalized username from given email of social provider
     * @param string $email Email address
     * @return string Formatted Username
     * @throws \yii\base\Exception
     */
    public static function calculateUsernameFromEmailAddress($email)
    {
        $data = explode('@', $email);
        $data[0] = preg_replace("/[^a-zA-Z]+/", "", $data[0]);
        if(empty($data[0])){
            $data[0] = Yii::$app->security->generateRandomString(10);
        }else{
            $data[0] .= Yii::$app->security->generateRandomString(5);
        }
        return preg_replace("/[^a-z0-9]+/", "", strtolower($data[0].$data[1]));
    }

    /**
     * @param string $file_name
     * @param string $image_size_name
     * @return ImageInterface
     */
    public static function cropPhoto($file_name, $image_size_name)
    {
        if (empty($file_name) || !is_file($file_name) || !is_readable($file_name)) {
            throw new InvalidArgumentException('Crop of file is failed due to file not found');
        }

        $defaultSize = Yii::$app->params[$image_size_name];
        $defaultRatio = $defaultSize['width'] / $defaultSize['height'];
        $finalBox = new Box($defaultSize['width'], $defaultSize['height']);

        $image = Image::getImagine()
            ->open($file_name);

        $ratio = $image->getSize()->getWidth() / $image->getSize()->getHeight();
        $box = ($ratio > 1) ? new Box(($defaultSize['height'] * $ratio), $defaultSize['height']) : new Box($defaultSize['height'], ($defaultSize['height'] / $ratio));

        $widenBox = $box->widen($defaultSize['width']);
        $centerWidenBoxX = (($widenBox->getWidth() / 2) - ($defaultSize['width'] / 2)) < 0 ? 0 : (($widenBox->getWidth() / 2) - ($defaultSize['width'] / 2));
        $centerWidenBoxY = (($widenBox->getHeight() / 2) - ($defaultSize['height'] / 2)) < 0 ? 0 : (($widenBox->getHeight() / 2) - ($defaultSize['height'] / 2));
        $cropWidenBoxStart = new Point($centerWidenBoxX, $centerWidenBoxY);

        $heightenBox = $box->heighten($defaultSize['height']);
        $centerHeightenBoxX = (($heightenBox->getWidth() / 2) - ($defaultSize['width'] / 2)) < 0 ? 0 : (($heightenBox->getWidth() / 2) - ($defaultSize['width'] / 2));
        $centerHeightenBoxY = (($heightenBox->getHeight() / 2) - ($defaultSize['height'] / 2)) < 0 ? 0 : (($heightenBox->getHeight() / 2) - ($defaultSize['height'] / 2));
        $cropHeightenBoxStart = new Point($centerHeightenBoxX, $centerHeightenBoxY);

        if ($defaultRatio > 1) { // when default image is landscape
            if ($ratio > 1) { // when uploaded image is landscape
                if ($ratio > $defaultRatio) {
                    $image
                        ->resize($heightenBox)
                        ->crop($cropHeightenBoxStart, $finalBox);
                } elseif ($ratio < $defaultRatio) {
                    $image
                        ->resize($widenBox)
                        ->crop($cropWidenBoxStart, $finalBox);
                } elseif ($ratio == $defaultRatio) {
                    $image
                        ->resize($finalBox);
                }
            } elseif ($ratio <= 1) { // when uploaded image is portrait or square
                $image
                    ->resize($widenBox)
                    ->crop($cropWidenBoxStart, $finalBox);
            }
        } else { // when default image is portrait
            if ($ratio >= 1) { // when uploaded image is landscape or square
                $image
                    ->resize($heightenBox)
                    ->crop($cropHeightenBoxStart, $finalBox);
            } elseif ($ratio < 1) { // when uploaded image is portrait
                if ($ratio > $defaultRatio) {
                    $image
                        ->resize($heightenBox)
                        ->crop($cropHeightenBoxStart, $finalBox);
                } elseif ($ratio < $defaultRatio) {
                    $image
                        ->resize($widenBox)
                        ->crop($cropWidenBoxStart, $finalBox);
                } elseif ($ratio == $defaultRatio) {
                    $image
                        ->resize($finalBox);
                }
            }
        }
        return $image;
    }

    /**
     * Calculates experience in years by given date of start
     *
     * @param DateTime|void $start_date Object DateTime of start date
     * @return int Experience in years
     * @throws Exception
     */
    public static function calculateAgeInYears($start_date)
    {
        $now = new DateTime('now', new DateTimeZone(Yii::$app->timeZone));
        if (empty($start_date)){
            $start_date = $now;
        }
        $interval = $start_date->diff($now);
        $interval_in_years = (int)$interval->format('%y');

        if ($interval_in_years <= 1) {
            //if experience is less than one year then it will be one year
            $interval_in_years = 1;
        }
        if($interval->format('%m') >= 6) {
            //for example if experience is 2 years and 6 or more months then it'll be 3 years
            ++$interval_in_years;
        }
        return $interval_in_years;
    }

    /**
     * Returns experience range in string
     * @param int[] $experience
     * @return string
     */
    public static function calculateExperienceRange($experience)
    {
        if(empty($experience)){
            return null;
        }
        $min_experience = min($experience);
        $max_experience = max($experience);
        unset($experience);
        if($min_experience >= $max_experience){
            $experience_range = Yii::t('app', 'Experience: {from,plural,=0{less than a year} =1{1 year} other{# years}}', [
                'from' => $min_experience
            ]);
        }else{
            $experience_range = Yii::t('app', 'Experience: {from} - {to} years', [
                'from' => $min_experience,
                'to' => $max_experience
            ]);
        }
        return $experience_range;
    }

    /**
     * List of last given years
     *
     * @param int $n Number of years
     * @return array list of years
     * @throws Exception
     */
    public static function LastYearsList($n = 5) {
        $data = array();

        $currentTimeZone =  new DateTimeZone(Yii::$app->timeZone);
        $now = new DateTime('now', $currentTimeZone);
        $currentYear = $now->format('Y');
        $last5Years = $now->sub(new DateInterval('P'.$n.'Y'))->format('Y');

        for ($i=$last5Years; $i<=$currentYear; $i++) {
            $data[$i] = $i;
        }
        return $data;
    }

    /**
     * List of month names with translation to the current language
     *
     * @return array list of months
     */
    public static function getMonthNames()
    {
        if(!function_exists('cal_info')){
            return [];
        }
        $calendar = cal_info(0);
        $months = $calendar['months'] ?: [];
        array_walk($months, function(&$month){
            $month = Yii::t('app', $month);
        });
        return $months;
    }

    /**
     * Checks and returns if phone number supports twilio caller ID
     * @param string $phone_number
     * @return bool
     */
    public static function isSupportSenderID($phone_number)
    {
        $result = false;
        $phoneUtil = \libphonenumber\PhoneNumberUtil::getInstance();
        try {
            $result = $phoneUtil->parse($phone_number);
            $country_code = $result->getCountryCode();
            $result = (in_array($country_code, ['7', '374'], false));
        } catch (\libphonenumber\NumberParseException $e) {
            Yii::error("Country code not detected for number $phone_number");
        }
        return $result;
    }

    /**
     * Checks if cell phone number
     * @param string $number
     * @return false|int
     */
    public static function isPhoneNumber($number){
        return (!empty($number) && preg_match('/^[0-9]+$/', $number, $matches));
    }

    /**
     * Returns dummy array for specialists list
     * ['1' => '1 Specialist', '2' => '2 Specialists', '3' => '3 Specialists', ...]
     *
     * @param null|int $selected
     * @return array|string|int
     */
    public static function getSpecialistNumberList($selected = null){
        $data = [];
        for ($i=1; $i<=20; $i++) {
            $data[$i] = Yii::t('app', '{provider,plural, =0{Specialist} one{# Specialist} other{# Specialists}}', ['provider' => $i]);
        }
        if($selected !== null){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }

    /**
     * Creates a new string of keywords from given text
     * @param $string
     * @return string
     */
    public static function extractKeyWords($string) {
        mb_internal_encoding('UTF-8');
        $stop_words = array();
        $string = preg_replace('/[\pP]/u', '', trim(preg_replace('/\s\s+/iu', '', mb_strtolower($string))));
        $matchWords = array_filter(explode(' ',$string) , function ($item) use ($stop_words) { return !($item == '' || in_array($item, $stop_words) || mb_strlen($item) <= 2 || is_numeric($item));});
        $wordCountArr = array_count_values($matchWords);
        arsort($wordCountArr);
        return join(',', array_keys(array_slice($wordCountArr, 0, 10)));
    }

    /**
     * Returns logo's or any file's relative or full path located in the theme/img folder
     * @param bool $fullPath if true it returns full path of logo
     * @param string $file_name specific file name to get full or relative path for
     * @param string $path specific file path to get full or relative path for
     * @return bool|string
     */
    public static function getImgPath($fullPath = false, $file_name = 'small_logo.png', $path = 'img')
    {
        $file_path = trim($path, '/\\') . DIRECTORY_SEPARATOR . ltrim($file_name, '/\\');
        try{
            if(!empty(Yii::$app->view->theme)){
                $basePath = Yii::$app->view->theme->basePath . DIRECTORY_SEPARATOR . ltrim($file_path, '/\\');
                $baseUrl = Yii::$app->view->theme->baseUrl . DIRECTORY_SEPARATOR . ltrim($file_path, '/\\');
            }else{
                // this is for tests when testing from common application
                $basePath = Yii::getAlias('@uploads') . DIRECTORY_SEPARATOR . ltrim($file_path, '/\\');
                $baseUrl = '/uploads' . DIRECTORY_SEPARATOR . ltrim($file_path, '/\\');
            }

            if (!file_exists($basePath) || !is_file($basePath) || !is_readable($basePath)){
                throw new FileNotFoundException(sprintf('File %s not found', $basePath));
            }

        }catch(Exception $e){
            // In case if file not found it will return existing file indicating file not found
            Yii::error(sprintf("Image file by specified path %s not fund due error: %s", $file_path, $e->getMessage()));
            $basePath = Yii::getAlias('@uploads/no_image.png');
            $baseUrl = '/uploads/no_image.png';
        }

        return $fullPath ? $basePath : $baseUrl;
    }

    /**
     * Makes string short and adds given symbols at the end
     * Default symbol is '...'
     * @param string $string
     * @param int $start
     * @param int $length
     * @param string $symbol
     * @return string
     */
    public static function showShortString($string, $start = 0, $length = 20, $symbol = '...')
    {
        return mb_strimwidth($string, $start, $length, $symbol);
    }

    /**
     * Returns DateTime object based on given date string and system timeZone
     * @param string|null $date_string
     * @return DateTime|false
     */
    public static function dateTime($date_string = null) {
        $timeZone = new DateTimeZone(Yii::$app->timeZone);
        return date_create($date_string, $timeZone);
    }

    /**
     * Formats given date from one format to the given format
     * @param string $date_string
     * @param string $format
     * @return string
     */
    public static function formatDate($date_string, $format = 'Y-m-d')
    {
        $result = '';
        if (empty($date_string)) {
            return $result;
        }
        if ($dateTime = self::dateTime($date_string)) {
            $result = $dateTime->format($format);
        }
        return $result;
    }

    /**
     * Generate UUID
     * @return string
     * @throws Exception
     */
    public static function uuid(){
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Return duration in seconds from given start and end times
     * @param string $start Start date and/or time
     * @param string $end End date and/or time
     * @return integer
     */
    public static function calculateDuration($start, $end)
    {
        $start_time = self::dateTime($start);
        $end_time = self::dateTime($end);
        $duration = $start_time->diff($end_time);
        $minutes = 0;
        $minutes += $duration->days * 24 * 60 * 60;
        $minutes += $duration->h * 60 * 60;
        $minutes += $duration->i * 60;
        $minutes += $duration->s;
        return $minutes;
    }

    /**
     * Runs the given DB query using the given privileged user and database
     * For this method to work we need configured peer authentication for given user and user needs all privileges to run given query.
     *
     * @param string $command SQL query ending with ';'
     * @param string $database Database where to connect
     * @param string $super_user User to use to login to the database
     */
    public static function runDBQuery($command, $database, $super_user='postgres')
    {
        system("sudo -Hiu $super_user psql -U $super_user $database -c \"
            $command
        \"");
    }
}