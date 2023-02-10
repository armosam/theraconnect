<?php
namespace common\exceptions;

/**
 * Class OrderSubmitException
 * @package common\exceptions
 */
class OrderSubmitException extends OrderException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Order Submit Exception';
    }
}