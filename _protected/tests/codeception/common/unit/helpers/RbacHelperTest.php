<?php

namespace _protected\tests\codeception\common\unit\helpers;

use Yii;
use common\models\User;
use common\rbac\helpers\RbacHelper;
use Codeception\Specify;
use yii\base\InvalidConfigException;
use tests\codeception\common\unit\TestCase;
use tests\codeception\common\fixtures\UserFixture;
use tests\codeception\common\fixtures\RoleFixture;

/**
 * Class RbacHelperTest
 * @group rbackHelper
 * @package _protected\tests\codeception\common\unit\helpers
 */
class RbacHelperTest extends TestCase
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
     * Clean up the objects against which you tested._getMessageFile
     */
    public function tearDown() : void
    {
        Yii::$app->user->logout();
        parent::tearDown();
    }

    public function testAssignRoleToFirstUser()
    {
        User::deleteAll();
        $user = new User();
        $user->username = 'test';
        $user->email = 'test@test.com';
        $user->password_hash = 'Test_Hash';
        $user->auth_key = 'Test_Auth_Key';
        $user->save();

        $this->specify('ensure that RBAC assigns super admin role to the first user', function() use($user){
            $role = User::USER_PROVIDER;
            $assigned_role = RbacHelper::assignRole($user->getId(), $role);
            expect('assigned role for firs user is super admin role', $assigned_role)->equals(User::USER_SUPER_ADMIN);
            expect('assigned role for first user is not specified role')->notEqualsWithDelta($role, 0);
        });
    }

    public function testAssignRoleBySpecifyingRole()
    {
        $this->specify('ensure that RBAC assigns specified role to the user', function(){
            $role = User::USER_PROVIDER;
            expect('assigned role is same as we specified', RbacHelper::assignRole(2, $role))->equals($role);
        });

    }

    public function testAssignRoleByDefault()
    {
        $this->specify('ensure that RBAC assigns customer role to the user by default', function(){
            expect('assigned default role customer when we no role is specified', RbacHelper::assignRole(2))->equals(User::USER_CUSTOMER);
        });
    }

    public function testAssignNotExistingRole()
    {
        $this->specify('ensure that RBAC throws exception when specified role is not existing', function(){
            $this->expectException(yii\base\InvalidArgumentException::class);
            $this->expectExceptionMessage('Role name is wrong.');
            RbacHelper::assignRole(2, 'wrong_role');
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