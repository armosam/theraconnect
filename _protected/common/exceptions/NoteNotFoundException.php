<?php
namespace common\exceptions;

/**
 * Class NoteNotFoundException
 * @package common\exceptions
 */
class NoteNotFoundException extends NoteException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Note Not Found Exception';
    }
}