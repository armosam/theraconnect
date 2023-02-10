<?php
namespace common\exceptions;


use yii\base\Exception;

/**
 * Class NoteException
 * @package common\exceptions
 */
class NoteException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Note Exception';
    }
}