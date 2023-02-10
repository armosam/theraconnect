<?php

namespace common\components;

use Yii;
use mikehaertl\pdftk\Pdf;
use common\helpers\ArrayHelper;

/**
 * This is a PDF Tool Kit Component. It sets java local version in the config.
 * Otherwise by default it will use the pdftk utility installed in the system.
 *
 * Class PDFToolKit
 * @package common\components
 */
class PDFToolKit extends Pdf
{
    /**
     * PDFToolKit constructor.
     * @param null|string $pdf
     * @param array $options
     */
    public function __construct($pdf = null, $options = array())
    {
        $config = [
            'command' => 'java -jar '.Yii::getAlias('@common/components/PDFToolKit/pdftk.jar'),
            // or on most Windows systems:
            // 'command' => 'C:\Program Files (x86)\PDFtk\bin\pdftk.exe',
            //'useExec' => true,  // May help on Windows systems if execution fails
        ];

        $options = ArrayHelper::merge($config, $options);

        parent::__construct($pdf, $options);
    }

}