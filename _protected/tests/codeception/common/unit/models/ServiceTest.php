<?php

namespace tests\codeception\common\unit\models;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\helpers\ConstHelper;
use common\models\Language;
use common\models\Service;
use tests\codeception\common\unit\DbTestCase;
use tests\codeception\common\fixtures\UserFixture;
use tests\codeception\common\fixtures\UserServiceFixture;
use tests\codeception\common\fixtures\ServiceTranslationFixture;

/**
 * Class ServiceTest
 * @package tests\codeception\common\unit\models
 * @group ServiceTest
 */
class ServiceTest extends DbTestCase
{
    use Specify;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    public function setUp() : void
    {
        parent::setUp();

        Yii::configure(Yii::$app, [
            'components' => [
                'user' => [
                    'class' => 'yii\web\User',
                    'identityClass' => 'common\models\base\UserIdentity',
                ],
            ],
        ]);
    }

    /**
     * Clean up the objects against which you tested.
     */
    public function tearDown() : void
    {
        parent::tearDown();
    }

    public function testAfterSave()
    {
        $model = new Service();
        $languages = Language::find()->all();
        if (!empty($languages)) {
            foreach ($languages as $lang) {
                $model->{'service_name_' . substr($lang->language_code, 0, 2)} = 'Service ' . $lang->language_code;
            }
        }
        $model->setAttributes([
            'service_category_id' => 1,
            'service_fee' => '1000',
            'transportation_fee' => '100',
            'service_frequency' => 1800,
            'service_radius' => 50,
            'experience_at' => '2019-10-10',
            'ordering' => 5,
            'status' => ConstHelper::STATUS_ACTIVE
        ]);
        $model->save();

        $this->specify('after service save', function () use ($model) {
            expect('service translation is not empty', $model->serviceTranslations)->notEmpty();
            foreach ($model->serviceTranslations as $serviceTranslation) {
                expect('service translation is object', $serviceTranslation)->object();
                expect('service translation is object', $serviceTranslation->attributes)->hasKey('language');
                expect('service translation is object', $serviceTranslation->attributes)->hasKey('service_name');
                expect('service translation name is', $serviceTranslation->service_name)->equals('Service ' . $serviceTranslation->language);
            }
        });
    }

    public function testAfterFind()
    {
        $model = Service::findOne([1]);

        $this->specify('after find all languages populated', function () use ($model) {
            expect('Service has valid attribute', $model)->hasAttribute('service_name_en');
            expect('Service has valid attribute', $model)->hasAttribute('service_name_ru');
            expect('Service has valid attribute', $model)->hasAttribute('service_name_hy');

            expect('service name is', $model->service_name_en)->equals('Birth Service');
            expect('service name is', $model->service_name_ru)->equals('Услуга родов');
            expect('service name is', $model->service_name_hy)->equals('Ծննդաբերության ծառայություն');
        });
    }

    public function testDisable()
    {
        $model = Service::findOne([1]);

        $this->specify('disable service', function() use ($model) {
            expect('service status is not passive', $model->status)->notEquals(ConstHelper::STATUS_PASSIVE);
            $model->disable();
            expect('service status is passive', $model->status)->equals(ConstHelper::STATUS_PASSIVE);
        });
    }

    public function testEnable()
    {
        $model = Service::findOne([1]);
        $model->status = ConstHelper::STATUS_PASSIVE;
        $model->save(false);

        $this->specify('enable service', function() use ($model) {
            expect('service status is not passive', $model->status)->notEquals(ConstHelper::STATUS_ACTIVE);
            $model->enable();
            expect('service status is passive', $model->status)->equals(ConstHelper::STATUS_ACTIVE);
        });
    }

    /*public function testServiceAverage()
    {
        $model = Service::findOne([1]);
        $service_fee_avg = 0;
        $transportation_fee_avg = 0;
        $duration_avg = 0;

        foreach ($model->userServices as $userService){
            $service_fee_avg += (float)$userService->service_fee;
            $transportation_fee_avg += (float)$userService->transportation_fee;
            $duration_avg += (int)$userService->service_frequency;
        }
        $service_fee_avg /= count($model->userServices);
        $transportation_fee_avg /= count($model->userServices);
        $duration_avg /= count($model->userServices);

        $this->specify('ensure service_fee average is calculated correctly', function() use($model, $service_fee_avg){
            expect('service_fee average will be calculated base on service', Service::serviceAverage($model->id, 'service_fee') )->equals(number_format($service_fee_avg, 0, '.', ''));
        });

        $this->specify('ensure transportation_fee average is calculated correctly', function() use($model, $transportation_fee_avg){
            expect('transportation_fee average will be calculated base on service', Service::serviceAverage($model->id, 'transportation_fee') )->equals(number_format($transportation_fee_avg, 0, '.', ''));
        });

        $this->specify('ensure service_frequency average is calculated correctly', function() use($model, $duration_avg){
            expect('service_frequency average will be calculated base on service', Service::serviceAverage($model->id, 'service_frequency') )->equals(number_format($duration_avg, 0, '.', ''));
        });
    }*/

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
            'serviceTranslation' => [
                'class' => ServiceTranslationFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/service_translation.php',
            ],
            'userService' => [
                'class' => UserServiceFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_service.php'
            ],
        ];
    }
}
