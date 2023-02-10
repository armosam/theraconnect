<?php
namespace common\exceptions;

/**
 * Class OrderAcceptException
 * @package common\exceptions
 */
class OrderAcceptException extends OrderException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Order Accept Exception';
    }
}