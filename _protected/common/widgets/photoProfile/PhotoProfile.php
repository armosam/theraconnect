<?php

namespace common\widgets\photoProfile;

use Yii;
use yii\base\Widget;
use yii\db\Expression;
use common\models\User;

/**
 * Class PhotoProfile
 * @package common\widgets\PhotoProfile
 */
class PhotoProfile extends Widget
{
    /**
     * @var string $title
     */
    public $title = '';

    /**
     * @var string $hint
     */
    public $hint = '';

    /**
     * @var array $filters
     */
    public $filters = [];

    /**
     * @var array $items
     */
    public $items = [];

    /**
     * @var int $item_count
     */
    public $item_count = 9;

    public function init()
    {
        parent::init();

        $items = User::find()->provider(true)->hasAvatar(true)->hasService(true)->orderBy(new Expression('random()'))->limit($this->item_count)->all();
        foreach ($items as $item) {
            $this->filters['*'] = ['name' => Yii::t('app', 'All'), 'active' => true];
            if (!empty($item->service)){
                $this->filters['.filter-'.$item->service->id] = ['name' => $item->service->service_name, 'active' => false];
                $this->items[] = [
                    'id' => $item->getId(),
                    'name' => $item->getUserFullName(),
                    'service_name' => $item->service->service_name,
                    'filter' => 'filter-'.$item->service->id
                ];
            }
        }
    }

    public function run()
    {
        if(empty($this->items)){
            return null;
        }
        PhotoProfileAsset::register($this->getView());
        return $this->render('index', ['model' => $this]);
    }
}