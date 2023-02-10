<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Patient;
use common\helpers\ConstHelper;

/**
 * PatientSearch represents the model behind the search form of `common\models\Patient`.
 */
class PatientSearch extends Patient
{
    /**
     * How many users we want to display per page.
     *
     * @var int
     */
    private $_pageSize = 50;

    /**
     * @var string $full_name
     */
    public $full_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'created_by', 'updated_by'], 'integer'],
            [['birth_date', 'start_of_care'], 'date', 'format'=>'php:m/d/Y'],
            [['patient_number', 'start_of_care', 'full_name', 'first_name', 'last_name', 'address', 'city', 'state', 'country', 'zip_code', 'birth_date', 'ssn', 'phone_number', 'status', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Patient::find()->joinWith('customer');

        // add conditions that should always apply here

        if(isset($this->customer_id)) {
            $query->andFilterWhere([$this::tableName().'.customer_id' => $this->customer_id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        // make full_name sortable
        $dataProvider->sort->attributes['customer_id'] = [
            'asc' => ["CONCAT_WS(' ', [[user.first_name]], [[user.last_name]])" => SORT_ASC],
            'desc' => ["CONCAT_WS(' ', [[user.first_name]], [[user.last_name]])" => SORT_DESC],
        ];

        // make full_name sortable
        $dataProvider->sort->attributes['full_name'] = [
            'asc' => ["CONCAT_WS(' ', [[patient.first_name]], [[patient.last_name]])" => SORT_ASC],
            'desc' => ["CONCAT_WS(' ', [[patient.first_name]], [[patient.last_name]])" => SORT_DESC],
        ];

        $dataProvider->sort->defaultOrder = ['updated_at'=>SORT_DESC];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '[[patient.id]]' => $this->id,
            '[[patient.customer_id]]' => $this->customer_id,
            "date([[patient.birth_date]])" => ConstHelper::formatDate($this->birth_date),
            "date([[patient.start_of_care]])" => ConstHelper::formatDate($this->start_of_care),
            //'[[patient.created_by]]' => $this->created_by,
            //'[[patient.updated_by]]' => $this->updated_by,
            "date([[patient.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            //"date([[patient.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
        ]);

        $query->andFilterWhere(['ilike', '[[patient_number]]', $this->patient_number])
            ->andFilterWhere(['ilike', "CONCAT_WS(' ', [[patient.first_name]], [[patient.last_name]])", $this->full_name])
            //->andFilterWhere(['ilike', '[[patient.first_name]]', $this->first_name])
            //->andFilterWhere(['ilike', '[[patient.last_name]]', $this->last_name])
            ->andFilterWhere(['ilike', "CONCAT_WS(' ', [[patient.address]], [[patient.city]], [[patient.state]], [[patient.zip_code]], [[patient.country]])", $this->address])
            //->andFilterWhere(['ilike', '[[patient.city]]', $this->city])
            //->andFilterWhere(['ilike', '[[patient.state]]', $this->state])
            //->andFilterWhere(['ilike', '[[patient.country]]', $this->country])
            //->andFilterWhere(['ilike', '[[patient.zip_code]]', $this->zip_code])
            ->andFilterWhere(['ilike', '[[patient.ssn]]', $this->ssn])
            ->andFilterWhere(['ilike', '[[patient.phone_number]]', $this->phone_number])
            ->andFilterWhere(['ilike', '[[patient.status]]', $this->status]);

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
