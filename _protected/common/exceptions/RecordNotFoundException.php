<?php

namespace common\exceptions;

use yii\base\Exception;

/**
 * Class RecordNotFoundException
 * @package common\exceptions
 */
class RecordNotFoundException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Record Not Found Exception';
    }
}