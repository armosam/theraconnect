<?php
namespace common\exceptions;

use yii\base\Exception;

/**
 * Class ChangeHistoryException
 * @package common\exceptions
 */
class ChangeHistoryException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Change History Exception';
    }
}