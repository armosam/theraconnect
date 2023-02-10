<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class OrderException
 * @package common\exceptions
 */
class OrderException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Order Exception';
    }
}