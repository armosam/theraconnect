<?php

namespace tests\codeception\common\_support\yii;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;
use yii\console\Application;
use yii\di\Container;
use yii\test\BaseActiveFixture;
use yii\test\FixtureTrait;
use Codeception\TestCase\Test;

/**
 * TestCase is the base class for all codeception unit tests
 *
 * @author Mark Jebri <mark.github@yandex.ru>
 * @since 2.0
 */
class TestCase extends Test
{
    use FixtureTrait;
    /**
     * @var array|string the application configuration that will be used for creating an application instance for each test.
     * You can use a string to represent the file path or path alias of a configuration file.
     * The application configuration array may contain an optional `class` element which specifies the class
     * name of the application instance to be created. By default, a [[\yii\web\Application]] instance will be created.
     */
    public $appConfig = '@tests/codeception/config/unit.php';

    /**
     * Returns the value of an object property.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `$value = $object->property;`.
     * @param string $name the property name
     * @return mixed the property value
     * @throws UnknownPropertyException if the property is not defined
     */
    public function __get($name)
    {
        $fixture = $this->getFixture($name);
        if ($fixture !== null) {
            return $fixture;
        } else {
            throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Calls the named method which is not a class method.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when an unknown method is being invoked.
     * @param string $name the method name
     * @param array $params method parameters
     * @return mixed the method return value
     * @throws InvalidConfigException
     */
    public function __call($name, $params)
    {
        $fixture = $this->getFixture($name);
        if ($fixture instanceof BaseActiveFixture) {
            return $fixture->getModel(reset($params));
        } else {
            throw new UnknownMethodException('Unknown method: ' . get_class($this) . "::$name()");
        }
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function setUp() : void
    {
        parent::setUp();
        $this->mockApplication();
        $this->loadFixtures();
    }

    /**
     * @inheritdoc
     */
    protected function tearDown() : void
    {
        $this->unloadFixtures();
        $this->destroyApplication();
        parent::tearDown();
    }

    /**
     * Mocks up the application instance.
     * @param array $config the configuration that should be used to generate the application instance.
     * If null, [[appConfig]] will be used.
     * @return \yii\web\Application|Application|Object|void the application instance
     * @throws InvalidConfigException if the application configuration is invalid
     */
    protected function mockApplication($config = null)
    {
        if (isset(Yii::$app)) {
            return;
        }
        Yii::$container = new Container();
        $config = $config === null ? $this->appConfig : $config;
        if (is_string($config)) {
            $configFile = Yii::getAlias($config);
            if (!is_file($configFile)) {
                throw new InvalidConfigException("The application configuration file does not exist: $config");
            }
            $config = require($configFile);
        }
        if (is_array($config)) {
            if (!isset($config['class'])) {
                $config['class'] = 'yii\web\Application';
            }
            return Yii::createObject($config);
        } else {
            throw new InvalidConfigException('Please provide a configuration array to mock up an application.');
        }
    }

    /**
     * Destroys the application instance created by [[mockApplication]].
     */
    protected function destroyApplication()
    {
        if (Yii::$app) {
            if (Yii::$app->has('session', true)) {
                Yii::$app->session->close();
            }
            if (Yii::$app->has('db', true)) {
                Yii::$app->db->close();
            }
        }
        Yii::$app = null;
        Yii::$container = new Container();
    }
}
