<?php

namespace common\traits;

use Yii;
use yii\base\Exception;
use yii\base\ErrorException;
use common\helpers\ConstHelper;
use common\helpers\FileHelper;

/**
 * Trait FileUploadTrait
 * @package common\helpers
 */
trait FileManagerTrait
{

    /**
     * Calculates the nested prefix directories which will be used to distribute the files on the filesystem for easier management.
     *
     * The current structure is to take the first 3 characters of the filename and create a directory structure 3 levels deep,
     * with 1 character for the first two levels, and 2 hex characters for the third level depending on the value of the
     * third character of the filename.
     *
     * Example: Filename "5c32dc0aae4a83408adce3c5faf5626d" will yield the prefix "5/c/08"
     *
     * @param string $filename The base filename used to build a series of nested directories.
     * @return string The snippet referring to the directory prefix this file.
     */
    protected function calculateDirectoryPrefix($filename)
    {
        $filename_length = strlen($filename);
        $path_components = array();

        for ( $i = 0; ($i < $filename_length && $i < 2 ); $i++ ) {
            $path_components[] = strtolower($filename[$i]);
        }

        if ( $filename_length >= 3 ) {
            $third_character = strtolower($filename[2]);
            if ( $third_character <= '8' ) {
                $path_components[] = '08';
            } else {
                $path_components[] = '9f';
            }
        }

        $path_prefix = implode(DIRECTORY_SEPARATOR, $path_components);

        if( preg_match('/[^a-z\d\/]/', $path_prefix)) {
            $path_prefix = 'other';
        }

        return $path_prefix;
    }

    /**
     * Upload document to the server
     * @param string $configName
     * @return string
     * @throws ErrorException
     * @throws Exception
     */
    public function getFileContentURI($configName)
    {
        if(empty($configName) || empty(Yii::$app->params[$configName]) ){
            throw new ErrorException('File type not specified.');
        }

        $config = Yii::$app->params[$configName];
        if(empty($config['destination_prefix'])){
            throw new ErrorException('Document destination path not specified in the config');
        }

        $rootDirectory = Yii::getAlias($config['destination_prefix']);
        if(!is_dir($rootDirectory)){
            if (!FileHelper::createDirectory($rootDirectory, 0750, true) && !is_dir($rootDirectory)) {
                throw new ErrorException(sprintf('Directory "%s" was not created', $rootDirectory));
            }
        }

        $filename = ConstHelper::uuid();
        $directory_prefix = $this->calculateDirectoryPrefix($filename);
        $pathAlias = FileHelper::normalizePath($config['destination_prefix'] . DIRECTORY_SEPARATOR . $directory_prefix);
        FileHelper::createDirectory(Yii::getAlias($pathAlias), '0750', true);
        return FileHelper::normalizePath($pathAlias . DIRECTORY_SEPARATOR . $filename);
    }

}