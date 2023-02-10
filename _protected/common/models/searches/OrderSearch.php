<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;
use common\helpers\ConstHelper;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * How many patient service request we want to display per page.
     *
     * @var int
     */
    private $_pageSize = 50;

    /** @var int $provider_id */
    public $provider_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'provider_id', 'service_id', 'submitted_by', 'accepted_by', 'completed_by', 'created_by', 'updated_by'], 'integer'],
            [['order_number', 'patient_id', 'patient_name', 'patient_number', 'service_name', 'service_frequency', 'certification_start_date', 'certification_end_date', 'submitted_at', 'accepted_at', 'completed_at', 'created_at', 'updated_at', 'status'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Order::find()->joinWith('orderUsers');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
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
            '[[order.id]]' => $this->id,
            '[[user_order.user_id]]' => $this->provider_id,
            '[[order.patient_id]]' => $this->patient_id,
            '[[order.service_id]]' => $this->service_id,
            '[[order.status]]' => $this->status,
            //'[[order.submitted_by]]' => $this->submitted_by,
            //'[[order.accepted_by]]' => $this->accepted_by,
            //'[[order.completed_by]]' => $this->completed_by,
            //'[[order.created_by]]' => $this->created_by,
            //'[[order.updated_by]]' => $this->updated_by,
            "date([[order.certification_start_date]])" => ConstHelper::formatDate($this->certification_start_date),
            "date([[order.certification_end_date]])" => ConstHelper::formatDate($this->certification_end_date),
            "date([[order.submitted_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->submitted_at),
            "date([[order.accepted_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->accepted_at),
            "date([[order.completed_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->completed_at),
            "date([[order.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            "date([[order.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
        ]);

        $query->andFilterWhere(['ilike', '[[order.order_number]]', $this->order_number])
            ->andFilterWhere(['ilike', '[[order.patient_number]]', $this->patient_number])
            ->andFilterWhere(['ilike', '[[order.patient_name]]', $this->patient_name])
            ->andFilterWhere(['ilike', '[[order.service_name]]', $this->service_name])
            ->andFilterWhere(['ilike', '[[order.service_frequency]]', $this->service_frequency]);

        return $dataProvider;
    }

    /**
     * Sets page size for dataProvider
     * @param int $page_size
     */
    public function setPageSize($page_size): void
    {
        $this->_pageSize = $page_size;
    }
}
