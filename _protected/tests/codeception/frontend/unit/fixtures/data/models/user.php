<?php

return [
    [
        // this user is active and is member of the site
        'username' => 'member',
        'email' => 'member@example.com',
        'auth_key' => 'iwTNae9t34OmnK6l4vT4IeaTk-YWI2Rv',
        // password is : member123
        'password_hash' => '$2y$13$X1pvTWtc6k8FZg0Z/84Ypejc5It23sJUdWxnu35S8oxBYieWDwhna',
        'password_reset_token' => 't5GU9NwpuGYSfb7FEZMAxqtuz2PkEvv_' . time(),
        'account_activation_token' => null,
        'status' => 'A',
        'created_at' => '2019-07-12 02:00:00',
        'updated_at' => '2019-07-12 02:00:00',
    ],
    [
        // this user has not activated his account
        'username' => 'tester',
        'email' => 'tester@example.com',
        'auth_key' => 'EdKfXrx88weFMV0vIxuTMWKgfK2tS3Lp',
        // password is : test123
        'password_hash' => '$2y$13$L7u5zjs0hMHVuMWaJzt2MuOvsgpkpEqIW0ir9pcMAeK16zDPoHmJu',
        'password_reset_token' => '4BSNyiZNAuxjs5Mty990c47sVrgllIi_' . time(),
        'account_activation_token' => 'xqVzjequF5PNR8jsLDUczmJkxPuQMStl_' . time(),
        'status' => 'N',
        'created_at' => '2019-07-12 02:00:00',
        'updated_at' => '2019-07-12 02:00:00',
    ],
    [
        // this user has not activated his account
        'username' => 'customer',
        'email' => 'customer@example.com',
        'auth_key' => 'EdKfXrx88weFMV0vIxuTMWKgfK2tS3Ld',
        // password is : test123
        'password_hash' => '$2y$13$L7u5zjs0hMHVuMWaJzt2MuOvsgpkpEqIW0ir9pcMAeK16zDPoHmJu',
        'password_reset_token' => '4BSNyiZNAuxjs5Mty990c47sVrdllIi_' . time(),
        'account_activation_token' => 'xqVzjequF5PNR8jsLDUcZmJkxPuQMStl_' . time(),
        'status' => 'N',
        'created_at' => '2019-07-12 02:00:00',
        'updated_at' => '2019-07-12 02:00:00',
    ],
];

