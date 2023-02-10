<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var common\models\Article $model */

?>
<style>
    #articleImage {
        text-align: left;
        width: auto;
    }
    #articleImage img, #articleImage iframe {
        height: 160px;
        min-width: 100px;
        max-width: 160px;
        margin-top: 10px;
    }

    @media screen and (max-width: 767px){
        #articleImage {
            text-align: center;
        }
        #articleImage img, #articleImage iframe {
            height: auto;
            max-height: initial;
            min-width: initial;
            max-width: 98%;
        }
    }
</style>

<div class="row">
    <div class="col-xs-12 col-sm-3" id="articleImage">
        <?php if (!empty($model->embed_content)): ?>
            <?= Html::decode($model->embed_content) ?>
        <?php elseif (!empty($model->file_name) && file_exists(Yii::getAlias(Yii::$app->params['articleImage']['destination_prefix'].$model->file_name))): ?>
            <?= Html::img(Yii::getAlias(Yii::$app->params['articleImage']['relative_prefix'].$model->file_name), ['height' => '160px'])?>
        <?php else: ?>
            <?= Html::img(Yii::getAlias('/uploads/no_image.png'), ['height' => '160px'])?>
        <?php endif;?>
    </div>
    <div class="col-xs-12 col-sm-9">
        <h2><a href=<?= Url::to(['article/item', 'id' => $model->id]) ?>><?= $model->title ?></a></h2>

        <br>

        <p><?= $model->summary ?></p>

        <a class="btn btn-primary btn-xs" href=<?= Url::to(['article/item', 'id' => $model->id]) ?>>
            <?= yii::t('app','Read more'); ?><span class="glyphicon glyphicon-chevron-right"></span>
        </a>

        <br><br>

    </div>
<p class="time text-right">
    <span class="glyphicon glyphicon-time"></span>
    <?= Yii::t('app','Published on').' '.Yii::$app->formatter->asDatetime($model->created_at) ?>
</p>

</div>

    <hr class="article-devider">
