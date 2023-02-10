<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Service;
use common\helpers\ConstHelper;

/**
 * ServiceSearch represents the model behind the search form about `common\models\Service`.
 *
 * Class ServiceSearch
 * @package common\models
 */
class ServiceSearch extends Service
{
    /**
     * How many users we want to display per page.
     *
     * @var int
     */
    private $_pageSize = -1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_name'], 'trim'],
            [['created_by', 'updated_by', 'ordering'], 'integer'],
            [['service_name'], 'string', 'max' => 200],
            [['status'], 'in', 'range' => [ConstHelper::STATUS_DELETED, ConstHelper::STATUS_ACTIVE, ConstHelper::STATUS_PASSIVE]],
            [['created_at', 'updated_at'], 'date', 'format'=>'yyyy-MM-dd HH:mm:ssZ']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Service::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>  [
                'defaultOrder' => ['ordering'=>SORT_ASC],
                'attributes' => [
                    'id'=>[
                        'asc' =>['[[service.id]]'=>SORT_ASC],
                        'desc' =>['[[service.id]]'=>SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'service_name'=>[
                        'asc' =>['[[service.service_name]]'=>SORT_ASC],
                        'desc' =>['[[service.service_name]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'status'=>[
                        'asc' =>['[[service.status]]'=>SORT_ASC],
                        'desc' =>['[[service.status]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'ordering' => [
                        'asc' =>['[[service.ordering]]'=>SORT_ASC],
                        'desc' =>['[[service.ordering]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            '[[service.id]]' => $this->id,
            '[[service.created_by]]' => $this->created_by,
            '[[service.updated_by]]' => $this->updated_by,
            "date([[service.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            "date([[service.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
            '[[service.ordering]]' => $this->ordering,
            '[[service.status]]' => $this->status,
        ]);

        $query->andFilterWhere(['ILIKE', '[[service.service_name]]', $this->service_name]);

        return $dataProvider;
    }
}
