<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Visit;
use common\helpers\ConstHelper;

/**
 * VisitSearch represents the model behind the search form of `common\models\Visit`.
 */
class VisitSearch extends Visit
{
    /**
     * @var int $_pageSize
     */
    private $_pageSize = -1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'order_id', 'created_by', 'updated_by'], 'integer'],
            [['visited_at', 'comment', 'created_at', 'updated_at'], 'safe'],
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
        $query = Visit::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['visited_at' => SORT_DESC]],
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
            '[[visit.id]]' => $this->id,
            '[[visit.order_id]]' => $this->order_id,
            '[[visit.visited_at]]' => $this->visited_at,
            '[[visit.created_by]]' => $this->created_by,
            '[[visit.updated_by]]' => $this->updated_by,
            "date([[visit.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            "date([[visit.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
        ]);

        $query->andFilterWhere(['ilike', '[[visit.comment]]', $this->comment]);

        return $dataProvider;
    }
}
