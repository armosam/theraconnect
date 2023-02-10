<?php

namespace common\components;

use Yii;
use yii\captcha\CaptchaAction as CaptchaActionBase;

/**
 * Class CaptchaAction
 * @package common\components
 */
class CaptchaAction extends CaptchaActionBase {

    /**
     * Validation of captcha when used ajax validation
     * It will return true if ajax validation is on
     * @param string $input
     * @param bool $caseSensitive
     * @return bool
     */
    public function validate($input, $caseSensitive) {
        // Skip validation on AJAX requests, as it expires the captcha.
        if (Yii::$app->request->isAjax) {
            return true;
        }
        return parent::validate($input, $caseSensitive);
    }
}