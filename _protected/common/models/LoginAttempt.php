<?php

namespace common\models;

use Yii;
use yii\db\StaleObjectException;

/**
 * Class LoginAttempt
 * @package common\models
 */
class LoginAttempt extends base\LoginAttempt
{
    /**
     * Checks if failed login attempts exits acceptable limit that set in the config variable {@see countOfAllowedFailedAttempts}
     *
     * @return bool True if failed login attempts exits acceptable limit otherwise false
     */
    public static function doesExitAcceptableLimit()
    {
        $ip = YII_ENV_TEST ? '::1' : Yii::$app->request->userIP;
        $login_attempt = LoginAttempt::find()->byIP($ip)->one();

        if (!empty($login_attempt)) {
            if ($login_attempt->failed_attempts >= Yii::$app->params['countOfAllowedFailedAttempts']){
                return true;
            }
        }
        return false;
    }

    /**
     * Increase failed login attempts and if it exits acceptable limit then return true otherwise false
     *
     * @return bool True if failed attempts exit acceptable limit otherwise false
     */
    public static function doAttemptAndCheckAgain()
    {
        $ip = YII_ENV_TEST ? '::1' : Yii::$app->request->userIP;
        $login_attempt = LoginAttempt::find()->byIP($ip)->one();

        if (empty($login_attempt)) {
            $login_attempt = new LoginAttempt();
            $login_attempt->setAttribute('ip', $ip);
        } else {
            $login_attempt->failed_attempts++;
        }

        if(!$login_attempt->save()){
            Yii::error('Login Attempt not saved in the database.', 'Login_Attempt');
            Yii::debug($login_attempt->getErrors());
        }

        if ($login_attempt->failed_attempts >= Yii::$app->params['countOfAllowedFailedAttempts']){
            return true;
        }
        return false;
    }

    /**
     * Cleans failed attempts information
     * @throws StaleObjectException
     * @throws \Throwable
     */
    public static function clearAttempts()
    {
        $ip = YII_ENV_TEST ? '::1' : Yii::$app->request->userIP;
        $login_attempt = LoginAttempt::find()->byIP($ip)->one();

        if (!empty($login_attempt)) {
            $login_attempt->delete();
        }
    }
}
