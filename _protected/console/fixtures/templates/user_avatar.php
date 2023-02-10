<?php

/**
 * @var $faker \Faker\Generator
 * @var $index integer
 */

use GuzzleHttp\Psr7\MimeType;

try {
    $dateTime = new DateTime('now', new DateTimeZone(Yii::$app->timeZone));
} catch (Exception $e) {
    $dateTime = date_create();
}

$file = $faker->image(Yii::getAlias('@console/fixtures/data/avatars'), Yii::$app->params['avatarImage']['width'], Yii::$app->params['avatarImage']['height'], null, false);
$file_full_path = Yii::getAlias('@console/fixtures/data/avatars/'.$file);
$file_name = ($index).'-'.$faker->word.'.'.pathinfo($file_full_path, PATHINFO_EXTENSION);

return [
    'id' => $index,
    'user_id' => $index,
    'mime_type' => MimeType::fromFilename($file_full_path),
    'file_size' => filesize($file_full_path),
    'file_name' => $file_name,
    'file_content' => '@console/fixtures/data/avatars/' . $file,
    'created_at' => $dateTime->format('Y-m-d H:i:s'),
    'updated_at' => $dateTime->format('Y-m-d H:i:s'),
    'created_by' => $index,
    'updated_by' => $index,
    'status' => 'A',
];