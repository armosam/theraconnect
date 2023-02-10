<?php

use yii\helpers\Url;
use yii\helpers\Html;

?>

<!-- ======= Footer ======= -->
<footer id="footer">
    <div class="footer-top">
        <div class="container">
            <div class="row">

                <div class="col-lg-4 col-md-6 col-sm-12">
                    <div class="footer-info">
                        <?= Html::beginTag('a', ['href' => Yii::$app->homeUrl]) ?>
                        <?= Html::img( $this->theme->getUrl('img/small_logo.png'), ['alt' => 'Logo', 'class' => 'img-fluid', 'width' => 50]) ?>
                        <?= Html::beginTag('h3', ['class'=> 'd-none d-sm-inline']) . Yii::t('app', Yii::$app->name) . Html::endTag('h3') ?>
                        <?= Html::endTag('a') ?>
                        <p>
                            155 N Lake Ave, 8th Floor, Suite #839 <br>
                            Pasadena, CA 91101, USA<br>
                            <strong><?= Yii::t('app', 'Phone')?>:</strong> +1 (626) 415-8880<br>
                            <strong><?= Yii::t('app', 'FAX:')?>:</strong> +1 (626) 628-2188<br>
                            <strong><?= Yii::t('app', 'Email')?>:</strong> <?= Html::a('Connect@gmail.com', ['site/contact'])?><br>
                        </p>
                        <div class="social-links mt-3">
                            <a href="https://twitter.com/Connect" class="twitter"><i class="bx bxl-twitter"></i></a>
                            <a href="https://www.facebook.com/Connect" class="facebook"><i class="bx bxl-facebook"></i></a>
                            <a href="https://www.instagram.com/Connect" class="instagram"><i class="bx bxl-instagram"></i></a>
                            <!--a href="#" class="google-plus"><i class="bx bxl-skype"></i></a-->
                            <a href="#" class="linkedin"><i class="bx bxl-linkedin"></i></a>
                        </div>
                    </div>
                </div>
                <?php if(Yii::$app->user->isGuest): ?>

                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4><?= Yii::t('app', 'How to?')?></h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'How it works'), ['site/how-it-work'])?></li>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'How to Interview'), ['#'])?></li>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'How to Hire'), ['#'])?></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4><?= Yii::t('app', 'Useful Links')?></h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'About Us'), ['site/about'])?></li>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Contact Us'), ['site/contact'])?></li>
                        </ul>
                    </div>

                <?php else: ?>

                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4><?= Yii::t('app', 'Navigation')?></h4>
                        <ul>
                            <?php if(Yii::$app->user->identity->isProvider): ?>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Find Patient'), ['search/index'])?></li>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'My Patients'), ['provider-order/index'])?></li>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'My Calendar'), ['provider-calendar/index'])?></li>
                            <?php elseif(Yii::$app->user->identity->isCustomer): ?>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'List Patients'), ['patient/index'])?></li>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'My Profile'), ['profile/index'])?></li>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Services'), ['site/service'])?></li>
                            <?php else: ?>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Manage Articles'), ['article/admin'])?></li>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'My Profile'), ['profile/index'])?></li>
                                <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Services'), ['site/service'])?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-md-6 footer-links">
                        <h4><?= Yii::t('app', 'Useful Links')?></h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'How it works'), ['site/how-it-work'])?></li>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'About Us'), ['site/about'])?></li>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Contact Us'), ['site/contact'])?></li>
                        </ul>
                    </div>

                <?php endif; ?>

                <div class="col-lg-4 col-md-6 footer-links">
                    <h4>&nbsp;</h4>
                        <ul>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Terms of Service'), ['site/terms-of-service'])?></li>
                            <li><i class="bx bx-chevron-right"></i> <?= Html::a(Yii::t('app', 'Privacy Policy'), ['site/privacy-policy'])?></li>
                        </ul>
                </div>

            </div>
        </div>
    </div>

    <div class="container">
        <div class="copyright">
            <?= Yii::t('app', '&copy; {date} {company}. All Rights Reserved', ['date' => date('Y'), 'company' => '<strong><span>'.Yii::$app->name.'</span></strong>'])?>
        </div>
    </div>
</footer><!-- End Footer -->