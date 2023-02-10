<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\helpers\ConstHelper;
use common\rbac\models\AuthItem;
use common\models\UserCredential;

/**
 * ProviderSearch represents the model behind the search form for common\models\User.
 */
class ProviderSearch extends User
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
     * @var int $service_id
     */
    public $service_id;

    /**
     * @var string $credential_status
     */
    public $credential_status;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email', 'title', 'full_name', 'first_name', 'last_name', 'status', 'item_name', 'service_id', 'credential_status', 'language', 'covered_county'], 'safe'],
        ];
    }

    /**
     * Returns a list of scenarios and the corresponding active attributes.
     *
     * @return array
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param  array $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        // we make sure that only providers showing here
        $query = User::find()->joinWith('role')->where(['=', 'auth_assignment.item_name', User::USER_PROVIDER]);

        $query->select(['user.*', 'auth_assignment.item_name', 'service.service_name', 'user_credential.credential_status']);
        $query->joinWith('service');

        $userCredentialSubQuery = UserCredential::find()
            ->select(['user_id', 'credential_status' => 'string_agg("status", \',\')'])
            ->groupBy('user_id');
        $query->leftJoin(['user_credential' => $userCredentialSubQuery], '"user_credential"."user_id" = "user"."id"');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created_at'=>SORT_DESC]],
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        // make full_name sortable
        $dataProvider->sort->attributes['full_name'] = [
            'asc' => ['CONCAT_WS(\' \', [[user.first_name]], [[user.last_name]])' => SORT_ASC],
            'desc' => ['CONCAT_WS(\' \', [[user.first_name]], [[user.last_name]])' => SORT_DESC],
        ];

        // make service sortable
        $dataProvider->sort->attributes['service_id'] = [
            'asc' => ['[[service.service_name]]' => SORT_ASC],
            'desc' => ['[[service.service_name]]' => SORT_DESC],
        ];

        // make credential_status sortable
        $dataProvider->sort->attributes['credential_status'] = [
            'asc' => ['[[user_credential.credential_status]]' => SORT_ASC],
            'desc' => ['[[user_credential.credential_status]]' => SORT_DESC],
        ];

        if (!($this->load($params) && $this->validate()))
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '[[user.id]]' => $this->id,
            '[[user.status]]' => $this->status,
            '[[service.id]]' => $this->service_id,
            "date([[user.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            "date([[user.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
        ]);

        $query->andFilterWhere(['ilike', '[[user.username]]', $this->username])
            ->andFilterWhere(['ilike', '[[user.email]]', $this->email])
            ->andFilterWhere(['ilike', '[[user.title]]', $this->title])
            ->andFilterWhere(['ilike', '[[user.first_name]]', $this->first_name])
            ->andFilterWhere(['ilike', '[[user.last_name]]', $this->last_name])
            ->andFilterWhere(['ilike', 'CONCAT_WS(\' \', [[user.first_name]], [[user.last_name]])', $this->full_name])
            ->andFilterWhere(['ilike', '[[user.language]]::text', $this->language])
            ->andFilterWhere(['ilike', '[[user.covered_county]]::text', $this->covered_county])
            ->andFilterWhere(['ilike', '[[auth_assignment.item_name]]', $this->item_name]);

        if (!empty($this->credential_status)) {
            if ($this->credential_status === ConstHelper::FLAG_YES) {
                $query->andWhere(['and',
                    ['NOT ILIKE', '[[user_credential.credential_status]]', UserCredential::STATUS_PENDING],
                    ['NOT ILIKE', '[[user_credential.credential_status]]', UserCredential::STATUS_EXPIRED],
                    ['IS NOT', '[[user_credential.credential_status]]', null]
                ]);
            } elseif ($this->credential_status === ConstHelper::FLAG_NO) {
                $query->andWhere(['or',
                    ['ILIKE', '[[user_credential.credential_status]]', UserCredential::STATUS_PENDING],
                    ['ILIKE', '[[user_credential.credential_status]]', UserCredential::STATUS_EXPIRED],
                    ['IS', '[[user_credential.credential_status]]', null]
                ]);
            }
        }

        return $dataProvider;
    }

    /**
     * Returns the array of possible user roles.
     * NOTE: used in user/index view.
     *
     * @return mixed
     */
    public static function getRolesList()
    {
        $roles = [];
        foreach (AuthItem::getRoles() as $item_name)
        {
            $roles[$item_name->name] = $item_name->name;
        }
        return $roles;
    }
}
