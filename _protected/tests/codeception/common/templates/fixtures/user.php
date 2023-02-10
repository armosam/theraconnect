<?php
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
    'username' => $faker->userName,
    'email' => $faker->email,
    'auth_key' => $security->generateRandomString(),
    'password_hash' => $security->generatePasswordHash('password_' . $index),
    'password_reset_token' => $security->generateRandomString() . '_' . time(),
    'created_at' => $dateTime->format('Y-m-d H:i:s'),
    'updated_at' => $dateTime->format('Y-m-d H:i:s'),
    'created_by' => 1,
    'updated_by' => 1,
    'first_name' => $faker->firstName,
    'last_name' => $faker->lastName,
    'gender' => $faker->randomElement(['M', 'F']),
    'phone1' => $faker->phoneNumber,
    'phone2' => $faker->phoneNumber,
    'lat' => $faker->latitude,
    'lng' => $faker->longitude,
    'address' => $faker->streetAddress,
    'city' => $faker->city,
    'state' => 'CA',
    'zip_code' => $faker->postcode,
    'country' => 'United States',
    'timezone' => $faker->timezone,
    'ip_address' => $faker->ipv4,
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
