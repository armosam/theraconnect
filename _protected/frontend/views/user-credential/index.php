<?php

use yii\helpers\Html;
use yii\widgets\MaskedInput;
use kartik\dialog\Dialog;
use common\helpers\CssHelper;
use common\models\UserCredential;
use kartik\grid\GridView;
use kartik\editable\Editable;
use kartik\grid\EditableColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\searches\UserCredentialSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="user-credential-index box box-primary">
    <div class="box-header with-border">
        <p class="text-right">
            <?php // Html::a(Yii::t('app', 'Add New Credential'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}",
            'resizableColumns' => false,
            'responsiveWrap' => false,
            'responsive' => true,
            'export' => false,
            'pjax' => true,
            'pjaxSettings' => [
                'neverTimeout' => true,
                'options' => [
                    'id' => Yii::$app->controller->id
                ]
            ],
            'columns' => [
                [
                    'class' => 'yii\grid\SerialColumn',
                    'options' =>['style' => 'width:5%; white-space: normal;'],
                    'contentOptions'=>['class'=>'kv-align-middle text-center'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'credential_type_id',
                    'filter' => UserCredential::credentialTypes(),
                    'value' => function ($data) {
                        /** @var UserCredential $data */
                        return $data->credentialType->credential_type_name;
                    },
                    'options' => ['style' => 'width:22%; white-space: normal;'],
                    'contentOptions' => ['class'=>'kv-align-middle'],
                    'headerOptions' => ['class' => 'small text-center']
                ],
                [
                    'attribute'=>'assigned_number',
                    'class' => EditableColumn::class,
                    'editableOptions' => [
                        'header' => Yii::t('app', 'Credential Number'),
                        'asPopover' => true,
                        'size' => 'md',
                        'inputType' => Editable::INPUT_TEXT,
                        'formOptions'=>['action' => ['user-credential/update-ajax']],
                        'options' => [
                            'placeholder' => Yii::t('app', 'Credential Number'),
                            'value' => ''
                        ],
                    ],
                    'value' => function ($data) {
                        /** @var UserCredential $data */
                        return empty($data->assigned_number) ? null : '****'.substr($data->assigned_number, -4);
                    },
                    'options' => ['style' => 'width:15%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'expire_date',
                    'format' => 'date',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'expire_date',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'class' => EditableColumn::class,
                    'editableOptions' => [
                        'header' => Yii::t('app', 'Expiration Date'),
                        'asPopover' => true,
                        'size' => 'md',
                        'inputType' => Editable::INPUT_WIDGET,
                        'widgetClass' => MaskedInput::class,
                        'formOptions'=>['action' => ['user-credential/update-ajax']],
                        'options' => [
                            'options' => [
                                'class' => 'form-control kv-editable-input',
                                'value' => ''
                            ],
                            'clientOptions' => [
                                'alias' => 'mm/dd/yyyy',
                                'placeholder' => '__/__/____',
                                'separator' => '/'
                            ]
                        ],
                    ],
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute'=>'upload_file',
                    'class' => EditableColumn::class,
                    'editableOptions' => [
                        'header'=> Yii::t('app','Credential Attachment'),
                        'asPopover' => true,
                        'size'=>'md',
                        'inputType' => Editable::INPUT_FILE,
                        'formOptions' => ['action' => ['user-credential/update-ajax']],
                        'options' => [
                            'class' => 'form-control kv-editable-input',
                            'value' => ''
                        ],
                    ],
                    'value' => function ($data) {
                        /** @var UserCredential $data */
                        return empty($data->file_name) ? null : Yii::t('app','Uploaded');
                    },
                    'options' =>['style' => 'width:13%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle text-center hidden-xs'],
                    'headerOptions' => ['class' => 'small text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute' => 'approved_at',
                    'format' => 'datetime',
                    'filter' => MaskedInput::widget([
                        'model' => $searchModel,
                        'attribute' => 'approved_at',
                        'mask' => '99/99/9999',
                        'clientOptions' => [
                            'alias' => 'mm/dd/yyyy',
                            'placeholder' => '__/__/____',
                            'separator' => '/'
                        ]
                    ]),
                    'options' =>['style' => 'width:17%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle hidden-xs'],
                    'headerOptions' => ['class' => 'small text-center hidden-xs'],
                    'filterOptions' => ['class' => 'hidden-xs']
                ],
                [
                    'attribute'=>'status',
                    'filter' => UserCredential::credentialStatuses(),
                    'value' => function ($data) {
                        return UserCredential::credentialStatuses($data->status);
                    },
                    'contentOptions'=>function($model, $key, $index, $column) {
                        return ['class'=>CssHelper::statusCss($model->status) . ' kv-align-middle text-center'];
                    },
                    'options' =>['style' => 'width:10%; white-space: normal;'],
                    'headerOptions' => ['class' => 'small text-center']
                ],

                [ // buttons
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('app', 'Actions'),
                    'options' =>['style' => 'width:8%; white-space: normal;'],
                    'contentOptions' => ['class' => 'kv-align-middle'],
                    'headerOptions' => ['class' => 'small text-center'],
                    'template' => '{view}&nbsp;&nbsp;{update}&nbsp;&nbsp;{document}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'View Record'),
                                'class'=>'glyphicon glyphicon-eye-open']);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'Manage Record'),
                                'class'=>'glyphicon glyphicon-pencil']);
                        },
                        /*'delete' => function ($url, $model, $key) {
                            return Html::a('', $url, ['title'=>Yii::t('app', 'Delete Record'),
                                'class'=>'glyphicon glyphicon-trash',
                                'data' => [
                                    'confirm' => Yii::t('app', 'Are you sure you want to delete this record?'),
                                    'method' => 'post'
                                ]
                            ]);
                        },*/
                        'document' => function ($url, $model, $key) {
                            if (!empty($model->file_name)) {
                                return Html::a('', $url, ['title' => Yii::t('app', 'Print Document'),
                                    'class' => 'glyphicon glyphicon-print', 'style' => 'margin:0 5px;color:#3c8dbc', 'target' => '_blank',
                                    'data' => ['pjax' => 0]
                                ]);
                            }
                            return '';
                        },
                    ],
                ], // ActionColumn
            ],
        ]) ?>
    </div>
</div>
