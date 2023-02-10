<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use common\models\UserCredential;
use common\models\CredentialType;
use common\helpers\ConstHelper;

/**
 * UserCredentialSearch represents the model behind the search form about `common\models\UserCredential`.
 *
 * Class UserCredentialSearch
 * @package common\models
 */
class UserCredentialSearch extends UserCredential
{
    /**
     * How many users we want to display per page.
     *
     * @var int
     */
    private $_pageSize = -1;

    public $upload_file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['assigned_number', 'file_name'], 'trim'],
            [['credential_type_id', 'created_by', 'updated_by', 'ordering'], 'integer'],
            [['assigned_number', 'file_name'], 'string', 'max' => 255],
            [['status'], 'in', 'range' => [self::STATUS_PENDING, self::STATUS_APPROVED, self::STATUS_EXPIRED]],
            [['created_at', 'updated_at', 'approved_at'], 'date', 'format'=>'mm/dd/yyyy'],
            [['expire_date'], 'date', 'format'=>'mm/dd/yyyy'],
            [['upload_file'], 'safe'],
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
        $query = UserCredential::find()->withActiveCredentialType();

        if(isset($this->user_id)) {
            $query->andFilterWhere([$this::tableName().'.user_id' => $this->user_id]);
        }

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' =>  [
                'defaultOrder' => ['ordering'=>SORT_ASC],
                'attributes' => [
                    'id'=>[
                        'asc' =>['id'=>SORT_ASC],
                        'desc' =>['id'=>SORT_DESC],
                        'default' => SORT_ASC
                    ],
                    'credential_type_id'=>[
                        'asc' =>['[[credential_type.credential_type_name]]'=>SORT_ASC],
                        'desc' =>['[[credential_type.credential_type_name]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'assigned_number'=>[
                        'asc' =>['[[user_credential.assigned_number]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.assigned_number]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'file_name'=>[
                        'asc' =>['[[user_credential.file_name]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.file_name]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    /*'updated_at'=>[
                        'asc' =>['[[user_credential.updated_at]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.updated_at]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'updated_by'=>[
                        'asc' =>['[[user_credential.updated_by]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.updated_by]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],*/
                    'expire_date'=>[
                        'asc' =>['[[user_credential.expire_date]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.expire_date]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'approved_at'=>[
                        'asc' =>['[[user_credential.approved_at]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.approved_at]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'status'=>[
                        'asc' =>['[[user_credential.status]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.status]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ],
                    'ordering' => [
                        'asc' =>['[[user_credential.ordering]]'=>SORT_ASC],
                        'desc' =>['[[user_credential.ordering]]'=>SORT_DESC],
                        //'default' => SORT_ASC
                    ]
                ],
            ],
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        if (!($this->load($params) && $this->validate()))
        {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            '[[user_credential.id]]' => $this->id,
            '[[user_credential.credential_type_id]]' => $this->credential_type_id,
            "date([[user_credential.expire_date]])" => ConstHelper::formatDate($this->expire_date),
            //'[[user_credential.created_by]]' => $this->created_by,
            //'[[user_credential.updated_by]]' => $this->updated_by,
            //"date([[user_credential.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            //"date([[user_credential.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
            "date([[user_credential.approved_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->approved_at),
            '[[user_credential.ordering]]' => $this->ordering,
            '[[user_credential.status]]' => $this->status
        ]);

        $query->andFilterWhere(['ilike', new Expression("decode([[user_credential.assigned_number]], 'base64')::text"), $this->assigned_number])
            ->andFilterWhere(['ilike', '[[user_credential.mime_type]]', $this->mime_type])
            ->andFilterWhere(['ilike', '[[user_credential.file_name]]', $this->file_name]);

        return $dataProvider;
    }
}
