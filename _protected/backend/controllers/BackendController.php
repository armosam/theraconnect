<?php

namespace backend\controllers;

use ReflectionClass;
use ReflectionException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * BackendController extends Controller and implements the behaviors() method
 * where you can specify the access control ( AC filter + RBAC) for 
 * your controllers and their actions.
 */
class BackendController extends Controller
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
                        'controllers' => ['patient-calendar'],
                        'actions' => ['view', 'detail', 'event'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['user-notification'],
                        'actions' => ['index', 'create', 'view', 'update'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['user-credential'],
                        'actions' => ['index', 'view', 'update', 'update-ajax', 'approve', 'disapprove', 'document'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['patient'],
                        'actions' => ['index', 'view', 'create', 'update', 'enable', 'disable'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['order'],
                        'actions' => ['index', 'create', 'submit', 'complete', 'view', 'document', 'approve-frequency', 'change-provider', 'list-rpt-providers', 'list-pta-providers', 'list-provider-orders', 'list-order-visits'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['note-supplemental', 'note-route-sheet',  'note-progress',  'note-eval',  'note-discharge-summary', 'note-discharge-order', 'note-communication'],
                        'actions' => ['index', 'view', 'create', 'update', 'accept', 'reject'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['document'],
                        'actions' => ['note-communication', 'note-discharge-order', 'note-discharge-summary', 'note-eval', 'note-progress', 'note-route-sheet', 'note-supplemental'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['prospect'],
                        'actions' => ['index', 'view', /*'create', 'update', 'delete',*/ 'accept', 'reject'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['service'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'enable', 'disable'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['credential-type'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'enable', 'disable'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['log', 'log-archive'],
                        'actions' => ['index', 'view', 'search'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['user'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'set-service'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['provider'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'set-service'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['customer'],
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'activate', 'deactivate'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'controllers' => ['site'],
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['error', 'captcha'],
                        'allow' => true,
                    ],
                ], // rules
            ], // access

            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'enable' => ['post'],
                    'disable' => ['post'],
                    'logout' => ['post'],
                ],
            ], // verbs

        ]; // return

    } // behaviors

    /**
     * Before Action event handler
     * @param $action
     * @return bool
     * @throws BadRequestHttpException
     */
    /*public function beforeAction($action)
    {
        //set back url for User detail view
        if(parent::beforeAction($action)){
            if(in_array($this->getRoute(), ['user/view', 'user/index'])
                || in_array($this->getRoute(), ['order/index']) && empty(Yii::$app->request->get())
            ){
                Url::remember();
            }
        }
        return true;
    } //beforeAction*/

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
                'layout' => 'main-simple'
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

}