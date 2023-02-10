<?php

namespace tests\codeception\common\unit\models;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\helpers\ConstHelper;
use common\models\Language;
use common\models\ServiceCategory;
use tests\codeception\common\unit\DbTestCase;
use tests\codeception\common\fixtures\ServiceCategoryTranslationFixture;

/**
 * Class ServiceCategoryTest
 * @package tests\codeception\common\unit\models
 * @group ServiceCategoryTest
 */
class ServiceCategoryTest extends DbTestCase
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

    public function testDisable()
    {
        $model = ServiceCategory::findOne([1]);

        $this->specify('disable service category', function() use ($model) {
            $this->assertNotEquals(ConstHelper::STATUS_PASSIVE, $model->status);
            $model->disable();
            $this->assertEquals(ConstHelper::STATUS_PASSIVE, $model->status);
        });
    }

    public function testEnable()
    {
        $model = ServiceCategory::findOne([1]);
        $model->status = ConstHelper::STATUS_PASSIVE;
        $model->save(false);

        $this->specify('enable service category', function() use ($model) {
            $this->assertNotEquals(ConstHelper::STATUS_ACTIVE, $model->status);
            $model->enable();
            $this->assertEquals(ConstHelper::STATUS_ACTIVE, $model->status);
        });
    }

    public function testAfterSave()
    {
        $model = new ServiceCategory();
        $languages = Language::find()->all();
        if (!empty($languages)) {
            foreach ($languages as $lang) {
                $model->{'category_name_' . substr($lang->language_code, 0, 2)} = 'Category ' . $lang->language_code;
            }
        }
        $model->setAttributes([
            'image1' => 'Photo1',
            'image2' => 'Photo2',
            'ordering' => 2,
            'status' => ConstHelper::STATUS_ACTIVE
        ]);
        $model->save();

        $this->specify('after service category save', function () use ($model) {
            $this->assertNotEmpty($model->serviceCategoryTranslations);
            foreach ($model->serviceCategoryTranslations as $serviceCategoryTranslation) {
                $this->assertIsObject($serviceCategoryTranslation);
                $this->assertArrayHasKey('language', $serviceCategoryTranslation->attributes);
                $this->assertArrayHasKey('category_name', $serviceCategoryTranslation->attributes);
                $this->assertEquals('Category ' . $serviceCategoryTranslation->language, $serviceCategoryTranslation->category_name);
            }
        });
    }

    public function testAfterFind()
    {
        $model = ServiceCategory::findOne([1]);

        $this->specify('after find all languages populated', function () use ($model) {
            $this->assertObjectHasAttribute('category_name_en', $model);
            $this->assertObjectHasAttribute('category_name_ru', $model);
            $this->assertObjectHasAttribute('category_name_hy', $model);

            $this->assertEquals('Services', $model->category_name_en);
            $this->assertEquals('Услуги родов', $model->category_name_ru);
            $this->assertEquals('Ծննդյան ծառայություններ', $model->category_name_hy);
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
            'serviceCategoryTranslation' => [
                'class' => ServiceCategoryTranslationFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/service_category_translation.php',
            ]
        ];
    }
}
