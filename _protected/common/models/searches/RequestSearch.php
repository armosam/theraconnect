<?php

namespace common\models\searches;

use Yii;
use yii\data\Sort;
use yii\base\Model;
use yii\db\Expression;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\models\Order;
use common\models\UserCredential;
use common\helpers\ConstHelper;

/**
 * ProviderSearch represents the model behind the search form for common\models\User.
 *
 * @property int $pageSize
 */
class RequestSearch extends Order
{
    /**
     * How many users we want to display per page.
     *
     * @var int
     */
    private $_pageSize = 10;

    /**
     * @var string $patient_ordering
     */
    public $patient_ordering;

    /**
     * Location of patient service
     * @var string $patient_location
     */
    public $patient_location;

    /**
     * Radius of patient service
     * @var integer $patient_distance
     */
    public $patient_distance;

    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['patient_distance'], 'integer'],
            [['patient_ordering', 'patient_location', 'patient_distance'], 'safe'],
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
     *
     * RULES TO SHOW AVAILABLE ORDERS FOR RPT AND PTA
     * ************************************************
     * For RPT:
     * - Order should be submitted status order.status = 'S'
     * - Order should not be assigned to any RPT order.user_order.user_id is null
     *
     * For PTA:
     * - Order should be accepted status order.status = 'A'
     * - Order should be assigned to active RPT order.user_order.user_id is not null AND order.user_order.status = 'A' AND order.user_order.user.title = 'RPT'
     * - Order should be allowed to transfer to PTA order.allow_transfer_to = 'Y'
     * - Order frequency should not be null and should be approved order.service_frequency is not null AND order.frequency_status = 'A'
     *
     *
     * @param array $params
     * @return ActiveDataProvider
     * @throws NotFoundHttpException
     */
    public function search($params)
    {
        $provider = User::currentLoggedUser();

        if($provider === null || empty($provider->service)){
            throw new NotFoundHttpException('There is no active service on your profile. Please contact administration to add your service.');
        }

        // Check if all credentials are approved for logged in provider
        if (empty($provider->userCredentials)) {
            throw new NotFoundHttpException('There are no credentials added in your profile. Please add all necessary credentials and get them approved from administration.');
        } elseif(!$provider->isProviderCredentialsApproved()) {
            throw new NotFoundHttpException('There is a credential not approved by administration in your profile. Please contact administration to approve your credentials.');
        }

        /**
         * Get lat lng from string $provider_location
         */
        //$defaultLocation = GeoDataHelper::getLocationFromIPAddress(Yii::$app->getRequest()->getUserIP());
        //$distance_exp = '(3959 * acos(cos(radians('.$defaultLocation->latitude.')) * cos(radians("user"."lat")) * cos(radians("user"."lng") - radians('.$defaultLocation->longitude.')) + sin(radians('.$defaultLocation->latitude.')) * sin(radians("user"."lat"))))';

        $query = Order::find();
        $query->andFilterWhere(['[[order.service_id]]' => $provider->service->id]);

        if ($provider->title === User::USER_TITLE_RPT) {
            $query->joinWith(['orderUsers']);
            $query->andFilterWhere(['IS', '[[user_order.user_id]]', new Expression('NULL')]);
            $query->andFilterWhere(['=', '[[order.status]]', Order::ORDER_STATUS_SUBMITTED]);
        } elseif ($provider->title === User::USER_TITLE_PTA) {
            $query->joinWith(['orderRPT']);
            $query->andFilterWhere(['IS NOT', '[[user.id]]', new Expression('NULL')]);
            $query->andFilterWhere(['=', '[[order.allow_transfer_to]]', ConstHelper::FLAG_YES]);
            $query->andFilterWhere(['=', '[[order.status]]', Order::ORDER_STATUS_ACCEPTED]);
            $query->andFilterWhere(['IS NOT', '[[order.service_frequency]]', new Expression('NULL')]);
            $query->andFilterWhere(['=', '[[order.frequency_status]]', Order::ORDER_FREQUENCY_STATUS_APPROVED]);
        } else {
            throw new NotFoundHttpException('No permission to open this page.');
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            /*'sort'=> new Sort([
                'attributes' => [
                    '"distance"."patient_distance"' => [
                        'asc' => [$distance_exp => SORT_ASC],
                        'desc' => [$distance_exp => SORT_DESC],
                    ],
                ]
            ]),*/
            'pagination' => [
                'pageSize' => $this->_pageSize,
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            //$query->andFilterWhere(['<', new JsExpression($distance_exp), $this->patient_distance]);
            return $dataProvider;
        }

        // This part gets distance from selected location and radius and sets ordering by distance expresion
        /*if(!empty($this->patient_location)){
            $requestedLocation = GeoDataHelper::getLocationFromAddress($this->patient_location);
            $distance_exp = '(3959 * acos(cos(radians('.$requestedLocation->latitude.')) * cos(radians("user"."lat")) * cos(radians("user"."lng") - radians('.$requestedLocation->longitude.')) + sin(radians('.$requestedLocation->latitude.')) * sin(radians("user"."lat"))))';
            $dataProvider->sort->attributes['"distance"."provider_distance"'] = [
                'asc' => [$distance_exp => SORT_ASC],
                'desc' => [$distance_exp => SORT_DESC]
            ];
        }

        $query->andFilterWhere(['<', new JsExpression($distance_exp), $this->patient_distance]);

        // item ordering switch
        switch($this->patient_ordering){

            case ConstHelper::SEARCH_ORDERING_BASED_ON_DISTANCE_ASC:
                $query->addGroupBy(['"user"."lat"', '"user"."lng"']);
                $dataProvider->sort->defaultOrder = ['"distance"."provider_distance"' => SORT_ASC ];
                break;
            case ConstHelper::SEARCH_ORDERING_BASED_ON_DISTANCE_DESC:
                $query->addGroupBy(['"user"."lat"', '"user"."lng"']);
                $dataProvider->sort->defaultOrder = ['"distance"."provider_distance"' => SORT_DESC ];
                break;
        }*/
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
