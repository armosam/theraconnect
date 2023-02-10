<?php

use common\models\Article;
use yii\helpers\Html;

/** @var $this yii\web\View */
/** @var array $articles */

$this->params['breadcrumbs'][] = Yii::t('app', 'News');
?>
<div class="article-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-xs-12">
        <?php if (!empty($articles_in_category)): ?>
            <?php foreach ($articles as $category_id => $articles_in_category): ?>
                <?php if (!empty($articles_in_category)): ?>
                    <h2><?= Article::getArticleCategoryList($category_id); ?></h2>
                    <div class="col-lg-offset-1">

                        <?php foreach ($articles_in_category as $article): ?>
                            <div class="col-xs-12" style="padding: 0">
                                <?= $this->render('_index', ['model' => $article]); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?= Yii::t('app', 'Currently we do not have new articles to show.') ?></p>
        <?php endif; ?>
    </div>

</div>