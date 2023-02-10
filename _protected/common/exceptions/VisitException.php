<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class VisitException
 * @package common\exceptions
 */
class VisitException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Visit Exception';
    }
}