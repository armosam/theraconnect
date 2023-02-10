<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\NoteProgress;
use common\helpers\ConstHelper;

/**
 * NoteProgressSearch represents the model behind the search form of `common\models\NoteProgress`.
 */
class NoteProgressSearch extends NoteProgress
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
            [['order_number', 'visited_at', 'status', 'created_at', 'updated_at', 'submitted_at', 'accepted_at', 'patient_name', 'mrn', 'dob', 'gender', 'therapist_name', 'note_date'], 'safe'],
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
        $query = NoteProgress::find();

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
            '[[note_progress.id]]' => $this->id,
            '[[note_progress.order_id]]' => $this->order_id,
            '[[note_progress.visit_id]]' => $this->visit_id,
            '[[note_progress.provider_id]]' => $this->provider_id,
            '[[note_progress.created_by]]' => $this->created_by,
            '[[note_progress.updated_by]]' => $this->updated_by,
            '[[note_progress.submitted_by]]' => $this->submitted_by,
            '[[note_progress.accepted_by]]' => $this->accepted_by,
            '[[note_progress.status]]' => $this->status,
            "date([[note_progress.submitted_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->submitted_at),
            "date([[visit.visited_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->visited_at)
        ]);

        $query
            ->andFilterWhere(['ilike', '[[note_progress.mrn]]', $this->mrn])
            ->andFilterWhere(['ilike', '[[note_progress.dob]]', $this->dob])
            ->andFilterWhere(['ilike', '[[note_progress.patient_name]]', $this->patient_name])
            ->andFilterWhere(['ilike', '[[note_progress.gender]]', $this->gender])
            ->andFilterWhere(['ilike', '[[note_progress.therapist_name]]', $this->therapist_name])
            ->andFilterWhere(['ilike', '[[order.order_number]]', $this->order_number]);

        return $dataProvider;
    }
}
