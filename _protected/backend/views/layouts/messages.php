<?php

// THIS FILE IS NOT USED ANYWHERE IT JUST KEPT TO BE AS PART OF DESIGN //

/** @var View $this */
/** @var string $directoryAsset */

use yii\web\View;

$directoryAsset = Yii::$app->assetManager->getPublishedUrl('@vendor/almasaeed2010/adminlte/dist');
?>

<a href="#" class="dropdown-toggle" data-toggle="dropdown">
    <i class="fa fa-envelope-o"></i>
    <span class="label label-success">4</span>
</a>
<ul class="dropdown-menu">
    <li class="header">You have 4 messages</li>
    <li>
        <!-- inner menu: contains the actual data -->
        <ul class="menu">
            <li><!-- start message -->
                <a href="#">
                    <div class="pull-left">
                        <img src="<?= Yii::$app->urlManagerToFront->createUrl(['site/avatar', 'id' => Yii::$app->user->id, 'w' => 160]) ?>" class="img-circle"
                             alt="User Image"/>
                    </div>
                    <h4>
                        Support Team
                        <small><i class="fa fa-clock-o"></i> 5 mins</small>
                    </h4>
                    <p>Why not buy a new awesome theme?</p>
                </a>
            </li>
            <!-- end message -->
            <li>
                <a href="#">
                    <div class="pull-left">
                        <img src="<?= $directoryAsset ?>/img/user3-128x128.jpg" class="img-circle"
                             alt="user image"/>
                    </div>
                    <h4>
                        AdminLTE Design Team
                        <small><i class="fa fa-clock-o"></i> 2 hours</small>
                    </h4>
                    <p>Why not buy a new awesome theme?</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <div class="pull-left">
                        <img src="<?= $directoryAsset ?>/img/user4-128x128.jpg" class="img-circle"
                             alt="user image"/>
                    </div>
                    <h4>
                        Developers
                        <small><i class="fa fa-clock-o"></i> Today</small>
                    </h4>
                    <p>Why not buy a new awesome theme?</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <div class="pull-left">
                        <img src="<?= $directoryAsset ?>/img/user3-128x128.jpg" class="img-circle"
                             alt="user image"/>
                    </div>
                    <h4>
                        Sales Department
                        <small><i class="fa fa-clock-o"></i> Yesterday</small>
                    </h4>
                    <p>Why not buy a new awesome theme?</p>
                </a>
            </li>
            <li>
                <a href="#">
                    <div class="pull-left">
                        <img src="<?= $directoryAsset ?>/img/user4-128x128.jpg" class="img-circle"
                             alt="user image"/>
                    </div>
                    <h4>
                        Reviewers
                        <small><i class="fa fa-clock-o"></i> 2 days</small>
                    </h4>
                    <p>Why not buy a new awesome theme?</p>
                </a>
            </li>
        </ul>
    </li>
    <li class="footer"><a href="#">See All Messages</a></li>
</ul>