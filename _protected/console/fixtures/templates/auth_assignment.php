<?php

use Faker\Provider\Address;
use common\models\User;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

if ($index === 0) {
    $item_name = User::USER_SUPER_ADMIN;
} elseif($index === 1){
    $item_name = User::USER_ADMIN;
} elseif($index === 2){
    $item_name = User::USER_EDITOR;
} else{
    $item_name = $faker->randomElement([
        User::USER_PROVIDER,
        User::USER_CUSTOMER,
    ]);
}

return [
    'item_name' => $item_name,
    'user_id' => $faker->unique()->numberBetween(1, $index+1),
    'created_at' => $faker->unixTime
];