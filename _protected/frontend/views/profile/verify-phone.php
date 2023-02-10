<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/** @var string $field */
/** @var \common\models\User $model */

?>

<?php Pjax::begin(['enablePushState' => false]); ?>

<?php $form = ActiveForm::begin([
    'id' => 'verify-phone-number-form',
    //'method' => 'post',
    'enableClientValidation' => true,
    'options' => ['role'=>'form'],
]); ?>
    <fieldset>
        <div class="row">
            <div class="col-sm-12 col-md-10  col-md-offset-1 ">
                <div class="form-group text-right">
                    <?= Html::button(Yii::t('app', 'Resend the verification code again'), [
                        'class' => 'btn btn-default send-verification-code-again',
                        'onclick' => new JsExpression("
                            $('.send-verification-code-again').prop('disabled', true);
                            $.ajax({
                                type: 'POST',
                                cache: false,
                                data: { field: '{$field}' },
                                url: '/profile/send-verification-code-again',
                                success: function(response) {
                                    if(response.error){
                                        $('.send-verification-code-again').prop('disabled', false);
                                        $('#send-verification-code-response').removeClass().addClass('alert alert-danger');
                                        $('#send-verification-code-response').html(response.error);
                                    }else if(response.success){
                                        $('#send-verification-code-response').removeClass().addClass('alert alert-success');
                                        $('#send-verification-code-response').html(response.success);
                                    }
                                },
                                error: function(result) {
                                    $('#send-verification-code-response').removeClass().addClass('alert alert-danger');
                                    $('#send-verification-code-response').html(response.error);
                                }
                            });return false;")
                        ])?>
                </div>
                <p id="send-verification-code-response"></p>
                <div class="form-group">
                    <?php Html::hiddenInput('field', $field)?>
                    <?= $form->field($model, 'verification_code',[
                        'inputOptions' => [
                            'class'=>'form-control',
                            'placeholder' => Yii::t('app', 'Verification Code'),
                        ]])
                    ?>
                </div>
                <div class="form-group text-center">
                    <?= Html::submitButton(Yii::t('app', 'Verify Phone Number'), [
                        'class' => 'btn btn-success',
                        'id'=>'verify-phone_number-button'
                    ]) ?>
                </div>
            </div>
        </div>
    </fieldset>
<?php ActiveForm::end(); ?>

<?php Pjax::end(); ?>
