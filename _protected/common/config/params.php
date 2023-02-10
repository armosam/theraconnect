<?php

return [

//------------------------//
// SYSTEM SETTINGS
//------------------------//

    /**
     * Registration Needs Activation.
     *
     * If set to true users will have to activate their accounts using email account activation.
     */
    'rna' => true,

    /**
     * Force Strong Password.
     *
     * If set to true users will have to use passwords with a strength determined by StrengthValidator.
     */
    'fsp' => false,

    /**
     * Set the password reset token expiration time.
     */
    'user.passwordResetTokenExpire' => 3600,

    /**
     * Set the account activation token expiration time.
     */
    'user.accountActivationTokenExpire' => 7200,

    /**
     * Set the list of usernames that we do not want to allow to users to take upon registration or profile change.
     */
    'user.spamNames' => 'admin|superadmin|creator|thecreator|username|superuser|user',

    'galleryImage' => ['width' => 800, 'height' => 600, 'size_mb' => 6, 'destination_prefix' => '@uploads/gallery/', 'relative_prefix' => '/uploads/gallery/', 'ratio' => '4:3', 'quality' => 70],
    'avatarImage' => ['width' => 400, 'height' => 300, 'size_mb' => 6, 'destination_prefix' => '@uploads/avatars/', 'relative_prefix' => '/uploads/avatars/', 'ratio' => '4:3', 'quality' => 70],
    'homePageImage' => ['width' => 400, 'height' => 300, 'size_mb' => 6, 'destination_prefix' => '@uploads/home/', 'relative_prefix' => '/uploads/home/', 'ratio' => '4:3', 'quality' => 70],
    'articleImage' => ['width' => 500, 'height' => 500, 'size_mb' => 6, 'destination_prefix' => '@uploads/articles/', 'relative_prefix' => '/uploads/articles/', 'ratio' => '1:1', 'quality' => 70],

    'credentialFile' => ['store_in_database' => false, 'destination_prefix' => '@uploads/credentials/', 'relative_prefix' => '/uploads/credentials/'],
    'orderDocumentFile' => ['store_in_database' => false, 'destination_prefix' => '@uploads/order_documents/', 'relative_prefix' => '/uploads/order_documents/'],

    'picturePreferredSourceFileSystem' => false,
    'countOfAllowedFailedAttempts' => 6,
    'serviceFeeSliderConfig' => ['default' => 10, 'AMD' => 1000, 'USD' => 1, 'RUB' => 50],
    'maxGalleryFileUploadLimit' => 10,
    'roundFeesToNearest' => 100,
    'similarProvidersPageSize' => 6,
    'searchPatientsPageSize' => 12,

    'service_radius' => 50,
    'orderProviderReminder' => '24H',
    'orderSubmitAfterAccountActivation' => '2D',

    'disableEmailNotifications' => false,
    'disableSMSNotifications' => true,

    'maxLimitOfRejectedOrders' => 3
];
