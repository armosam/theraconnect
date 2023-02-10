<?php

namespace frontend\controllers;

use ReflectionClass;
use ReflectionException;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * FrontendController extends Controller and implements the behaviors() method
 * where you can specify the access control ( AC filter + RBAC) for
 * your controllers and their actions.
 */
class FrontendController extends Controller
{
    /**
     * Returns a list of behaviors that this component should behave as.
     * Here we use RBAC in combination with AccessControl filter.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'controllers' => ['article'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'admin'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['article'],
                        'actions' => ['create', 'update', 'admin'],
                        'allow' => true,
                        'roles' => ['editor'],
                    ],
                    [
                        'controllers' => ['user-language'],
                        'actions' => ['index', 'update'],
                        'allow' => true,
                        'roles' => ['provider'],
                    ],
                    [
                        'controllers' => ['user-credential'],
                        'actions' => ['index', 'view', 'update', 'update-ajax', 'document'],
                        'allow' => true,
                        'roles' => ['provider'],
                    ],
                    [
                        'controllers' => ['provider-order'],
                        'actions' => ['index', 'view', 'allow-provider-transfer', 'document'],
                        'allow' => true,
                        'roles' => ['provider'],
                    ],
                    [
                        'controllers' => ['visit'],
                        'actions' => ['index', 'create', 'update', 'note-communication', 'note-discharge-order', 'note-discharge-summary', 'note-eval', 'note-progress', 'note-route-sheet', 'note-supplemental'],
                        'allow' => true,
                        'roles' => ['provider'],
                    ],
                    [
                        'controllers' => ['document'],
                        'actions' => ['note-communication', 'note-discharge-order', 'note-discharge-summary', 'note-eval', 'note-progress', 'note-route-sheet', 'note-supplemental'],
                        'allow' => true,
                        'roles' => ['provider', 'customer'],
                    ],
                    [
                        'controllers' => ['provider-calendar'],
                        'actions' => ['index', 'view', 'event'],
                        'allow' => true,
                        'roles' => ['provider'],
                    ],
                    [
                        'controllers' => ['search'],
                        'actions' => ['index', 'accept'],
                        'allow' => true,
                        'roles' => ['provider'],
                    ],
                    [
                        'controllers' => ['patient-calendar'],
                        'actions' => ['view', 'detail', 'event'],
                        'allow' => true,
                        'roles' => ['customer'],
                    ],
                    [
                        'controllers' => ['patient'],
                        'actions' => ['index', 'view', 'create', 'update', 'enable', 'disable'],
                        'allow' => true,
                        'roles' => ['customer'],
                    ],
                    [
                        'controllers' => ['order'],
                        'actions' => ['index', 'view', 'create', 'update', 'document'],
                        'allow' => true,
                        'roles' => ['customer'],
                    ],
                    [
                        'controllers' => ['user-notification'],
                        'actions' => ['index', 'update'],
                        'allow' => true,
                        'roles' => ['customer'],
                    ],
                    [
                        'controllers' => ['profile'],
                        'actions' => ['index', 'update', 'send-verification-email', 'verify-phone', 'send-verification-code-again', 'dismiss-change'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'controllers' => ['site'],
                        'actions' => ['index', 'logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'controllers' => ['site', 'article', 'join'],
                        'actions' => ['index', 'view', 'avatar', 'about', 'contact', 'terms-of-service', 'privacy-policy', 'service', 'how-it-work', 'service-request', 'verify-email-address', 'application-form', 'success'],
                        'allow' => true
                    ],
                    [
                        'actions' => ['sign-up-customer', 'login', 'reset-password', 'request-password-reset', 'activate-account', 'activate-account-and-set-new-password'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['error', 'captcha'],
                        'allow' => true
                    ],

                ], // rules
            ], // access

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'logout' => ['post'],
                    'send-verification-email' => ['post'],
                    'dismiss-email-change' => ['post'],

                    'send-verification-again' => ['post'],
                    'dismiss-change' => ['post'],
//                    'verify-phone' => ['post']
                ],
            ], // verbs

        ]; // return

    } // behaviors

    /**
     * Declares external actions for the controller.
     *
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'common\components\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Creates an array of models according to given data
     * It is used for tabular data saving process.
     * Data should be array and contain element with $model_name key
     * Like:
     *  $data = [User => [first_name => xxx, last_name => yyy]]
     *
     * @param $modelClass
     * @param $data
     * @return array
     * @throws ReflectionException
     */
    public function createMultipleModelsForData($modelClass, $data)
    {
        $result = [];
        $reflect = new ReflectionClass($modelClass);
        $model_name = $reflect->getShortName();

        if(empty($data[$model_name]) || !is_array($data[$model_name])){
            return $result;
        }

        foreach($data[$model_name] as $index => $attribute){
            $result[$index] = new $modelClass();
        }
        return $result;
    }

} // AppController
