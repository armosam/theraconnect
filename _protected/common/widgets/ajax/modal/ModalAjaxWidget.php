<?php

namespace common\widgets\ajax\modal;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;


/**
 * Class ModalAjaxWidget allows to load content in modal window via ajax,
 * by clicking on button, if [[ModalAjaxWidget::showBtn]] is `true`,
 * Content gets from [[ModalAjaxWidget::btnUrl]].
 *
 * ```
 * ModalAjaxWidget::widget([
 *      'showBtn' => true,                              // default
 *      'modalText' => Yii::t('app', 'Loading ...'),    // default
 *      'btnLabel' => Yii::t('app', 'Some label'),
 *      'btnUrl' => ['action'],
 *      'btnOptions' => [],                             // default
 * ]);
 * ```
 *
 * Or if you want to open modal on your link, you can use like this:
 *
 * ```
 * echo \common\widgets\ajax\modal\ModalAjaxWidget::widget([
 *      'showBtn' => false,
 *      'targetId' => 'some static id used in button's data-target attribute',
 * ]);
 *
 * <a href="url to get content for modal" data-target="#some_target_id" data-toggle="modal">Click me</a>
 * ```
 *
 */
class ModalAjaxWidget extends Widget
{
    /**
     * @var null|string Title of modal window.
     */
    public $modalTitle = null;
    /**
     * @var null|string Content text of modal window. Is shown before content is render to modal.
     */
    public $modalText = null;
    /**
     * @var bool Whether to show button, by clicking which will show content
     */
    public $showBtn = true;
    /**
     * @var string Button label text (`required`).
     */
    public $btnLabel;
    /**
     * @var string Url of button by which will be loaded content (`required`).
     */
    public $btnUrl;
    /**
     * @var array Html options of button.
     */
    public $btnOptions = [];
    /**
     * @var string Id of modal window, button will have it like `data-target` attribute.
     * If not set, uses id of this widget.
     */
    public $targetId;
    /**
     * @var string
     */
    public $modalClass = 'ajax-modal-wrap';


    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if ($this->modalText === null) {
            $this->modalText = Html::tag(
                'div',
                Html::tag('span', '', [
                    'class' => 'glyphicon glyphicon-refresh glyphicon-refresh-animate',
                ]),
                ['class' => 'spinner-wrap']
            );
        }

        if ($this->modalTitle === null) {
            $this->modalTitle = Yii::t('app', 'Info');
        }

        if (!$this->targetId) {
            $this->targetId = $this->id;
        }

        if ($this->showBtn && ($this->btnLabel === null || !$this->btnUrl)) {
            throw new InvalidConfigException('Params "btnLabel" and "btnUrl" are required');
        }

        $defaultBtnOptions = [
            'class' => 'btn btn-info',
            'data' => [
                'toggle' => 'modal',
                'target' => '#' . $this->targetId,
            ],
        ];

        $this->btnOptions = ArrayHelper::merge($defaultBtnOptions, $this->btnOptions);

        ModalAjaxAsset::register($this->view);

        $options = Json::encode([
            'modalTitle' => $this->modalTitle,
            'modalText' => $this->modalText,
            'targetId' => '#'.$this->targetId
        ]);
        $this->view->registerJs("new modalAjaxWidgetClicker({$options})");
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->showBtn) {
            echo Html::a($this->btnLabel, $this->btnUrl, $this->btnOptions);
        }

        Modal::begin([
            'header' => Html::tag('h3', Html::tag('span', '', ['class' => 'glyphicon glyphicon-check', 'aria-hidden' => 'true']) .' '. Html::tag('span', $this->modalTitle, ['class' => 'modal-title'])),
            'options' => ['id' => $this->targetId, 'class' => $this->modalClass],
            'size' => Modal::SIZE_DEFAULT
        ]);

        echo $this->modalText;

        Modal::end();
    }
}
