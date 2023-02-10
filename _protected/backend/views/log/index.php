<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use common\models\Log;
use common\widgets\grid\GridView;
use common\models\searches\LogSearch;

/* @var $this yii\web\View */
/* @var $searchModel LogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $levels array */
/* @var $categories array */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="log-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $messagesModalContent = []; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'level',
                'value' => function (Log $model) {
                    return $model->getLevel_label();
                },
                'filter' => $levels,
                'options' => ['width' => '8%'],
            ],
            [
                'attribute' => 'category',
                'filter' => Select2::widget([
                    'data' => $categories,
                    'model' => $searchModel,
                    'attribute' => 'category',
                    'options' => ['placeholder' => 'Select...'],
                    'pluginOptions' => [
                        'allowClear' => true,
                        'dropdownAutoWidth' => true,
                    ],
                ]),
            ],
            [
                'attribute' => 'log_time',
                'format' => 'datetime',
                'filter' => DateRangePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'log_time_search',
                    'convertFormat' => true,
                    'pluginOptions' => [
                        'timePicker' => false,
                        'timePickerIncrement' => 1,
                        'locale' => ['format' => 'Y-m-d H:i:s'],
                    ],
                ]),
            ],
            [
                'attribute' => 'message',
                'format' => 'raw',
                'options' => ['width' => '55%'],
                'value' => function (Log $model) use (&$messagesModalContent) {

                    $messagesModalContent[$model->id] = $model->message;

                    $messageToShow = str_replace("\n\n", '', $model->message);
                    $messageToShow = str_replace("\n", '; ', $messageToShow);

                    if (strlen($messageToShow) > Log::MAX_MESSAGE_LENGTH) {
                        $messageToShow = substr($messageToShow, 0, Log::MAX_MESSAGE_LENGTH) . '...';
                    }

                    return Html::tag('span', $messageToShow, [
                        'data' => [
                            'toggle' => 'modal',
                            'target' => '#' . $model->id,
                        ],
                        'class' => 'mouse-pointer',
                    ]);
                },
            ],

            [ // buttons
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'options' => ['width' => '4%'],
            ],
        ],
    ]); ?>

    <?php
    foreach ($messagesModalContent as $messageId => $messageContent) {
        Modal::begin([

            'id' => $messageId,
            'header' => '<h3>Details</h3>',
            'options' => ['class' => 'ajax-modal-wrap'],
        ]);

        $afterHtmlspecialchars = htmlspecialchars($messageContent);
        echo '<div class="break-word-all">' . nl2br($afterHtmlspecialchars) . '</div>';

        Modal::end();
    }
    ?>

</div>
