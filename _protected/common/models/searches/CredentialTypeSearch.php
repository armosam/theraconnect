<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\helpers\ConstHelper;
use common\models\CredentialType;

/**
 * CredentialTypeSearch represents the model behind the search form about `common\models\searches\CredentialType`.
 *
 * Class CredentialTypeSearch
 * @package common\models\searches
 */
class CredentialTypeSearch extends CredentialType
{
    /**
     * @var int $_pageSize
     */
    public $_pageSize = -1;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'ordering'], 'integer'],
            [['credential_type_name'], 'trim'],
            [['assigned_number_label', 'icon_class', 'credential_type_name'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'date', 'format'=>'yyyy-MM-dd HH:mm:ssZ'],
            [['status'], 'in', 'range' => [ConstHelper::STATUS_DELETED, ConstHelper::STATUS_ACTIVE, ConstHelper::STATUS_PASSIVE]],
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
        $query = CredentialType::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                /*'attributes' => [
                    '[[credential_type.id]]',
                    '[[credential_type.credential_type_name]]',
                    '[[credential_type.created_by]]',
                    '[[credential_type.created_at]]',
                    '[[credential_type.updated_by]]',
                    '[[credential_type.updated_at]]',
                    '[[credential_type.ordering]]',
                    '[[credential_type.status]]',
                ],*/
                'defaultOrder' => ['ordering' => SORT_ASC]
            ],
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        $dataProvider->sort->attributes['credential_type_name'] = [
            'asc' => ['[[credential_type.credential_type_name]]' => SORT_ASC],
            'desc' => ['[[credential_type.credential_type_name]]' => SORT_DESC],
        ];

        if (!$this->validate())
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $this->load($params);

        // grid filtering conditions
        $query->andFilterWhere([
            '[[credential_type.id]]' => $this->id,
            '[[credential_type.created_by]]' => $this->created_by,
            '[[credential_type.updated_by]]' => $this->updated_by,
            "date([[credential_type.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            "date([[credential_type.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
            '[[credential_type.status]]' => $this->status,
            '[[credential_type.ordering]]' => $this->ordering,
        ]);

        $query->andFilterWhere(['ilike', 'icon_class', $this->icon_class])
            ->andFilterWhere(['ilike', 'credential_type_name', $this->credential_type_name])
            ->andFilterWhere(['ilike', 'assigned_number_label', $this->assigned_number_label])
            ->andFilterWhere(['ilike', 'status', $this->status]);

        return $dataProvider;
    }
}
