<?php

use yii\helpers\Url;
use yii\web\JsExpression;
use common\widgets\ajax\modal\ModalAjaxWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Patient */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Patients'), 'url' => ['patient/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="patient-calendar-view box box-primary">
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
                /*'select' => new JSExpression("function(date, allDay, element, view) {
                    $.get('/visit/create', {oid: 3}, function(data) {
                        $('.modal-body').html(data);
                    });
                }"),*/
                'eventClick' => new JsExpression("function(event, element, view) {
                    $.get('detail', {id: event.id, oid: event.resourceId}, function(content) {
                        $('#patient_calendar_modal_window .modal-body').html(content);
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
                    element.attr('data-target', '#patient_calendar_modal_window');
                    element.attr('data-toggle', 'modal');
                    console.log(event, element, view);
                }"),*/
            ],
            'events' => Url::to(['patient-calendar/event', 'pid' => $model->id])
        ])?>
    </div>
</div>

<?= ModalAjaxWidget::widget([
    'showBtn' => false,
    'modalTitle' => 'Visit Details',
    'targetId' => 'patient_calendar_modal_window'
])?>