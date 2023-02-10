<?php
namespace frontend\models;

use Yii;
use Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\base\InvalidArgumentException;
use common\models\User;

/**
 * Class representing account activation.
 *
 * @property string $username
 */
class AccountActivation extends Model
{
    /**
     * @var User
     */
    private $_user;

    /**
     * Creates the user object given a token.
     *
     * @param  string $token  Account activation token.
     * @param  array  $config Name-value pairs that will be used to initialize the object properties.
     *                        
     * @throws InvalidArgumentException  If token is empty or not valid.
     */
    public function __construct($token, $config = [])
    {
        if (empty($token) || !is_string($token)) 
        {
            throw new InvalidArgumentException(Yii::t('app', 'Account activation token cannot be blank.'));
        }

        $this->_user = User::findByAccountActivationToken($token);

        if (!$this->_user) 
        {
            throw new InvalidArgumentException(Yii::t('app', 'Wrong account activation token.'));
        }

        parent::__construct($config);
    }

    /**
     * Activates account. It sets status as inactive. Admin approves and activates account
     *
     * @return bool|User Whether the account was activated.
     * @throws Exception
     */
    public function activateAccount()
    {
        $user = $this->_user;
        if(!$user){
            return false;
        }
        $user->setScenario(User::SCENARIO_ACTIVATE_ACCOUNT);
        $user->setAttribute('status', User::USER_STATUS_INACTIVE);
        $user->removeAccountActivationToken();

        if($user->save()){
            return $user;
        }

        return false;
    }

    /**
     * Returns the username of the user who has activated account.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_user->username;
    }

    /**
     * Returns the email of the user who has activated account.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_user->email;
    }

    /**
     * Returns user object how activated account
     *
     * @return User|null|ActiveRecord
     */
    public function getUser()
    {
        return $this->_user;
    }
}
