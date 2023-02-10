<?php

use yii\helpers\Url;
use kartik\dialog\Dialog;
use yii\web\JsExpression;
use common\widgets\ajax\modal\ModalAjaxWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Order */

$this->params['breadcrumbs'][] = $this->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>
<div class="patient-view box box-primary">
    <div class="box-header">

    </div>
    <div class="box-body table-responsive no-padding">
        <?= yii2fullcalendar\yii2fullcalendar::widget([
            'header' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => ''
            ],
            'clientOptions' => [
                'editable' => false,
                'theme' => true,
                'themeSystem' => 'jquery-ui',
                'selectable' => true,
                'eventLimit' => true,
                'selectAllow' => new JSExpression("function(select) {
                    return moment().diff(select.start) <= 0
                }"),
                /*'select' => new JSExpression("function(date, allDay, jsEvent, view) {
                    $.get('/visit/create', {oid: 3}, function(content) {
                        $('.modal-body').html(content);
                    });
                }"),*/
                'eventClick' => new JsExpression("function(event, element, view) {
                    $.get('/provider-calendar/view', {id: event.id, oid: event.resourceId}, function(content) {
                        $('#provider_calendar_modal_window .modal-body').html(content);
                    });
                }"),
                'eventRender' => new JsExpression("function(event, element, view) {
                    var data = event.nonstandard.data ? event.nonstandard.data : null;
                    if(data instanceof Object && data.constructor === Object) {
                        for (const property in data) {
                            element.attr('data-'+property, data[property]);
                        }
                    }
                }"),
                /*'eventAfterRender' => new JsExpression("function(event, element, view) {
                    element.attr('data-target', '#provider_calendar_modal_window');
                    element.attr('data-toggle', 'modal');
                    console.log(event, element, view);
                }"),*/
            ],
            'events' => Url::to(['provider-calendar/event'])
        ])?>
    </div>
</div>

<?= ModalAjaxWidget::widget([
    'showBtn' => false,
    'modalTitle' => 'Visit Details',
    'targetId' => 'provider_calendar_modal_window'
])?>