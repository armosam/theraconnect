<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class ServiceException
 * @package common\exceptions
 */
class ServiceException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Service Exception';
    }
}