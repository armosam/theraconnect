<?php

/** @var $this yii\web\View */
/** @var $model common\models\User */
/** @var $terminationModel common\models\User */

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'My Profile'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Edit');

//$pub = Yii::$app->assetManager->publish(Yii::getAlias('@webroot/theme/js/user-profile.js'));
//$this->registerJsFile($pub[1], ['depends' => ['yii\web\JqueryAsset']])
?>
<div class="user-profile-update">

    <div class="col-lg-12 well">

        <?= $this->render('_form', [
            'model' => $model,
            'terminationModel' => $terminationModel,
        ]) ?>

    </div>

</div>