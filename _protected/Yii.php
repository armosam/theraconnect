<?php

use yii\BaseYii;
use yii\web\UrlManager;
use floor12\metamaster\MetaMaster;
use common\components\GeoCoding;
use common\components\LogArchiveComponent;
use common\components\TwilioProvider;

/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocomplete.
 */
class Yii extends BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 * @property UrlManager $urlManagerToFront Url Manager component for backend to frontend
 * @property LogArchiveComponent $logArchive Log Archive component
 * @property GeoCoding $geoCoding GeoCoding component
 * @property TwilioProvider $twilio Twilio provider component
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property MetaMaster $meta Meta component to add custom meta tags on the page
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 *
 */
class ConsoleApplication extends yii\console\Application
{
}