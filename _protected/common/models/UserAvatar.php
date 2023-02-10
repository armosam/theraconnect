<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\imagine\Image;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use Exception;
use PDO;

/**
 * Class UserAvatar
 * @package common\models
 */
class UserAvatar extends base\UserAvatar
{
    /**
     * Returns an ID that can uniquely identify a user avatar.
     *
     * @return int|mixed|string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Setting some attributes by default before insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     * @throws \yii\base\Exception
     */
    public function beforeSave($insert) {

        if (parent::beforeSave($insert)) {
            /*if($insert){

            }*/
            if(!empty($this->upload_file) && $this->upload_file instanceof UploadedFile){
                $mime_type = $this->upload_file->type;
                $file_size = $this->upload_file->size;
                $file_name = Yii::$app->user->id.'-'.Yii::$app->security->generateRandomString(15).'.'.$this->upload_file->extension;
                $this->setAttributes([
                    'mime_type' => $mime_type,
                    'file_size' => $file_size,
                    'file_name' => $file_name,
                ]);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     * @param bool $insert
     * @param array $changedAttributes
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        if(!empty($this->upload_file)) {

            // Uploaded from social network as URL
            $upload_file = $this->upload_file;

            if($this->upload_file instanceof UploadedFile && is_readable($this->upload_file->tempName)) {
                // Uploaded as file
                $upload_file = $this->upload_file->tempName;
            }

            // Crop uploaded image
            $sourceImage = Image::getImagine()->open($upload_file);
            $config = $this->initImageCropFromNamedConfig($sourceImage, 'avatarImage');
            $this->cropImage();

            $file_path = Yii::getAlias($config['destination_prefix'].$this->file_name);
            $sourceImage->save($file_path, ['quality' => $config['quality']]);
            $file = file_get_contents($file_path);

            if($file){
                $id = $this->getId();
                $sql = "UPDATE {{%user_avatar}} SET file_content = :file WHERE id = :id;";
                $q = $this->getDb()->createCommand($sql);
                $q->bindParam(':id', $id);
                $q->bindParam(':file', $file, PDO::PARAM_LOB);
                $q->execute();

                if(!$insert && is_file(Yii::getAlias($config['destination_prefix'].$changedAttributes['file_name']))){
                    unlink(Yii::getAlias($config['destination_prefix'].$changedAttributes['file_name']));
                }
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * Checks if count of uploaded photos not exits allowed amount for given user.
     * If argument is specified it will get it as user id
     * otherwise it will try to get logged user's id
     * @param null|int $id
     * @param bool $isAlarm
     * @return bool
     * @throws NotFoundHttpException
     */
    public function isValidUploadsCount($id = null, $isAlarm = true){
        $user_id = empty($id) ? Yii::$app->user->id : $id;
        if (empty($user_id)){
            throw new NotFoundHttpException('Given User ID not found in the database');
        }

        $defaultLimit = Yii::$app->params['maxGalleryFileUploadLimit'];
        $currentUploadsCount = self::find()->where(['user_id' => $user_id])->count();
        if($currentUploadsCount >= $defaultLimit){
            if ($isAlarm) {
                Yii::$app->session->addFlash('error',
                    Yii::t('app', 'You are allowed to upload maximum {count} photos.', ['count' => $defaultLimit]));
            }
            return false;
        }else{
            return true;
        }

    }

}
