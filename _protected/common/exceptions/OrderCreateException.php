<?php
namespace common\exceptions;

/**
 * Class OrderCreateException
 * @package common\exceptions
 */
class OrderCreateException extends OrderException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Order Create Exception';
    }
}