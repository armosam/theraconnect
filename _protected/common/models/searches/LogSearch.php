<?php

namespace common\models\searches;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Log;
use common\traits\LogTimeSearchTrait;

/**
 * LogSearch represents the model behind the search form of `common\models\Log`.
 */
class LogSearch extends Log
{
    use LogTimeSearchTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'level', 'start_time', 'end_time'], 'integer'],
            [['category', 'prefix', 'message', 'log_time_search'], 'safe'],
            [['log_time'], 'number'],
            [['start_time'], 'compare', 'compareAttribute' => 'end_time', 'operator' => '<=', 'skipOnEmpty' => true],
            [['end_time'], 'compare', 'compareAttribute' => 'start_time', 'operator' => '>=', 'skipOnEmpty' => true],
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
        $query = static::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'log_time' => SORT_DESC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'level' => $this->level,
            'log_time' => $this->log_time,
            'category' => $this->category,
        ]);

        if(!empty($this->start_time)){
            $query->andFilterWhere(['between', 'log_time', $this->start_time, $this->end_time]);
        }

        $query->andFilterWhere(['like', 'prefix', $this->prefix])
            ->andFilterWhere(['like', 'message', $this->message]);

        return $dataProvider;
    }
}
