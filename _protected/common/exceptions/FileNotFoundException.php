<?php

namespace common\exceptions;

use yii\base\Exception;

/**
 * Class FileNotFoundException
 * @package common\exceptions
 */
class FileNotFoundException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'File Not Found Exception';
    }
}