<?php
namespace common\exceptions;


/**
 * Class VisitNotFoundException
 * @package common\exceptions
 */
class VisitNotFoundException extends VisitException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Visit Not Found Exception';
    }
}