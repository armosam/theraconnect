<?php

namespace common\models;

use Yii;
use Throwable;
use yii\base\ErrorException;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\web\UploadedFile;
use common\helpers\ConstHelper;

/**
 * Class OrderDocument
 * @package common\models
 */
class OrderDocument extends base\OrderDocument
{
    /**
     * Setting some attributes automatically before an insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     * @throws Throwable
     * @throws ErrorException
     * @throws Exception
     * @throws StaleObjectException
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {

            // Upload document to the server
            if($this->upload_file instanceof UploadedFile && is_readable($this->upload_file->tempName)) {

                $this->setAttribute('file_name', $this->upload_file->name);
                $this->setAttribute('file_size', $this->upload_file->size);
                $this->setAttribute('mime_type', $this->upload_file->type);
                $this->setAttribute('status', ConstHelper::STATUS_ACTIVE);

                $file_content_uri = $this->getFileContentURI('orderDocumentFile');
                $this->setAttribute('file_content_uri', $file_content_uri);

                // cleanup existing documents
                $existing = self::find()->where(['order_id' => $this->order_id, 'document_type' => $this->document_type])->all();
                if(!empty($existing)) {
                    foreach ($existing as $doc) {
                        $path = Yii::getAlias($doc->file_content_uri);
                        if ($doc->delete()) {
                            if (is_file($path) && is_readable($path)) {
                                unlink($path);
                            }
                        }
                    }
                }

                $file = null;
                if(empty(Yii::$app->params['orderDocumentFile']['store_in_database'])){
                    $this->upload_file->saveAs($file_content_uri, $this->deleteTempFile);
                } else{
                    $file = file_get_contents($this->upload_file->tempName);
                }
                $this->setAttribute('file_content', $file);
            }

            return true;
        }

        return false;
    }

    /**
     * Returns the possible values of order document types.
     *
     * @param bool|string $selected
     * @return array|string Array of possible type of document.
     */
    public static function getDocumentTypeList($selected = false)
    {
        $data = [
            null => Yii::t('app', 'Not Selected'),
            self::DOCUMENT_TYPE_INTAKE => Yii::t('app', 'Intake'),
            self::DOCUMENT_TYPE_FORM485 => Yii::t('app', 'Form-485'),
            self::DOCUMENT_TYPE_OTHER => Yii::t('app', 'Other'),
        ];
        if($selected !== false){
            return $data[$selected] ?? $selected;
        }
        return $data;
    }
}