<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\helpers\ConstHelper;
use common\models\NoteDischargeSummary;

/**
 * NoteDischargeSummarySearch represents the model behind the search form of `common\models\NoteDischargeSummary`.
 */
class NoteDischargeSummarySearch extends NoteDischargeSummary
{
    /**
     * How many patient service request we want to display per page.
     * @var int
     */
    private $_pageSize = 50;

    /**
     * @var string $order_number
     */
    public $order_number;

    /**
     * @var string $visited_at
     */
    public $visited_at;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'visit_id', 'provider_id', 'created_by', 'updated_by', 'submitted_by', 'accepted_by'], 'integer'],
            [['order_number', 'visited_at', 'status', 'created_at', 'updated_at', 'submitted_at', 'accepted_at', 'patient_name', 'mrn', 'diagnosis','therapist_name', 'therapist_title', 'note_date'], 'safe'],
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
        $query = NoteDischargeSummary::find();

        // add conditions that should always apply here
        $query->joinWith(['visit', 'order']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        $dataProvider->sort->attributes['visited_at'] = [
            'asc' => ['[[visit.visited_at]]' => SORT_ASC],
            'desc' => ['[[visit.visited_at]]' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['order_number'] = [
            'asc' => ['[[order.order_number]]' => SORT_ASC],
            'desc' => ['[[order.order_number]]' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '[[note_discharge_summary.id]]' => $this->id,
            '[[note_discharge_summary.order_id]]' => $this->order_id,
            '[[note_discharge_summary.visit_id]]' => $this->visit_id,
            '[[note_discharge_summary.provider_id]]' => $this->provider_id,
            '[[note_discharge_summary.created_by]]' => $this->created_by,
            '[[note_discharge_summary.updated_by]]' => $this->updated_by,
            '[[note_discharge_summary.submitted_by]]' => $this->submitted_by,
            '[[note_discharge_summary.accepted_by]]' => $this->accepted_by,
            '[[note_discharge_summary.status]]' => $this->status,
            "date([[note_discharge_summary.submitted_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->submitted_at),
            "date([[visit.visited_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->visited_at)
        ]);

        $query
            ->andFilterWhere(['ilike', '[[note_discharge_summary.mrn]]', $this->mrn])
            ->andFilterWhere(['ilike', '[[note_discharge_summary.patient_name]]', $this->patient_name])
            ->andFilterWhere(['ilike', '[[note_discharge_summary.therapist_name]]', $this->therapist_name])
            ->andFilterWhere(['ilike', '[[order.order_number]]', $this->order_number]);
            //->andFilterWhere(['ilike', '[[note_discharge_summary.diagnosis]]', $this->diagnosis])

        return $dataProvider;
    }
}
