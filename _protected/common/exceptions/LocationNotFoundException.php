<?php

namespace common\exceptions;

use yii\base\Exception;

/**
 * Class LocationNotFoundException
 * @package common\exceptions
 */
class LocationNotFoundException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Location Not Found Exception';
    }
}