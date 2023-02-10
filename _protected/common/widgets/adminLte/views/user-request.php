<?php

/** @var View $this */
/** @var Order[] $orders */

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use common\models\Order;
use common\helpers\CssHelper;

?>

<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-envelope-o"></i>
    <span class="label label-success"><?= count($orders)?></span>
</a>
<ul class="dropdown-menu">
    <li class="header"><?= Yii::t('app', 'You have {n} active requests', ['n' => count($orders)])?></li>
    <li>
        <!-- inner menu: contains the actual data -->
        <ul class="menu">
            <?php foreach($orders as $order): ?>
            <li><!-- start message -->
                <?= Html::beginTag('a', ['href' => Url::to(['/order/view', 'id'=>$order->id])]) ?>
                    <div class="pull-left">
                        <img src="<?= Yii::$app->urlManagerToFront->createUrl(['site/avatar', 'id' => $order->provider->id, 'w' => 160]) ?>" class="img-circle" alt="<?= $order->provider->getUserFullName()?>"/>
                    </div>
                    <h4>
                        <small><i class="fa fa-clock-o"></i> <?= Yii::t('app', '{age}', ['age' => Yii::$app->formatter->asRelativeTime($order->updated_at)])?></small><br>
                        <?= $order->provider->getUserFullName() ?>
                    </h4>
                    <p><?= $order->service_name ?></p>
                    <small class="<?= CssHelper::orderStatusCss($order->status) ?>"><?= Order::orderStatuses($order->status) ?></small>
                <?= Html::endTag('a')?>
            </li>
            <!-- end message -->
            <?php endforeach; ?>
        </ul>
    </li>
    <li class="footer"><?= Html::a(Yii::t('app', 'See All Requests'), ['/order/index']) ?></a></li>
</ul>