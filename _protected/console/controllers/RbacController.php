<?php

namespace console\controllers;

use Yii;
use yii\helpers\Console;
use yii\console\Controller;
use common\models\User;
use common\rbac\helpers\RbacHelper;
use common\rbac\rules\AuthorRule;
use common\rbac\rules\CredentialOwnerRule;
use common\rbac\rules\NotePrinterRule;
use common\rbac\rules\NoteOwnerRule;
use common\rbac\rules\OrderViewerRule;
use common\rbac\rules\OrderOwnerRule;
use common\rbac\rules\PatientOwnerRule;
use common\rbac\rules\VisitViewerRule;
use common\rbac\rules\VisitOwnerRule;
use common\rbac\rules\ProfileOwnerRule;

/**
 * Creates base rbac authorization data for our application.
 * CURRENTLY THIS STRUCTURE IS USED ONLY FROM FRONTEND APPLICATION AND BACKEND HAS FULL ACCESS FOR ADMIN AND SUPER ADMIN
 * -----------------------------------------------------------------------------
 * Creates 5 roles:
 *
 * - theCreator : you, developer of this site (super admin)
 * - admin      : your direct administrators of this site
 * - editor     : editor of this site who can manage own created news articles
 * - provider   : service provider of this site who can manage own visits, notes, credentials, orders
 * - customer   : agency of this site who can manage own orders, patients and read visits and notes
 *
 * Creates 18 permissions:
 *
 * - manageUser                 : allows admin+ roles to manage user
 * - manageProfile              : allows customer+ roles to manage own profile
 *
 * - manageService              : allows admin+ roles to manage services
 *
 * - manageCredentialType       : allows admin+ roles to manage CredentialType
 *
 * - manageOrder                : allows admin+ roles to manage Order
 * - manageOwnOrder             : allows customer roles to manage own Order
 *
 * - managePatient              : allows admin+ roles to manage Patient
 * - manageOwnPatient           : allows customer roles to manage own Patient
 *
 * - manageVisit                : allows admin+ roles to manage Visit
 * - manageOwnVisit             : allows provider role to manage own Visit
 * - viewOwnVisit               : allows customer+ role to view own Visit
 *
 * - manageNote                 : allows admin+ roles to manage Note
 * - manageOwnNote              : allows provider role to manage own Note
 * - printOwnNote               : allows customer+ role to print own Note
 *
 * - manageCredential           : allows admin+ roles to manage credential
 * - manageOwnCredential        : allows provider roles to manage own credential
 *
 * - createArticle              : allows editor+ roles to create articles
 * - manageOwnArticle           : allows editor+ roles to manage own articles
 * - manageArticle              : allows admin+ roles to manage articles
 * - deleteArticle              : allows admin+ roles to delete articles
 *
 * Creates 7 rule:
 *
 * - AuthorRule             : allows editor+ roles to manage their created content
 * - ProfileOwnerRule       : allows customer+ roles to manage their own content
 * - OrderOwnerRule         : allows customer role to manage their own order
 * - PatientOwnerRule       : allows customer role to manage their own patient
 * - VisitOwnerRule         : allows provider role to manage their own visit
 * - VisitViewerRule        : allows customer+ role to view their own visit
 * - NoteOwnerRule          : allows provider role to manage their own note
 * - NotePrinterRule        : allows customer+ role to print their own note
 * - CredentialOwnerRule    : allows provider role to manage their own credential
 */
class RbacController extends Controller
{
    /**
     * Initializes the RBAC authorization data.
     */
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        //---------- RULES ----------//
        $authorRule = new AuthorRule;
        $auth->add($authorRule);

        $profileOwnerRule = new ProfileOwnerRule;
        $auth->add($profileOwnerRule);

        $orderOwnerRule = new OrderOwnerRule;
        $auth->add($orderOwnerRule);

        $orderViewerRule = new OrderViewerRule;
        $auth->add($orderViewerRule);

        $patientOwnerRule = new PatientOwnerRule;
        $auth->add($patientOwnerRule);

        $visitOwnerRule = new VisitOwnerRule;
        $auth->add($visitOwnerRule);

        $visitViewerRule = new VisitViewerRule;
        $auth->add($visitViewerRule);

        $noteOwnerRule = new NoteOwnerRule;
        $auth->add($noteOwnerRule);

        $notePrinterRule = new NotePrinterRule;
        $auth->add($notePrinterRule);

        $credentialOwnerRule = new CredentialOwnerRule;
        $auth->add($credentialOwnerRule);


        //---------- PERMISSIONS ----------//
        // add "manageUser" permission
        $manageUser = $auth->createPermission('manageUser');
        $manageUser->description = 'Allows admin+ roles to manage User';
        $auth->add($manageUser);

        // add the "manageProfile" permission and associate the ProfileOwnerRule rule with it.
        $manageProfile = $auth->createPermission('manageProfile');
        $manageProfile->description = 'Allow customer+ roles to manage own Profile';
        $manageProfile->ruleName = $profileOwnerRule->name;
        $auth->add($manageProfile);

        // "manageProfile" will be used from "manageUser"
        $auth->addChild($manageProfile, $manageUser);


        // add "manageService" permission
        $manageService = $auth->createPermission('manageService');
        $manageService->description = 'Allows admin+ roles to manage Service';
        $auth->add($manageService);


        // add "manageCredentialType" permission
        $manageCredentialType = $auth->createPermission('manageCredentialType');
        $manageCredentialType->description = 'Allows admin+ roles to manage CredentialType';
        $auth->add($manageCredentialType);


        // add "manageOrder" permission
        $manageOrder = $auth->createPermission('manageOrder');
        $manageOrder->description = 'Allows admin+ roles to manage Order';
        $auth->add($manageOrder);

        // add "viewOwnOrder" permission
        $viewOwnOrder = $auth->createPermission('viewOwnOrder');
        $viewOwnOrder->description = 'Allows customer role of the order and provider assigned to the order to view an Order';
        $viewOwnOrder->ruleName = $orderViewerRule->name;
        $auth->add($viewOwnOrder);

        // "viewOwnOrder" will be used from "manageOrder"
        $auth->addChild($viewOwnOrder, $manageOrder);

        // add "manageOwnOrder" permission
        $manageOwnOrder = $auth->createPermission('manageOwnOrder');
        $manageOwnOrder->description = 'Allows customer role to manage own Order';
        $manageOwnOrder->ruleName = $orderOwnerRule->name;
        $auth->add($manageOwnOrder);

        // "manageOwnOrder" will be used from "manageOrder"
        $auth->addChild($manageOwnOrder, $viewOwnOrder);


        // add "managePatient" permission
        $managePatient = $auth->createPermission('managePatient');
        $managePatient->description = 'Allows admin+ roles to manage Patient';
        $auth->add($managePatient);

        // add "manageOwnPatient" permission
        $manageOwnPatient = $auth->createPermission('manageOwnPatient');
        $manageOwnPatient->description = 'Allows customer role to manage own Patient';
        $manageOwnPatient->ruleName = $patientOwnerRule->name;
        $auth->add($manageOwnPatient);

        // "manageOwnPatient" will be used from $managePatient"
        $auth->addChild($manageOwnPatient, $managePatient);


        // add "manageVisit" permission
        $manageVisit = $auth->createPermission('manageVisit');
        $manageVisit->description = 'Allows admin+ roles to manage Visit';
        $auth->add($manageVisit);

        // add "manageOwnVisit" permission
        $manageOwnVisit = $auth->createPermission('manageOwnVisit');
        $manageOwnVisit->description = 'Allows provider role to manage own Visit';
        $manageOwnVisit->ruleName = $visitOwnerRule->name;
        $auth->add($manageOwnVisit);

        // "manageOwnVisit" will be used from "manageVisit"
        $auth->addChild($manageOwnVisit, $manageVisit);

        // add "viewOwnVisit" permission
        $viewOwnVisit = $auth->createPermission('viewOwnVisit');
        $viewOwnVisit->description = 'Allows customer role of the order and provider assigned to the order to view a Visit';
        $viewOwnVisit->ruleName = $visitViewerRule->name;
        $auth->add($viewOwnVisit);

        // "viewOwnVisit" will be used from "manageVisit"
        $auth->addChild($viewOwnVisit, $manageVisit);


        // add "manageNote" permission
        $manageNote = $auth->createPermission('manageNote');
        $manageNote->description = 'Allows admin+ roles to manage Note';
        $auth->add($manageNote);

        // add "manageOwnNote" permission
        $manageOwnNote = $auth->createPermission('manageOwnNote');
        $manageOwnNote->description = 'Allows provider role to manage own Note';
        $manageOwnNote->ruleName = $noteOwnerRule->name;
        $auth->add($manageOwnNote);

        // "manageOwnNote" will be used from "manageNote"
        $auth->addChild($manageOwnNote, $manageNote);

        // add "printOwnNote" permission
        $printOwnNote = $auth->createPermission('printOwnNote');
        $printOwnNote->description = 'Allows customer role of the order and provider assigned to the order to print a Note';
        $printOwnNote->ruleName = $notePrinterRule->name;
        $auth->add($printOwnNote);

        // "printOwnNote" will be used from "manageNote"
        $auth->addChild($printOwnNote, $manageNote);


        // add "manageCredential" permission
        $manageCredential = $auth->createPermission('manageCredential');
        $manageCredential->description = 'Allows admin+ roles to manage UserCredential';
        $auth->add($manageCredential);

        // add "manageOwnCredential" permission
        $manageOwnCredential = $auth->createPermission('manageOwnCredential');
        $manageOwnCredential->description = 'Allows provider role to manage own UserCredential';
        $manageOwnCredential->ruleName = $credentialOwnerRule->name;
        $auth->add($manageOwnCredential);

        // "manageOwnCredential" will be used from "manageCredential"
        $auth->addChild($manageOwnCredential, $manageCredential);


        // add "createArticle" permission
        $createArticle = $auth->createPermission('createArticle');
        $createArticle->description = 'Allows editor+ roles to create Article';
        $auth->add($createArticle);

        // add "manageArticle" permission
        $manageArticle = $auth->createPermission('manageArticle');
        $manageArticle->description = 'Allows editor+ roles to manage Article';
        $auth->add($manageArticle);

        // add "deleteArticle" permission
        $deleteArticle = $auth->createPermission('deleteArticle');
        $deleteArticle->description = 'Allows admin+ roles to delete Article';
        $auth->add($deleteArticle);

        // add the "manageOwnArticle" permission and associate the rule with it.
        $manageOwnArticle = $auth->createPermission('manageOwnArticle');
        $manageOwnArticle->description = 'Allows editor+ roles to manage own Article';
        $manageOwnArticle->ruleName = $authorRule->name;
        $auth->add($manageOwnArticle);

        // "manageOwnArticle" will be used from "manageArticle"
        $auth->addChild($manageOwnArticle, $manageArticle);



        //---------- ROLES ----------//
        // add "customer" role
        $customer = $auth->createRole('customer');
        $customer->description = 'Registered users, members of this site';
        $auth->add($customer);
        $auth->addChild($customer, $manageProfile);
        $auth->addChild($customer, $manageOwnOrder);
        $auth->addChild($customer, $manageOwnPatient);
        $auth->addChild($customer, $viewOwnVisit);
        $auth->addChild($customer, $printOwnNote);

        // add "provider" role
        // manageOwnService, manageOwnOrder, manageOwnNote, manageOwnCredential, customerManageOwnOrder, providerManageOwnOrder, manageProfile
        $provider = $auth->createRole('provider');
        $provider->description = 'Service provider. They have permissions of customer and more.';
        $auth->add($provider);
        //$auth->addChild($provider, $customer);
        $auth->addChild($provider, $manageProfile);
        $auth->addChild($provider, $viewOwnOrder);
        $auth->addChild($provider, $manageOwnVisit);
        $auth->addChild($provider, $viewOwnVisit);
        $auth->addChild($provider, $manageOwnNote);
        $auth->addChild($provider, $manageOwnCredential);

        // add "editor" role and give this role:
        // createArticle, manageOwnArticle permissions, plus he can update own profile.
        $editor = $auth->createRole('editor');
        $editor->description = 'Editor of news for this application';
        $auth->add($editor);
        $auth->addChild($editor, $manageProfile);
        $auth->addChild($editor, $createArticle);
        $auth->addChild($editor, $manageOwnArticle);

        // add "admin" role and give this role:
        // Currently we do not give any deep access permission to admin and super admin as they have full access from backend application
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator of this application';
        $auth->add($admin);
        //$auth->addChild($admin, $provider);
        /*$auth->addChild($admin, $manageUser);
        $auth->addChild($admin, $manageOrder);
        $auth->addChild($admin, $manageVisit);
        $auth->addChild($admin, $manageNote);
        $auth->addChild($admin, $manageCredential);*/
        $auth->addChild($admin, $manageProfile);
        $auth->addChild($admin, $createArticle);
        $auth->addChild($admin, $manageArticle);
        $auth->addChild($admin, $deleteArticle);
        $auth->addChild($admin, $manageService);
        $auth->addChild($admin, $manageCredentialType);

        // add "theCreator" role ( this is you :) )
        // You can do everything that admin can do plus more (if You decide so)
        $theCreator = $auth->createRole('theCreator');
        $theCreator->description = 'Super Admin of this application';
        $auth->add($theCreator);
        $auth->addChild($theCreator, $admin);

        if ($auth)
        {
            $this->stdout("\nRbac authorization data are installed successfully.\n", Console::FG_GREEN);
        }
    }
}