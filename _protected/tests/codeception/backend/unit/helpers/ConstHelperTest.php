<?php

namespace tests\codeception\backend\unit\helpers;

use Yii;
use Codeception\Specify;
use common\helpers\ConstHelper;
use tests\codeception\backend\unit\TestCase;

/**
 * Class ConstHelperTest
 * @package tests\codeception\frontend\unit\helpers
 * @group constHelper
 */
class ConstHelperTest extends TestCase
{
    use Specify;

    /**
     * Ensure GetImgPath returns correct path
     */
    public function testGetImgPath()
    {
        $this->specify('ensure getImgPath method returns correct path', function(){
            expect('getImgPath method returns relative path to the default logo file', ConstHelper::getImgPath())
                ->equals('/admin/themes/thera/img/logo.png');
            expect('getImgPath method returns full path to the default logo file', ConstHelper::getImgPath(true))
                ->equals('/vagrant/www/public_html/admin/themes/thera/img/logo.png');

            expect('getImgPath method returns relative path to the custom file from theme', ConstHelper::getImgPath(false, 'small_logo.png'))
                ->equals('/admin/themes/thera/img/small_logo.png');
            expect('getImgPath method returns fall path to the custom file from theme', ConstHelper::getImgPath(true, 'small_logo.png'))
                ->equals('/vagrant/www/public_html/admin/themes/thera/img/small_logo.png');

            expect('getImgPath method returns relative path to the custom file', ConstHelper::getImgPath(false, 'test.png'))
                ->equals('/uploads/no_image.png');
            expect('getImgPath method returns relative path to the default logo file', ConstHelper::getImgPath(true, 'test.png'))
                ->equals('/vagrant/www/public_html/uploads/no_image.png');
        });
    }

    /**
     * Ensure @webroot, @webroot/theme, @theme aliases returns correct path
     */
    public function testBackendAliases()
    {
        $this->specify('ensure backend aliases return correct paths' , function(){
            expect('alias @webroot returns correct path', Yii::getAlias('@webroot'))
                ->equals('/vagrant/www/public_html/admin');
            expect('alias @webroot/theme returns correct full path for current themes', Yii::getAlias('@webroot/theme'))
                ->equals('/vagrant/www/public_html/admin/themes/thera');
            expect('alias @theme returns correct relative path for current themes', Yii::getAlias('@theme'))
                ->equals('/admin/themes/thera');
            expect('baseUrl of theme returns correct relative path for current themes', Yii::$app->view->theme->baseUrl)
                ->equals('/admin/themes/thera');
            expect('alias @theme equal to baseUrl of theme', Yii::$app->view->theme->baseUrl)
                ->equals(Yii::getAlias('@theme'));
        });
    }

}