<?php

use yii\helpers\Html;
use common\models\Log;
use common\widgets\detail\DetailView;

/* @var $this yii\web\View */
/* @var $model Log */

$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title . ': ' . $model->level_label;
?>
<div class="log-view">

        <?= Html::encode($this->title . ': ' . $model->level_label) ?>

        <p class="text-right back-btn">
            <?= Html::a(Yii::t('app', 'Back'), ['index'], ['class' => 'btn btn-warning']) ?>
        </p>

    <?= DetailView::widget([
        'model' => $model,
        'columnGroupsOptions' => [
            0 => ['width' => '150px']
        ],
        'attributes' => [
            'id',
            'level_label',
            'category',
            'log_time:datetime',
            'prefix:ntext',
            'message:ntext',
        ],
    ]) ?>

</div>
