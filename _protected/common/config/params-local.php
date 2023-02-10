<?php

return [
    'geoIPDataPath' => '/usr/local/share/geoIP/GeoLite2-City.mmdb',
    'defaultMapSize' => ['width'=>600, 'height'=>400],
    'accountCreatedEmailNotification' => 'Y',
    'accountCreatedSmsNotification' => 'N',
    'accountCreatedByAdminEmailNotification' => 'Y',
    'accountCreatedByAdminSmsNotification' => 'N',

    /**
     * Email used in Bcc of contact page only.
     */
    'adminEmail' => 'thera@gmail.com',

    /**
     * This email address used in the replyTo of system messages or in contact form
     * Because currently GSuit is not used and @Connect.com addresses not exist
     * So as from address we use fake address from @Connect.com domain and for replyTo we use existing supportEmail
     */
    'supportEmail' => 'Connect@gmail.com',

    /**
     * This email address is fake address currently and represents @Connect.com domain.
     * It used in from address of system emails as we use sendgrid transport
     */
    'fromEmailAddress' => 'support@Connect.com',

    /**
     * All emails will be BCC to this address. Could be comma separated values
     * If this parameter is empty then bcc emails won't be sent.
     */
    'systemNotificationEmailAddress' => '123@gmail.com',

    'facebook_app_id' => 'api id here',
    'googleMapsApiKey' => '',
    'googleMapsLibraries' => '',
    'googleMapsLanguage' => '',
];
