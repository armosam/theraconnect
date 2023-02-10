<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\User;
use common\helpers\ConstHelper;
use common\rbac\models\AuthItem;

/**
 * CustomerSearch represents the model behind the search form for common\models\User.
 */
class CustomerSearch extends User
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
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email', 'full_name', 'agency_name', 'rep_position', 'first_name', 'last_name', 'status', 'item_name'], 'safe'],
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
        $query = User::find()->joinWith('role')->where(['=', 'auth_assignment.item_name', User::USER_CUSTOMER]);
        $query->select(['user.*', 'auth_assignment.item_name']);

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

        if (!($this->load($params) && $this->validate()))
        {
            return $dataProvider;
        }

        $query->andFilterWhere([
            '[[user.id]]' => $this->id,
            '[[user.status]]' => $this->status,
            "date([[user.created_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->created_at),
            "date([[user.updated_at]] AT TIME ZONE '".Yii::$app->timeZone."')" => ConstHelper::formatDate($this->updated_at),
        ]);

        $query->andFilterWhere(['ilike', '[[user.username]]', $this->username])
            ->andFilterWhere(['ilike', '[[user.email]]', $this->email])
            ->andFilterWhere(['ilike', '[[user.agency_name]]', $this->agency_name])
            ->andFilterWhere(['ilike', '[[user.rep_position]]', $this->rep_position])
            ->andFilterWhere(['ilike', '[[user.first_name]]', $this->first_name])
            ->andFilterWhere(['ilike', '[[user.last_name]]', $this->last_name])
            ->andFilterWhere(['ilike', 'CONCAT_WS(\' \', [[user.first_name]], [[user.last_name]])', $this->full_name])
            ->andFilterWhere(['ilike', '[[auth_assignment.item_name]]', $this->item_name]);

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
