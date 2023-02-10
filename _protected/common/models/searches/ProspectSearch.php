<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Prospect;
use common\helpers\ConstHelper;

/**
 * ProspectSearch represents the model behind the search form of `common\models\Prospect`.
 */
class ProspectSearch extends Prospect
{
    public $_pageSize = 100;

    /** @var string $full_name */
    public $full_name;

    /** @var string $full_address */
    public $full_address;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'service_id', 'rejected_by', 'created_by', 'updated_by'], 'integer'],
            [['first_name', 'last_name', 'full_name', 'full_address', 'email', 'phone_number', 'address', 'city', 'state', 'zip_code', 'country', 'license_type', 'license_number', 'license_expiration_date', 'language', 'covered_county', 'covered_city', 'ip_address', 'note', 'rejected_at', 'rejection_reason', 'status', 'created_at', 'updated_at'], 'safe'],
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
        $query = Prospect::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        // make full_name sortable
        $dataProvider->sort->attributes['full_name'] = [
            'asc' => ["CONCAT_WS(' ', [[prospect.first_name]], [[prospect.last_name]])" => SORT_ASC],
            'desc' => ["CONCAT_WS(' ', [[prospect.first_name]], [[prospect.last_name]])" => SORT_DESC],
        ];

        // make full_name sortable
        $dataProvider->sort->attributes['full_address'] = [
            'asc' => ["CONCAT_WS(' ', [[prospect.address]], [[prospect.city]], [[prospect.state]], [[prospect.zip_code]], [[prospect.country]])" => SORT_ASC],
            'desc' => ["CONCAT_WS(' ', [[prospect.address]], [[prospect.city]], [[prospect.state]], [[prospect.zip_code]], [[prospect.country]])" => SORT_DESC],
        ];

        $dataProvider->sort->defaultOrder = ['created_at'=>SORT_DESC];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '[[prospect.id]]' => $this->id,
            '[[prospect.service_id]]' => $this->service_id,
            "date([[prospect.license_expiration_date]])" => ConstHelper::formatDate($this->license_expiration_date),
            '[[prospect.status]]' => $this->status,
            /*'[[prospect.rejected_by]]' => $this->rejected_by,
            '[[prospect.created_by]]' => $this->created_by,
            '[[prospect.updated_by]]' => $this->updated_by,
             "date([[prospect.rejected_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->rejected_at),
             "date([[prospect.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
             "date([[prospect.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),*/
        ]);

        $query
            ->andFilterWhere(['ilike', "CONCAT_WS(' ', [[prospect.first_name]], [[prospect.last_name]])", $this->full_name],)
            ->andFilterWhere(['ilike', '[[prospect.email]]', $this->email])
            ->andFilterWhere(['ilike', '[[prospect.phone_number]]', $this->phone_number])
            ->andFilterWhere(['ilike', "CONCAT_WS(' ', [[prospect.address]], [[prospect.city]], [[prospect.state]], [[prospect.zip_code]], [[prospect.country]])", $this->full_address])
            ->andFilterWhere(['ilike', '[[prospect.license_type]]', $this->license_type])
            ->andFilterWhere(['ilike', '[[prospect.license_number]]', $this->license_number])
            ->andFilterWhere(['ilike', '[[prospect.language]]::text', $this->language])
            ->andFilterWhere(['ilike', '[[prospect.covered_county]]::text', $this->covered_county]);
            //->andFilterWhere(['ilike', '[[prospect.covered_city]]::text', $this->covered_city]);
            /*->andFilterWhere(['ilike', '[[prospect.first_name]]', $this->first_name])
            ->andFilterWhere(['ilike', '[[prospect.last_name]]', $this->last_name])
            ->andFilterWhere(['ilike', '[[prospect.address]]', $this->address])
            ->andFilterWhere(['ilike', '[[prospect.city]]', $this->city])
            ->andFilterWhere(['ilike', '[[prospect.state]]', $this->state])
            ->andFilterWhere(['ilike', '[[prospect.zip_code]]', $this->zip_code])
            ->andFilterWhere(['ilike', '[[prospect.country]]', $this->country])
            ->andFilterWhere(['ilike', '[[prospect.ip_address]]', $this->ip_address])
            ->andFilterWhere(['ilike', '[[prospect.note]]', $this->note])
            ->andFilterWhere(['ilike', '[[prospect.rejection_reason]]', $this->rejection_reason])*/

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
