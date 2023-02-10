<?php
namespace common\exceptions;

/**
 * Class OrderNotFoundException
 * @package common\exceptions
 */
class OrderNotFoundException extends OrderException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Order Not Found Exception';
    }
}