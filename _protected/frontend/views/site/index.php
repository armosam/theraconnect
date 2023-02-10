<?php

use yii\helpers\Html;
use common\widgets\photoProfile\PhotoProfile;

?>
    <!-- ======= About Section ======= -->
    <section id="about" class="about">
        <div class="container">

            <div class="row">
                <div class="col-lg-12">
                    <div class="content" style="margin: 0 0 40px 0">
                        <h3 class="text-center text-uppercase text-muted"><b><?= Yii::t('app', 'How to join us')?></b></h3>
                        <h5 class="text-muted"><?= Yii::t('app', 'THERA Connections INC. collaborates with the most proficient and experienced therapists in order to enhance all their clients needs.') ?></h5>
                    </div>
                </div>
                <div class="col-lg-6" data-aos="fade-right">
                    <div class="content text-center text-muted" style="margin: 0 0 40px 0">
                        <h4><b><?= Yii::t('app', 'Are You Therapist?') ?></b></h4>
                        <h6 class="hint-block"><?= Yii::t('app', 'Click this button if you want to join as a therapist.') ?></h6>
                        <?= Html::a(Yii::t('app', 'Join as a therapist'), ['join/application-form'], ['class' => 'btn-get-started']) ?>
                    </div>
                </div>
                <div class="col-lg-6 d-flex flex-column" data-aos="fade-left">
                    <div class="content text-center text-muted"style="margin: 0 0 40px 0">
                        <h4><b><?= Yii::t('app', 'Home Health Care Agency?') ?></b></h4>
                        <h6 class="hint-block"><?= Yii::t('app', 'Click this button if you want to sign up as a home health agency.') ?></h6>
                        <?= Html::a(Yii::t('app', 'Sign up as an agency'), ['site/sign-up-customer'], ['class' => 'btn-get-started']) ?>
                    </div>
                </div>
            </div>

        </div>
    </section><!-- End About Section -->

    <!-- ======= Features Section ======= -->
    <section id="features" class="features">
        <div class="container">

            <div class="row">
                <div class="col-lg-12 text-center" style="margin: 0 0 40px 0">
                    <h2 class="text-uppercase"><b><?= Yii::t('app', 'Our Services')?></b></h2>
                </div>
                <div class="col-lg-6 mt-2 mb-tg-0 order-2 order-lg-1">
                    <ul class="nav nav-tabs flex-column">
                        <li class="nav-item" data-aos="fade-up">
                            <a class="nav-link active show" data-toggle="tab" href="#tab-1">
                                <h4><?= Yii::t('app', 'Physical Therapist')?></h4>
                                <p><?= Yii::t('app', 'These therapists help patients to improve their health issues.')?></p>
                            </a>
                        </li>
                        <li class="nav-item mt-2" data-aos="fade-up" data-aos-delay="100">
                            <a class="nav-link" data-toggle="tab" href="#tab-2">
                                <h4><?= Yii::t('app', 'Occupational Therapist')?></h4>
                                <p><?= Yii::t('app', 'They are therapists trained to provide a qualified support to patients.')?></p>
                            </a>
                        </li>
                        <li class="nav-item mt-2" data-aos="fade-up" data-aos-delay="200">
                            <a class="nav-link" data-toggle="tab" href="#tab-3">
                                <h4><?= Yii::t('app', 'Speech Therapist')?></h4>
                                <p><?= Yii::t('app', 'Speech therapists provide support to improve speech of patient.')?></p>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-lg-6 order-1 order-lg-2" data-aos="zoom-in">
                    <div class="tab-content">
                        <div class="tab-pane active show" id="tab-1">
                            <figure>
<!--                                <img src="--><?php //$this->theme->getUrl('img/home/service1.jpg')?><!--" alt="" class="img-fluid">-->
                                <h4><b>Physical Therapy</b></h4>
                                <div>
                                    <p>If you are dealing with a recent injury, recovering from surgery or experiencing chronic pain, our Select Physical Therapy team can help you heal, regain strength, mobility and reduce pain. We provide a patient care experience that promotes healing and recovering in a compassionate environment.</p>
                                    <p>Our goal is to help you feel better physically while making sure you’re comfortable and an active partner throughout your physical therapy treatment. Our experienced clinical team will work diligently to get to the core of your injury or condition to help you recover and stay healthy.</p>
                                    <p>We provide a patient care experience that promotes healing and recovering in a compassionate environment.</p>
                                    <p>
                                        <span><strong>Physical therapy can be used to treat many conditions, including:</strong></span>
                                    <ul>
                                        <li>Back and neck pain</li>
                                        <li>Body misalignment (scoliosis)/spinal dysfunctions</li>
                                        <li>Chronic pain/fibromyalgia</li>
                                        <li>Muscle, myofascial and joint pain</li>
                                        <li>Orthopedic injuries</li>
                                        <li>Pre and post-operative conditions</li>
                                        <li>Recovery and Reconditioning</li>
                                        <li>Soft tissue injuries</li>
                                        <li>Sprains and strains</li>
                                        <li>Stroke</li>
                                        <li>Sports-related injuries</li>
                                        <li>Weakness or loss of motion</li>
                                        <li>Work-related injuries</li>
                                    </ul>
                                    </p>
                                </div>
                            </figure>
                        </div>
                        <div class="tab-pane" id="tab-2">
                            <figure>
<!--                                <img src="--><?php //$this->theme->getUrl('img/home/service2.jpg')?><!--" alt="" class="img-fluid">-->
                                <h4><b>Occupational Therapy</b></h4>
                                <div>
                                    <p>Occupational therapy is used to treat a variety of physical, sensory and cognitive (thinking) conditions. Your doctor and occupational therapist will work with you to determine if therapy is right for you. We treat patients of all ages. As our patient, you’ll benefit from a treatment plan specific to your unique needs and goals. Through the therapeutic use of daily activities our Occupational therapists enable our clients by composing an individualized evaluation, during which our client and their family along with occupational therapist determine the person’s goal.</p>
                                    <p>
                                        <span><strong>Our Occupational Therapists specialize in the following areas:</strong></span>
                                    <ul>
                                        <li>Amputations, including the use of prosthetics (artificial body parts) for upper and lower limbs</li>
                                        <li>Arthritis and related joint disorders</li>
                                        <li>Burns and traumatic (serious) injuries</li>
                                        <li>Fractures</li>
                                        <li>Multiple sclerosis (MS), a disease of the central nervous system</li>
                                        <li>Neurological disorders of the brain, spine or nerves (Parkinson’s disease, stroke)</li>
                                        <li>Repetitive motion disorders from the overuse of muscles, nerves, ligaments and tendons</li>
                                        <li>Sports-related injuries</li>
                                        <li>Work-related injuries</li>
                                    </ul>
                                    </p>
                                </div>
                            </figure>
                        </div>
                        <div class="tab-pane" id="tab-3">
                            <figure>
<!--                                <img src="--><?php //$this->theme->getUrl('img/home/service3.jpg')?><!--" alt="" class="img-fluid">-->
                                <h4><b>Speech Therapy</b></h4>
                                <div>
                                    <p>Speech therapy help to improve language development, speech disability that includes trouble pronouncing words, communication, and pragmatic language skills. Our Speech therapists operate to prevent , assess , diagnose, and treat speech, language , social communication, cognitive- communication, and swallowing disorders in our clients of all ages. </p>
                                    <p>
                                        <span><strong>Conditions treated by Speech therapy include:</strong></span>
                                    <ul>
                                        <li>Speech sounds: articulation, dysarthria, apraxia of speech</li>
                                        <li>Accent Modification</li>
                                        <li>Expressive/Receptive language: traumatic brain injury or stroke</li>
                                        <li>Language Processing</li>
                                        <li>Listening skills for adults with hearing loss/cochlear implants</li>
                                        <li>Stuttering</li>
                                        <li>Voice disorders</li>
                                        <li>Transgender voice</li>
                                        <li>Literacy</li>
                                        <li>Articulation</li>
                                        <li>Accent Modification</li>
                                        <li>Auditory rehabilitation</li>
                                        <li>Communication coaching for anxiety related disorders</li>
                                        <li>Dysarthria</li>
                                        <li>Stuttering</li>
                                        <li>Voice</li>
                                        <li>Transgender voice</li>
                                        <li>Traumatic Brain Injury</li>
                                    </ul>
                                    </p>
                                </div>
                            </figure>
                        </div>
                        <h6><?= Yii::t('app', '{service_link}', ['service_link' => Html::a('More About Our Services', ['site/service'], ['class'=>'btn btn-link'])]) ?></h6>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- End Features Section -->

    <!-- ======= Counts Section ======= -->
    <section id="counts" class="counts section-bg">
        <div class="container">

            <div class="row">
                <div class="image col-xl-5 d-flex align-items-stretch justify-content-center justify-content-xl-start" data-aos="fade-right" data-aos-delay="150">
                    <img src="<?= $this->theme->getUrl('img/home/counts-img.jpg')?>" alt="" class="img-fluid">
                </div>

                <div class="col-xl-7 d-flex align-items-stretch pt-4 pt-xl-0" data-aos="fade-left" data-aos-delay="300">
                    <div class="content d-flex flex-column justify-content-center">
                        <div class="row">
                            <div class="col-md-6 d-md-flex align-items-md-stretch">
                                <div class="count-box">
                                    <i class="bx bx-happy"></i>
                                    <span data-toggle="counter-up">65</span>
                                    <p><?= Yii::t('app', '{clients} found their therapist through our services.', ['clients' => Html::tag('strong', Yii::t('app', 'Happy agencies and patients'))])?></p>
                                </div>
                            </div>

                            <div class="col-md-6 d-md-flex align-items-md-stretch">
                                <div class="count-box">
                                    <i class="bx bx-conversation"></i>
                                    <span data-toggle="counter-up">85</span>
                                    <p><?= Yii::t('app', '{connection} established by health agencies and therapists through our qualified service.', ['connection' => Html::tag('strong', Yii::t('app', 'Connections'))])?> </p>
                                </div>
                            </div>

                            <div class="col-md-6 d-md-flex align-items-md-stretch">
                                <div class="count-box">
                                    <i class="bx bx-calendar-star"></i>
                                    <span data-toggle="counter-up">12</span>
                                    <p><?= Yii::t('app', '{years} have our qualified therapists.', ['years' => Html::tag('strong', Yii::t('app', 'Years of experience'))])?> </p>
                                </div>
                            </div>

                            <div class="col-md-6 d-md-flex align-items-md-stretch">
                                <div class="count-box">
                                    <i class="bx bx-check-shield"></i>
                                    <span data-toggle="counter-up">15</span>
                                    <p><?= Yii::t('app', '{success} as a result of having qualified therapist.', ['success' => Html::tag('strong', Yii::t('app', 'Succeed stories'))])?> </p>
                                </div>
                            </div>
                        </div>
                    </div><!-- End .content-->
                </div>
            </div>

        </div>
    </section><!-- End Counts Section -->


    <?php /*echo PhotoProfile::widget([
        'title' => Yii::t('app', 'Our Therapists'),
        'hint' => Yii::t('app', ''),
        'item_count' => 9
    ]) */?>

    <!-- ======= Testimonials Section ======= -->
    <!--<section id="testimonials" class="testimonials section-bg">
        <div class="container">

            <div class="section-title" data-aos="fade-up">
                <h2><?/*= Yii::t('app', 'Testimonials')*/?></h2>
                <p><?/*= Yii::t('app', 'Qualified support for all kind of patients. ')*/?></p>
            </div>

            <div class="owl-carousel testimonials-carousel" data-aos="fade-up" data-aos-delay="200">

                <div class="testimonial-wrap">
                    <div class="testimonial-item">
                        <img src="<?/*= $this->theme->getUrl('img/home/testimonials1.jpg')*/?>" class="testimonial-img" alt="">
                        <h3><?/*= Yii::t('app', 'Alison Ches')*/?></h3>
                        <h4><?/*= Yii::t('app', 'Physical Therapy')*/?></h4>
                        <p>
                            <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                            <?/*= Yii::t('app', 'I had a wonderful experience. My physical therapy began morning and my therapist was wonderful. is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.')*/?>
                            <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                        </p>
                    </div>
                </div>

                <div class="testimonial-wrap">
                    <div class="testimonial-item">
                        <img src="<?/*= $this->theme->getUrl('img/home/testimonials2.jpg')*/?>" class="testimonial-img" alt="">
                        <h3><?/*= Yii::t('app', 'Jenny Karas')*/?></h3>
                        <h4><?/*= Yii::t('app', 'Speech Therapy')*/?></h4>
                        <p>
                            <i class="bx bxs-quote-alt-left quote-icon-left"></i>
                            <?/*= Yii::t('app', 'It was getting closer to our speech therapy, and I still could not find a therapist. is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.')*/?>
                            <i class="bx bxs-quote-alt-right quote-icon-right"></i>
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>--><!-- End Testimonials Section -->

    <!-- ======= Frequently Asked Questions Section ======= -->
    <!--<section id="faq" class="faq">
        <div class="container">

            <div class="section-title">
                <h2><?/*= Yii::t('app', 'Frequently Asked Questions')*/?></h2>
                <p><?/*= Yii::t('app', 'We have answers for most frequently asked questions. Please {contact_us} if you do not find answer of you question.', ['contact_us' => Html::a(Yii::t('app', 'contact us'), ['site/contact'])])*/?></p>
            </div>

            <h5><?/*= Yii::t('app', 'For Home Health Care Agency')*/?></h5>

            <div class="accordion-list" id="faq1">
                <ul>
                    <li data-aos="fade-up">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" class="collapse" href="#accordion-list-1"><?/*= Yii::t('app', 'How to sign up as a home health care agency?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-1" class="collapse show" data-parent="#faq1">
                            <p>
                                <?/*= Yii::t('app', 'There are two options to sign up as a home health care agency. You can go ahead and {search}, then get signed up when you request service for your selected therapist. Also you can open regular {sign-up-customer} page and provide your information to sign up. After sign up you need to activate your account to allow therapists get your service requests. So without full filled profile and activated account therapists will not see your requests.', ['search' => Yii::t('app', 'find your preferred therapist'), 'sign-up-customer' => Yii::t('app', 'Sign up as an agency')])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="100">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-2" class="collapsed"><?/*= Yii::t('app', 'How to request a service?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-2" class="collapse" data-parent="#faq1">
                            <p>
                                <?/*= Yii::t('app', 'Our {find_specialist} page helps you find your preferred therapist. Click on the Request Service button for the selected therapist and provide necessary information to request a service. It is important to provide correct and up to date information on the service request form as your contact information should be shared with therapist allowing them to contact you for a service.', ['find_specialist' => Html::a(Yii::t('app', 'Find Your therapist'), ['search/index'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="200">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-3" class="collapsed"><?/*= Yii::t('app', 'How to hire a therapist?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-3" class="collapse" data-parent="#faq1">
                            <p>
                                <?/*= Yii::t('app', 'To hire a therapist go to our {find_specialist} page, set search criteria to find couple of therapists matching your requirements and needs. Send Service Requests to selected therapists. Do not hesitate to send more requests, it will help you get a list of possible candidates. Therapists who are serious about serving families will respond you within 48 hours. So you are going to receive email from each requested therapist with contact information. Go ahead and contact each therapist to find the best match. Confirm the therapist’s availability. Ask if you would like to continue a face-to-face meeting. More about {more_hire_specialist}.', ['find_specialist' => Html::a(Yii::t('app', 'Find Your Patient'), ['search/index'], ['class' => 'simple-link']), 'more_hire_specialist' => Html::a(Yii::t('app', 'How to hire a therapist'), ['#'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="300">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-4" class="collapsed"><?/*= Yii::t('app', 'How to interview therapist?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-4" class="collapse" data-parent="#faq1">
                            <p>
                                <?/*= Yii::t('app', 'The occupation is unlicensed and unregulated. One way to ensure your has met high standards for education, training and experience is to choose a certified from a well-known and respected certifying organization. Ask therapists to show their certification and qualification for your verification. More about {evaluate_specialist}.', ['evaluate_specialist' => Html::a(Yii::t('app', 'Evaluation of Therapist'), ['#'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="400">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-5" class="collapsed"><?/*= Yii::t('app', 'What if therapist rejected your service request?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-5" class="collapse" data-parent="#faq1">
                            <p>
                                <?/*= Yii::t('app', 'It is possible that therapist you found cannot respond your request because of availability. You can try to find another possible candidates on the {find_specialist} page.', ['find_specialist' => Html::a(Yii::t('app', 'Find Your Therapist'), ['search/index'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                </ul>
            </div>

            <br>
            <h5><?/*= Yii::t('app', 'For Therapist')*/?></h5>

            <div class="accordion-list" id="faq2">
                <ul>
                    <li data-aos="fade-up">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" class="collapse" href="#accordion-list-11"><?/*= Yii::t('app', 'How to sign up as a therapist?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-11" class="collapse show" data-parent="#faq2">
                            <p>
                                <?/*= Yii::t('app', 'Sign up is strait forward action, just open {sign-up-specialist} page, provide necessary information and submit sign up form. Then you are going to receive account activation link by email. Just click on the link and activate your account.', ['sign-up-specialist' => Html::a(Yii::t('app', 'Sign up as a therapist'), ['site/sign-up-provider'], ['class' => 'simple-link'])
                                ])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="100">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-12" class="collapsed"><?/*= Yii::t('app', 'How to set up your profile correctly?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-12" class="collapse" data-parent="#faq2">
                            <p>
                                <?/*= Yii::t('app', 'After activating you account you need to complete your profile. Go to {profile} page, upload your up to date photo, provide your information for your profile. After changing your email or phone numbers you need to verify them. Please be informed that without complete information your profile could not be displayed on the search or listings page. Also go to {notification} page to change notification preferences.', ['profile' => Html::a(Yii::t('app', 'My Profile'), ['profile/update'], ['class' => 'simple-link']), 'notification' => Html::a(Yii::t('app', 'My Notifications'), ['user-notification/index'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="300">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-14" class="collapsed"><?/*= Yii::t('app', 'How to configure your services?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-14" class="collapse" data-parent="#faq2">
                            <p>
                                <?/*= Yii::t('app', 'To set up services you are providing please go to {services} page (You can open this page in the menu also), click edit button then add or change services you are qualified and ready to provide. You can provide your fees, duration, experience for each service here. After changes please save your services page.', ['services' => Html::a(Yii::t('app', 'My Services'), ['user-service/index'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="400">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-15" class="collapsed"><?/*= Yii::t('app', 'How to set your qualifications?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-15" class="collapse" data-parent="#faq2">
                            <p>
                                <?/*= Yii::t('app', 'To set up your qualification and education please go to {qualification} page (You can open this page in the menu also), click edit button then add or change qualifications or education you have. After changes please save your qualifications page.', ['qualification' => Html::a(Yii::t('app', 'My Qualification'), ['user-qualification/index'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                    <li data-aos="fade-up" data-aos-delay="200">
                        <i class="bx bx-help-circle icon-help"></i> <a data-toggle="collapse" href="#accordion-list-13" class="collapsed"><?/*= Yii::t('app', 'How to set your speaking language?')*/?> <i class="bx bx-chevron-down icon-show"></i><i class="bx bx-chevron-up icon-close"></i></a>
                        <div id="accordion-list-13" class="collapse" data-parent="#faq2">
                            <p>
                                <?/*= Yii::t('app', 'To set up your speaking languages go to {language} page (You can open this page in the menu also), click edit button then add or change languages you are speaking. After changes please save your languages page.', ['language' => Html::a(Yii::t('app', 'My Languages'), ['user-language/index'], ['class' => 'simple-link'])])*/?>
                            </p>
                        </div>
                    </li>

                </ul>
            </div>

        </div>
    </section>--><!-- End Frequently Asked Questions Section -->

