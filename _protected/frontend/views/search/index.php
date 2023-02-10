<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\Pjax;
use kartik\dialog\Dialog;
use common\widgets\ajax\modal\ModalAjaxWidget;

/**
 * @var yii\web\View $this
 * @var common\models\searches\RequestSearch $searchModel
 * @var yii\data\ActiveDataProvider $dataProvider
 */

$this->params['breadcrumbs'][] = $this->title;

Dialog::widget(['overrideYiiConfirm' => true]);
?>

<div class="search-patient">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">
        <div class="col-lg-12">
            <?= $this->render('_form', ['searchModel' => $searchModel]) ?>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-lg-12">
            <?php $pjax = Pjax::begin(); ?>

                <?php echo ListView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => '<div>{items}</div><br><div class="pagination-wrap">{pager}</div>',
                    'options' => ['class' => 'list-view',],
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => '_item',
                    'summary' => false,
                    'pager' => [
                        'class' => common\widgets\InfinitePager\InfiniteScrollPager::class,
                        'paginationSelector' => '.pagination-wrap',
                        'containerSelector' => '.list-view',
                        'pjaxContainer' => $pjax->id,
                    ],
                ]); ?>

                <?= ModalAjaxWidget::widget([
                    'showBtn' => false,
                    'modalTitle' => Yii::t('app', 'Service Request Form'),
                    'targetId' => 'search_modal_window'
                ])?>

            <?php Pjax::end(); ?>
        </div>
    </div>
    <div class = "clearfix"></div>
</div>
