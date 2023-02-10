<?php

namespace common\widgets\signature;

use Yii;
use yii\web\View;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use common\models\UserSignature;

class Signature extends InputWidget {

    protected $signature;
    protected $signatureImage;
    protected $attributeId;
    protected $modalId;
    protected $clearBtn;
    protected $saveBtn;
    protected $useExistingBtn;
    protected $existingSignature = null;
    protected $saveSignatureAttributeId = '';

    /** @var bool $allowed This is a flag to allow to modify signature */
    public $allowed = false;

    /** @var string $save_signature_attribute This is a attribute name of external hidden to keep signature for saving */
    public $save_signature_attribute = '';

    /** @var string $signed_by This is a parameter to set name of signing person to show on the signature block */
    public $signed_by = '';

    /**
     * Init widget and all necessary variables
     * @throws InvalidConfigException
     */
    public function init() {
        parent::init();
        $view = $this->getView();
        SignatureAsset::register($view);

        $this->attributeId = $this->hasModel() ? Html::getInputId($this->model, $this->attribute) : $this->getId();
        $this->signature = $this->attributeId . '_signature_div';
        $this->signatureImage = $this->attributeId . '_signature_image';
        $this->modalId = $this->attributeId . '_modal_box';
        $this->clearBtn = $this->attributeId . '_clear_btn';
        $this->saveBtn = $this->attributeId . '_save_btn';
        $this->useExistingBtn = $this->attributeId . '_use_existing_btn';

        if(!Yii::$app->user->isGuest && Yii::$app->user->identity->isProvider){
            $this->existingSignature = UserSignature::getSignature(Yii::$app->user->id);
        }

        if(!empty($this->save_signature_attribute)) {
            $this->saveSignatureAttributeId = ($this->hasModel() ? Html::getInputId($this->model, $this->save_signature_attribute) : $this->save_signature_attribute);
        }
    }

    /**
     * Runs widget
     * @return string|void
     */
    public function run() {
        parent::run();
        $this->renderWidget();
    }

    /**
     * Render and show widget view on the main page
     */
    protected function renderWidget () {
        $content  = '';
        $content .= $this->renderInputHtml('hidden');
        if ($this->allowed) {
            $content .= Html::button(Yii::t('app', 'Sign Here'), ['class' => 'btn btn-default btn-sm', 'data' => ['toggle' => 'modal', 'target' => '#' . $this->modalId]]);
            if (!empty($this->existingSignature) && !empty($this->saveSignatureAttributeId)) {
                $content .= ' or ';
                $content .= Html::button(Yii::t('app', 'Use Stored Signature'), ['id' => $this->useExistingBtn, 'class' => 'btn btn-default btn-sm']);
            }
        } else {
            $content .= Html::Tag('div', '', ['id'=>$this->signature, 'class'=>'signature', 'style' => 'display:none;color:darkblue']);
        }
        $content .= Html::beginTag('div', ['style'=>'margin:10px']);
        $content .= Html::img('', ['id' => $this->signatureImage, 'alt' => $this->model->getAttributeLabel($this->attribute), 'class' => 'img-responsive', 'height' => '100']);
        $content .= Html::endTag('div');

        echo $content;
        $this->renderJs();
    }

    /**
     * Returns modal dialog for signature content
     * @return string
     */
    private function _renderDialog (){
        $content = '';
        $content .= Html::beginTag('div', ['class'=>'modal fade', 'id'=>$this->modalId, 'tabindex'=>'-1', 'role'=>'dialog', 'aria-hidden'=>'true']);
        $content .= Html::beginTag('div', ['class'=>'modal-dialog modal-dialog-centered', 'role'=>'document']);
        $content .= Html::beginTag('div', ['class'=>'modal-content']);
        $content .= Html::beginTag('div', ['class'=>'modal-header']);
        $content .= Html::button('x', ['class'=>'close', 'data-dismiss'=>'modal', 'aria-hidden'=>'true', 'aria-label'=>'Close']);
        $content .= Html::Tag('h3', Html::Tag('span', $this->model->getAttributeLabel($this->attribute), ['class'=>'modal-title']));
        $content .= Html::endTag('div');
        $content .= Html::beginTag('div', ['class'=>'modal-body']);
        $content .= Html::tag('div', $this->signed_by, ['class'=>'text-right']);
        $content .= Html::beginTag('div', ['class'=>'signature-parent']);
        $content .= Html::Tag('div', '', ['id'=>$this->signature, 'class'=>'signature']);
        $content .= Html::endTag('div');
        if(!empty($this->saveSignatureAttributeId)) {
            $content .= Html::Tag('label', Html::checkbox(null, false, ['id' => $this->attribute . '_save_signature', 'class' => 'small', 'style' => 'margin:5px']) . Yii::t('app', 'Save Signature to use later'), ['style' => 'margin:5px;color:green;']);
            $content .= Html::Tag('div', Yii::t('app', 'If you set this checkbox active the signature will be saved for later use.'), ['class' => 'hint-block small']);
        }
        $content .= Html::endTag('div');
        $content .= Html::beginTag('div', ['class'=>'modal-footer']);
        $content .= Html::button('Apply Signature', ['class'=>'btn btn-success', 'id'=>$this->saveBtn, 'data-dismiss'=>'modal']);
        $content .= Html::button('Clear', ['class'=>'btn btn-warning', 'id'=>$this->clearBtn]);
        $content .= Html::button('Close', ['class'=>'btn btn-danger', 'data-dismiss'=>'modal']);
        $content .= Html::endTag('div');
        $content .= Html::endTag('div');
        $content .= Html::endTag('div');
        $content .= Html::endTag('div');
        return $content;
    }

    /**
     * Renders and registers JavaScript on the view
     */
    protected function renderJs ()
    {
        $view = $this->getView();
        $modal = $this->_renderDialog();

        $js = "
        jQuery(function($) {
            $('body').append('".$modal."');
            
            jQuery('#".$this->signature."').jSignature({width: '100%', height: 'auto', color: 'darkblue', 'background-color': 'cornsilk'});
            
            if( jQuery('#".$this->attributeId."').val() === '' ) {
                jQuery('#".$this->signatureImage."').hide();
            } else {
                jQuery('#".$this->signature."').jSignature('setData', jQuery('#".$this->attributeId."').val(), 'base30');
                var ".$this->attribute."_svgData = jQuery('#".$this->signature."').jSignature('getData', 'svgbase64');
                console.log(".$this->attribute."_svgData[1]);
                if( ".$this->attribute."_svgData[1] !== '' ) {
                    jQuery('#".$this->signatureImage."').attr('src', 'data:' + ".$this->attribute."_svgData.join(',')).show();
                }
            }
            
            function ".$this->attribute."_clearSignature () {
                jQuery('#".$this->signature."').jSignature('reset');
                jQuery('#".$this->attributeId."').val('');
                jQuery('#".$this->signatureImage."').hide();
            }
            
            jQuery(document).on('show.bs.modal', '#".$this->modalId."', function (e) {
                jQuery('#".$this->signature."').resize();
                ".$this->attribute."_clearSignature();
            });
            
            jQuery(document).on('hidden.bs.modal', '#".$this->modalId."', function (e) {
                if ($('.modal:visible').length) {
                    $('body').addClass('modal-open');
                } else {
                    $('html').removeClass('modal-open');
                }
            });
        
            jQuery(document).on('click', '#".$this->clearBtn."', function(e){
                ".$this->attribute."_clearSignature();
            });
            
            jQuery(document).on('click', '#".$this->saveBtn."', function(e) {
                var ".$this->attribute."_base30Data = jQuery('#".$this->signature."').jSignature('getData', 'base30');
                var ".$this->attribute."_svgB64Data = jQuery('#".$this->signature."').jSignature('getData', 'svgbase64');
        
                if (".$this->attribute."_base30Data[1] === '' || ".$this->attribute."_svgB64Data[1] === '') {
                    ".$this->attribute."_clearSignature();
                    return false;
                }
                ";
            if(!empty($this->saveSignatureAttributeId)) {
                $js .= "
                    if(jQuery('#".$this->attribute."_save_signature').prop('checked') === true) {
                        jQuery('#".$this->saveSignatureAttributeId."').val(1);
                    } else {
                        jQuery('#".$this->saveSignatureAttributeId."').val(0);
                    }
                ";
            }
            $js .= "
                jQuery('#".$this->signatureImage."').attr('src', 'data:' + ".$this->attribute."_svgB64Data.join(',')).show();
                jQuery('#".$this->attributeId."').val(".$this->attribute."_base30Data.join(','));
                jQuery('#".$this->attributeId."').trigger('change');
            });
            
            jQuery(document).on('click', '#".$this->useExistingBtn."', function(e) {
                
                jQuery('#".$this->signature."').jSignature('setData', '".$this->existingSignature."', 'base30');
                var ".$this->attribute."_svgData = jQuery('#".$this->signature."').jSignature('getData', 'svgbase64');
                if( ".$this->attribute."_svgData[1] !== '' ) {
                    jQuery('#".$this->signatureImage."').attr('src', 'data:' + ".$this->attribute."_svgData.join(',')).show();
                    jQuery('#".$this->attributeId."').val('".$this->existingSignature."');
                    jQuery('#".$this->attributeId."').trigger('change');
                }
            });
        });";

        $view->registerJs(new JsExpression($js), View::POS_READY, 'signature_'.$this->attribute);
    }

    /**
     * Creates and returns SignatureService instance
     * @return SignatureService
     */
    public static function getSignatureService () {
        return new SignatureService();
    }

}
