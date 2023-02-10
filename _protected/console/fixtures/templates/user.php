<?php

use Faker\Provider\Address;

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

$security = Yii::$app->getSecurity();

try {
    $dateTime = new DateTime('now', new DateTimeZone(Yii::$app->timeZone));
} catch (Exception $e) {
    $dateTime = date_create();
}

return [
    'username' => $index == 0 ? 'admin' : preg_replace('/[^a-zA-Z0-9]/', '',  $faker->userName),
    'email' => $faker->email,
    'auth_key' => $security->generateRandomString(),
    'password_hash' => $security->generatePasswordHash('user123'/* . $index*/),
    'password_reset_token' => $security->generateRandomString() . '_' . time(),
    'created_at' => $dateTime->format('Y-m-d H:i:s'),
    'updated_at' => $dateTime->format('Y-m-d H:i:s'),
    'created_by' => 1,
    'updated_by' => 1,
    'title' => null,
    'agency_name' => $faker->words(3, true),
    'first_name' => $index == 0 ? 'System' : $faker->firstName,
    'last_name' => $index == 0 ? 'User' : $faker->lastName,
    'gender' => $faker->randomElement(['M', 'F']),
    'phone1' => $faker->e164PhoneNumber,
    'phone2' => $faker->e164PhoneNumber,
    'lat' => $faker->latitude,
    'lng' => $faker->longitude,
    'address' => $faker->streetAddress,
    'city' => $faker->city,
    'state' => $faker->stateAbbr,
    'zip_code' => Address::postcode(),
    'country' => $faker->country,
    'timezone' => $faker->timezone,
    'ip_address' => $faker->ipv4,
    'status' => $faker->toUpper('A'),
    'note' => $faker->sentence(20),
    'note_email_news_and_promotions' => $faker->toUpper('Y'),
    'note_email_account_updated' => $faker->toUpper('Y'),
    'note_email_order_submitted' => $faker->toUpper('Y'),
    'note_email_order_accepted' => $faker->toUpper('Y'),
    'note_email_order_rejected' => $faker->toUpper('Y'),
    'note_email_order_canceled' => $faker->toUpper('Y'),
    'note_email_rate_service' => $faker->toUpper('Y'),
    'note_email_order_reminder' => $faker->toUpper('Y'),
    'note_sms_news_and_promotions' => $faker->toUpper('N'),
    'note_sms_account_updated' => $faker->toUpper('N'),
    'note_sms_order_submitted' => $faker->toUpper('N'),
    'note_sms_order_accepted' => $faker->toUpper('N'),
    'note_sms_order_rejected' => $faker->toUpper('N'),
    'note_sms_order_canceled' => $faker->toUpper('N'),
    'note_sms_rate_service' => $faker->toUpper('N'),
    'note_sms_order_reminder' => $faker->toUpper('N'),
];
