<?php

namespace tests\codeception\common\unit\models;

use Yii;
use yii\base\InvalidConfigException;
use Codeception\Specify;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\ChangeHistory;
use tests\codeception\common\fixtures\UserFixture;
use tests\codeception\common\fixtures\RoleFixture;
use tests\codeception\common\unit\DbTestCase;

/**
 * Class ServiceTest
 * @package tests\codeception\common\unit\models
 * @group getFullName
 */
class UserTest extends DbTestCase
{
    use Specify;

    /**
     * Create the objects against which you will test.
     * @throws InvalidConfigException
     */
    public function setUp(): void
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
        ChangeHistory::deleteAll();
    }

    /**
     * Clean up the objects against which you tested.
     */
    public function tearDown(): void
    {
        ChangeHistory::deleteAll();
        parent::tearDown();
    }

    public function testGetFullName()
    {
        $this->specify('ensure getFillName method returns correct full name of user', function(){

            $model = User::findOne(['5']);

            expect('getFullName method returns only first and last name', $model->getUserFullName())
                ->equals('Test User');
            expect('getFullName method returns user role name', $model->getUserFullName(true))
                ->equals('Test User [ Customer ]');
            expect('getFullName method returns user role name and email address', $model->getUserFullName(true, true))
                ->equals('Test User (tester@example.com) [ Customer ]');
            expect('getFullName method returns user role, email address and username', $model->getUserFullName(true, true, true))
                ->equals('tester Test User (tester@example.com) [ Customer ]');

            expect('getFullName method returns user role, email address and username', $model->getUserFullName(true, true, true, 4))
                ->equals('provider Provider User (provider@example.com) [ Provider ]');

            expect('getFullName method returns user email address for specified user', $model->getUserFullName(false, true, false, 2))
                ->equals('Admin User (admin@example.com)');
        });
    }

    public function testGetUserGender()
    {
        $this->specify('ensure getGenderList method returns correct data', function(){

            expect('method getGenderList returns array of 2 elements', User::getGenderList())->hasKey(User::USER_GENDER_MALE);
            expect('method getGenderList returns array of 2 elements', User::getGenderList())->hasKey(User::USER_GENDER_FEMALE);
            expect('method getGenderList returns array of 2 elements', User::getGenderList())->array();
            expect('method getGenderList returns array of 2 elements', User::getGenderList())->equals(['' => 'Not Selected', 'F' => 'Female', 'M' => 'Male']);
            expect('method getGenderList returns array of 2 elements', User::getGenderList(User::USER_GENDER_FEMALE))->equals('Female');
            expect('method getGenderList returns array of 2 elements', User::getGenderList(User::USER_GENDER_MALE))->equals('Male');
        });
    }

    public function testValidatePhoneNumber()
    {
        $user = User::findOne([3]);
        Yii::$app->user->login($user);
        $user->setScenario(User::SCENARIO_UPDATE);

        $this->specify('ensure phone number validation is working', function() use($user) {
            $user->setAttribute('phone1', '10000001234');
            expect('see that user save method returns false for number 10000001234', $user->save(true, ['phone1']))->false();
            $user->setAttribute('phone1', '9991111234');
            expect('see that user save method returns false for number 9991111234', $user->save(true, ['phone1']))->false();
            $user->setAttribute('phone1', '818987333');
            expect('see that user save method returns false for number 818987333', $user->save(true, ['phone1']))->false();
            $user->setAttribute('phone1', '+818987333');
            expect('see that user save method returns false for number +818987333', $user->save(true, ['phone1']))->false();
            $user->setAttribute('phone1', '+8189873333');
            expect('see that user save method returns false for number +8189873333', $user->save(true, ['phone1']))->false();
            $user->setAttribute('phone1', '+18189873333');
            expect('see that user save method returns true for number +18189873333', $user->save(true, ['phone1']))->true();
            $user->setAttribute('phone1', '+330612345678');
            expect('see that user save method returns true for number +330612345678', $user->save(true, ['phone1']))->true();
        });
    }

    public function testVerificationCheck()
    {
        $user = User::findOne([3]);
        Yii::$app->user->login($user);

        $this->specify('ensure email verification check works as expected', function() use($user){
            expect('see that returns verified result for email', $user->verificationCheck('email'))->stringContainsString('<a href="mailto:member@example.com">member@example.com</a> <span class="label label-success small">Verified</span>');
            expect('see that logged in as user with id #3', Yii::$app->user->id)->equals($user->id);

            $history = ChangeHistory::saveChanges($user->id, 'email', 'member@example.com', 'xxx@user.com');
            expect('saveChanges method returns ChangeHistory object', $history)->isInstanceOf(ChangeHistory::class);
            expect('see verification token is generated', $history->verification_code)->notNull();
            expect('see verification token is alphanumeric', $history->verification_code)->notNumeric();
            expect('see old value of email', $history->old_value)->equals('member@example.com');
            expect('see new value of email', $history->new_value)->equals('xxx@user.com');
            expect('see status is Yes meaning needs verification', $history->status)->equals(ConstHelper::FLAG_YES);
            expect('see that returns not verified result for email with buttons', $user->verificationCheck('email'))->stringContainsString('<div><a href="mailto:xxx@user.com">xxx@user.com</a> <span class="text text-danger small">Not verified</span><div class="hint-block"><a class="btn btn-primary btn-xs" href="'.Yii::$app->urlManager->createUrl(['profile/send-verification-email']).'" data-method="post" data-confirm="Are you sure you want to resend verification email again?">Verify</a>&nbsp;&nbsp;<a class="btn btn-danger btn-xs" href="'.Yii::$app->urlManager->createUrl(['profile/dismiss-change', 'field' => 'email']).'" data-method="post" data-confirm="Are you sure you want to dismiss changes and restore Email Address?">Dismiss</a></div></div>');
        });

        $this->specify('ensure phone1 verification check works as expected', function() use($user){
            expect('see that returns empty result for phone1', $user->verificationCheck('phone1'))->notEmpty();
            expect('see that returns verified result for phone1', $user->verificationCheck('phone1'))->stringContainsString('(818) 999-8888 <span class="label label-success small">Verified</span>');

            $history = ChangeHistory::saveChanges($user->id, 'phone1', '+18189998888', '0005550000');
            expect('see verification code is generated', $history->verification_code)->notNull();
            expect('see verification code is numeric', $history->verification_code)->numeric();
            expect('see old value of phone1', $history->old_value)->equals('+18189998888');
            expect('see new value of phone1', $history->new_value)->equals('0005550000');
            expect('see status is Yes meaning needs verification', $history->status)->equals(ConstHelper::FLAG_YES);
            expect('see that returns not verified result for phone1 with buttons', $user->verificationCheck('phone1'))->stringContainsString('<div>0005550000 <span class="text text-danger small">Not verified</span><div class="hint-block"><a class="btn btn-primary btn-xs" href="'.Yii::$app->urlManager->createUrl(['profile/verify-phone', 'field' => 'phone1']).'" data-toggle="modal" data-target="#phone_verification_modal_window">Verify</a>&nbsp;&nbsp;<a class="btn btn-danger btn-xs" href="'.Yii::$app->urlManager->createUrl(['profile/dismiss-change', 'field' => 'phone1']).'" data-method="post" data-confirm="Are you sure you want to dismiss changes and restore Primary Phone Number?">Dismiss</a></div></div>');
        });

        $this->specify('ensure phone1 verification check works as expected', function() use($user){
            expect('see that returns empty result for phone2', $user->verificationCheck('phone2'))->isEmpty();

            $user->setAttribute('phone2', '2223334444');
            expect('see that returns verified result for phone2', $user->verificationCheck('phone2'))->stringContainsString('2223334444 <span class="label label-success small">Verified</span>');

            $history = ChangeHistory::saveChanges($user->id, 'phone2', '', '1112221111');
            expect('see verification code is generated', $history->verification_code)->notNull();
            expect('see verification code is numeric', $history->verification_code)->numeric();
            expect('see old value of phone1', $history->old_value)->isEmpty();
            expect('see new value of phone1', $history->new_value)->equals('1112221111');
            expect('see status is Yes meaning needs verification', $history->status)->equals(ConstHelper::FLAG_YES);
            expect('see that returns not verified result for phone2 with buttons', $user->verificationCheck('phone2'))->stringContainsString('<div>1112221111 <span class="text text-danger small">Not verified</span><div class="hint-block"><a class="btn btn-primary btn-xs" href="'.Yii::$app->urlManager->createUrl(['profile/verify-phone', 'field' => 'phone2']).'" data-toggle="modal" data-target="#phone_verification_modal_window">Verify</a>&nbsp;&nbsp;<a class="btn btn-danger btn-xs" href="'.Yii::$app->urlManager->createUrl(['profile/dismiss-change', 'field' => 'phone2']).'" data-method="post" data-confirm="Are you sure you want to dismiss changes and restore Secondary Phone Number?">Dismiss</a></div></div>');
        });
    }

    public function testSaveUpdateScenarioForVerificationNeededAttributes()
    {
        $user = User::findOne([3]);
        Yii::$app->user->login($user);

        $this->specify('ensure that after User::save method an email not saved in User', function() use($user) {
            expect('see that changeHistory has no email record', ChangeHistory::getNotVerified($user->id, 'email'))->null();
            $user->setScenario(User::SCENARIO_UPDATE);
            $user->setAttribute('email', 'xxx@user.com');
            expect('see that user save method returns true', $user->save(false, ['email']))->true();
            expect('see that user model has email not changed', $user->email)->equals('member@example.com');
            expect('see that changeHistory has a new record', ChangeHistory::getNotVerified($user->id, 'email'))->isInstanceOf(ChangeHistory::class);
        });

        $this->specify('ensure that after User::save method a phone1 not saved in User', function() use($user) {
            expect('see that changeHistory has no phone1 record', ChangeHistory::getNotVerified($user->id, 'phone1'))->null();
            $user->setScenario(User::SCENARIO_UPDATE);
            $user->setAttribute('phone1', '10000001234');
            expect('see that user save method returns true', $user->save(false, ['phone1']))->true();
            expect('see that user model has phone1 not changed', $user->phone1)->equals('+18189998888');
            expect('see that changeHistory has a new record', ChangeHistory::getNotVerified($user->id, 'phone1'))->isInstanceOf(ChangeHistory::class);
        });

        $this->specify('ensure that after User::save method a phone2 not saved in User', function() use($user) {
            expect('see that changeHistory has no phone2 record', ChangeHistory::getNotVerified($user->id, 'phone2'))->null();
            $user->setScenario(User::SCENARIO_UPDATE);
            $user->setAttribute('phone2', '10000004444');
            expect('see that user save method returns true', $user->save(false, ['phone2']))->true();
            expect('see that user model has phone2 not changed', $user->phone2)->isEmpty();
            expect('see that changeHistory has a new record', ChangeHistory::getNotVerified($user->id, 'phone2'))->isInstanceOf(ChangeHistory::class);
        });

        $this->specify('ensure that after User::save method an address saved in User', function() use($user) {
            expect('see that changeHistory has no record about address', ChangeHistory::getNotVerified($user->id, 'address'))->null();
            $user->setScenario(User::SCENARIO_UPDATE);
            $user->setAttribute('address', 'Test Address New One');
            expect('see that user save method returns true', $user->save(false, ['address']))->true();
            expect('see that user model has address changed as it is not verification needed', $user->address)->equals('Test Address New One');
            expect('see that changeHistory does not have a new record', ChangeHistory::getNotVerified($user->id, 'address'))->null();
        });
    }

    public function testSaveNonUpdateScenarioForVerificationNeededAttributes()
    {
        $user = User::findOne([3]);
        Yii::$app->user->login($user);

        $this->specify('ensure that after User::save method with Activate scenario an email is saved in the User', function() use($user) {
            expect('see that changeHistory has no record', ChangeHistory::getNotVerified($user->id, 'email'))->null();
            $user->setScenario(User::SCENARIO_ACTIVATE_ACCOUNT);
            $user->setAttribute('email', 'xxx@user.com');
            expect('see that user save method returns true', $user->save(false))->true();
            expect('see that user model has email changed', $user->email)->equals('xxx@user.com');
            expect('see that changeHistory has no new record', ChangeHistory::getNotVerified($user->id, 'email'))->null();
        });

        $this->specify('ensure that after User::save method with Default scenario an email is saved in the User', function() use($user) {
            expect('see that changeHistory has no record', ChangeHistory::getNotVerified($user->id, 'email'))->null();
            $user->setScenario(User::SCENARIO_DEFAULT);
            $user->setAttribute('email', 'zzz@user.com');
            expect('see that user save method returns true', $user->save(false))->true();
            expect('see that user model has email changed', $user->email)->equals('zzz@user.com');
            expect('see that changeHistory has no new record', ChangeHistory::getNotVerified($user->id, 'email'))->null();
        });

        $this->specify('ensure that after User::save method with Default scenario a phone1 is saved in the User', function() use($user) {
            expect('see that changeHistory has no record', ChangeHistory::getNotVerified($user->id, 'phone1'))->null();
            $user->setScenario(User::SCENARIO_DEFAULT);
            $user->setAttribute('phone1', '10000001234');
            expect('see that user save method returns true', $user->save(false))->true();
            expect('see that user model has phone1 changed', $user->phone1)->equals('10000001234');
            expect('see that changeHistory has no new record', ChangeHistory::getNotVerified($user->id, 'phone1'))->null();
        });

        $this->specify('ensure that after User::save method with Default scenario a phone2 is saved in the User', function() use($user) {
            expect('see that changeHistory has no record', ChangeHistory::getNotVerified($user->id, 'phone2'))->null();
            $user->setScenario(User::SCENARIO_DEFAULT);
            $user->setAttribute('phone2', '10000004444');
            expect('see that user save method returns true', $user->save(false))->true();
            expect('see that user model has phone2 changed', $user->phone2)->equals('10000004444');
            expect('see that changeHistory has no new record', ChangeHistory::getNotVerified($user->id, 'phone2'))->null();
        });

        $this->specify('ensure that after User::save method with Default scenario an address saved in User', function() use($user) {
            expect('see that changeHistory has no record about address', ChangeHistory::getNotVerified($user->id, 'address'))->null();
            $user->setScenario(User::SCENARIO_DEFAULT);
            $user->setAttribute('address', 'Test Address New One');
            expect('see that user save method returns true', $user->save(false))->true();
            expect('see that user model has address changed as it is not verification needed', $user->address)->equals('Test Address New One');
            expect('see that changeHistory does not have a new record', ChangeHistory::getNotVerified($user->id, 'address'))->null();
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
            'user' => [
                'class' => UserFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/user.php'
            ],
            'role' => [
                'class' => RoleFixture::class,
                'dataFile' => '@tests/codeception/common/fixtures/data/auth_assignment.php'
            ]
        ];
    }

}