<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var $this yii\web\View */
/** @var $model common\models\Article */

$this->registerMetaTag(['property'=>'og:image', 'content' => Url::to([Yii::$app->params['articleImage']['relative_prefix'].$model->file_name, 'w' => 350], true)]);
$this->registerMetaTag(['property'=>'og:url', 'content' => Url::to(['@web/article/item', 'id'=>$model->id, '_' => time()], true)]);
$this->registerMetaTag(['property'=>'og:type', 'content' => 'website']);
$this->registerMetaTag(['property'=>'og:title', 'content' => $model->title]);
$this->registerMetaTag(['property'=>'og:description', 'content' => $model->summary]);
//$this->registerMetaTag(['property'=>'fb:app_id', 'content'=>Yii::$app->params['facebook_app_id']]);

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'News'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerMetaTag([
    'name' => 'description',
    'content' => yii::t('app', 'News'). ': ' . Html::decode($model->summary)
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => str_ireplace(' ', ',', Html::decode($model->summary))
]);
?>
<style>
    @media screen and (max-width: 767px){
        .panel-body {
            overflow-x: hidden;
        }
        .panel-body iframe {
            width: 100%;
        }
        .panel-body .textbox {
            margin: 10px !important;
        }
    }
</style>

<div class="article-item">

<article class="panel panel-default">
    <header class="panel-heading">
        <h2 class="text-muted text-center"><?= Html::decode($model->title)?></h2>
    </header>
    <div class="panel-body">
        <div class="row" style="text-align: center; margin: 10px 0;">
            <h5><?= Html::decode($model->summary)?></h5>
        </div>
        <div class="row">
            <div class="col-sx-12" style="text-align: center;">
                <?php if (!empty($model->embed_content)): ?>
                    <?= Html::decode($model->embed_content) ?>
                <?php elseif (!empty($model->file_name) && file_exists(Yii::getAlias(Yii::$app->params['articleImage']['destination_prefix'].$model->file_name))): ?>
                    <img class="img-responsive center-block" src="<?= Yii::getAlias(Yii::$app->params['articleImage']['relative_prefix'].$model->file_name)?>" alt="<?=$model->file_name ?>"/>
                <?php else: ?>
                    <img class="img-responsive center-block" src="<?= Yii::getAlias('/uploads/no_image.png') ?>" alt="No picture available"/>
                <?php endif;?>
            </div>
        </div>
        <div class="row">
            <div class="col-sx-12">
                <div class="textbox" style="word-wrap: break-word;margin: 10px 0">
                    <?= Html::decode($model->content) ?>
                </div>
            </div>
        </div>
    </div>
</article>

</div>