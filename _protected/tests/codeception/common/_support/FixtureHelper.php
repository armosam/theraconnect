<?php

namespace tests\codeception\common\_support;

use yii\test\InitDbFixture;
use yii\test\FixtureTrait;
use Codeception\Module;
use Codeception\Event\TestEvent;
use tests\codeception\common\fixtures\UserFixture;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\fixtures\UserAvatarFixture;
use tests\codeception\common\fixtures\UserRatingFixture;
use tests\codeception\common\fixtures\UserServiceFixture;
use tests\codeception\common\fixtures\UserCredentialFixture;
use tests\codeception\common\fixtures\UserLanguageFixture;
use tests\codeception\common\fixtures\PatientFixture;

/**
 * This helper is used to populate the database with needed fixtures before any tests are run.
 * In this example, the database is populated with the demo login user, which is used in acceptance
 * and functional tests.  All fixtures will be loaded before the suite is started and unloaded after it
 * completes.
 */
class FixtureHelper extends Module
{
    /**
     * Redeclare visibility because codeception includes all public methods that do not start with "_"
     * and are not excluded by module settings, in actor class.
     */
    use FixtureTrait {
        loadFixtures as public;
        fixtures as public;
        globalFixtures as public;
        createFixtures as public;
        unloadFixtures as protected;
        getFixtures as protected;
        getFixture as protected;
    }
    /**
     * Method called before any suite tests run.
     * Loads fixtures to use in acceptance and functional tests.
     * @param array $settings
     */
    public function _beforeSuite($settings = [])
    {
        //$this->initFixtures();
    }
    /**
     * Method is called after all suite tests run
     */
    public function _afterSuite()
    {
        $this->unloadFixtures();
    }
    /**
     * Methods is called before each test
     * This make tests slower but we are sure each test gets new data
     * @TODO Could be improved to be faster and smarter
     * @param  TestEvent $event $event
     */
    public function _before($event)
    {
        $this->initFixtures();
    }
    /**
     * @inheritdoc
     */
    public function globalFixtures()
    {
        return [
            InitDbFixture::class,
        ];
    }
    /**
     * @inheritdoc
     */
    public function fixtures()
    {
        return [
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user.php',
            ],
            'role' => [
                'class' => RoleFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/auth_assignment.php',
            ],
            'user_avatar' => [
                'class' => UserAvatarFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_avatar.php',
            ],
            'user_rating' => [
                'class' => UserRatingFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_rating.php',
            ],
            'user_service' => [
                'class' => UserServiceFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_service.php',
            ],
            'user_qualification' => [
                'class' => UserCredentialFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_qualification.php',
            ],
            'user_language' => [
                'class' => UserLanguageFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user_language.php',
            ],
            'order' => [
                'class' => PatientFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/order.php',
            ]
        ];
    }
}