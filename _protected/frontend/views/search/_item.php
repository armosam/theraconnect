<?php

use yii\helpers\Html;
use common\models\User;
use common\models\Order;
use common\helpers\ConstHelper;
use common\models\OrderDocument;

/**
 * @var yii\web\View $this
 * @var Order $model
 */
?>

<div class="col-lg-3 col-md-4 col-sm-6 col-xs-12">
    <div class="panel panel-success">
        <div class="panel-heading">
            <h3 class="panel-title text-center text-capitalize">
                <b><?= $model->patient->patientFullName ?></b>
            </h3>
        </div>
        <div class="panel-body">
            <div class="text-right" style="height: 30px">
                <?php if ($model->isReadyToAccept()): ?>
                    <?= Html::a(Yii::t('app', 'Accept This Patient'), ['search/accept', 'id' => $model->id], [
                        'title' => 'Accept Patient',
                        'class' => 'btn btn-success',
                        'data' => [
                            'toggle' => 'modal',
                            'target' => '#search_modal_window',
                        ],
                    ]) ?>
                <?php else: ?>
                    <?= Html::a(Yii::t('app', 'Accept This Patient'), '#', ['class' => 'btn btn-default btn-sm', 'disabled' => 'disabled']) ?>
                <?php endif; ?>
            </div>

            <div style="height: 55px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Service Required') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px"><?= $model->service_name ?></div>
            </div>

            <div style="height: 55px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Available Documents') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px">
                    <?php if (!empty($model->orderDocuments)): ?>
                        <?php foreach ($model->orderDocuments as $orderDocument): ?>
                            <div class="label label-info"><?= OrderDocument::getDocumentTypeList($orderDocument->document_type) ?></div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="label label-danger"><i class="fa fa-ban"></i> <?= Yii::t('app', 'No document assigned') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <div style="height: 55px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Patient Age') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px"><?= $model->patient->patientAge ?></div>
            </div>
            <div style="height: 70px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Patient Location') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px"><?= $model->patient->patientAddress ?></div>
            </div>

            <div style="height: 55px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Patient Preferences') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px">
                    <?= $model->patient->patientPreferences ?: Yii::t('app', 'No Preferences') ?>
                </div>
            </div>

            <div style="height: 55px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Frequency and Status') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px"><?= !empty($model->service_frequency) ? $model->service_frequency : '' ?> <?php if($model->frequency_status === Order::ORDER_FREQUENCY_STATUS_APPROVED): ?><span class="label label-success">Approved</span><?php else: ?><span class="label label-danger">Pending</span><?php endif; ?></div>
            </div>

            <div style="height: 55px">
                <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Service Rate') ?></b></span></div>
                <div class="text-primary small" style="padding: 2px 5px"><?= !empty($model->service_rate) ? Yii::$app->formatter->asCurrency($model->service_rate) : Html::tag('span', 'Not Set', ['class' => 'label label-danger']) ?></div>
            </div>

            <div style="height: 55px">
                    <div class="text-left"><span class="text-info"><b><?= Yii::t('app', 'Evaluation Status') ?></b></span></div>
                    <div class="text-primary small" style="padding: 2px 5px"><?php if(!empty($model->service_frequency) && !empty($model->evalNotes[0]) && $model->evalNotes[0]->isAccepted()): ?><span class="label label-success">Completed</span><?php else: ?><span class="label label-danger">Pending</span><?php endif; ?></div>
            </div>

            <div class="pull-right"><span class="small text-muted">#<?= $model->order_number ?></span></div>

        </div>
    </div>
</div>