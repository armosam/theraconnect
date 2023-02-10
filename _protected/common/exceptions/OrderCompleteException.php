<?php
namespace common\exceptions;

/**
 * Class OrderCompleteException
 * @package common\exceptions
 */
class OrderCompleteException extends OrderException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Order Complete Exception';
    }
}