<?php

namespace common\models\searches;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use common\models\Article;

/**
 * ArticleSearch represents the model behind the search form about `app\models\Article`.
 */
class ArticleSearch extends Article
{
    /**
     * Returns the validation rules for attributes.
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['status', 'category'], 'string'],
            [['title', 'summary', 'content'], 'trim'],
            [['title', 'summary', 'content', 'user_id', 'start_date', 'end_date', 'created_at', 'updated_at'], 'safe'],
            [['upload_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg, gif, bmp'],
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
     * @param array   $params    The search query params.
     * @param integer $pageSize  The number of results to be displayed per page.
     * @param boolean $published Whether articles have to be published or not.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 3, $published = false)
    {
        $query = Article::find();
//            ->groupBy('article.id');

        // this means that editor is trying to see articles
        // we will allow him to see published ones and drafts made by him
        if ($published === true)
        {
            $query->where(['status' => Article::STATUS_PUBLISHED]);
            $query->orWhere(['user_id' => Yii::$app->user->id]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> [
                'attributes' => ['[[article.id]]' => SORT_DESC, '[[article.title]]', '[[article.user_id]]', '[[article.category]]', '[[article.start_date]]', '[[article.end_date]]', '[[article.status]]']
            ],
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        if(!empty($this->user_id)){
            $query->leftJoin('user_detail', '[[user.user_id]] = [[article.user_id]]');
            $query->andFilterWhere(['like', "concat_ws(' ', [[user.first_name]], [[user.last_name]])", $this->user_id]);
        }

        $query->andFilterWhere([
            '[[article.id]]' => $this->id,
            '[[article.status]]' => $this->status,
            '[[article.category]]' => $this->category,
        ]);

        if(!empty($this->title)){
            $query->andFilterWhere(['ilike', '[[article.title]]', $this->title]);
        }

        if(!empty($this->start_date)){
            $start_date = new Expression("'$this->start_date'::TIMESTAMP");
            $query->andFilterWhere(['>=', '[[article.start_date]]', $start_date]);
        }

        if(!empty($this->end_date)){
            $end_date = new Expression("'$this->end_date'::TIMESTAMP + INTERVAL '1 day' - INTERVAL '1 second'");
            $query->andFilterWhere(['<=', '[[article.end_date]]', $end_date]);
        }

        return $dataProvider;
    }
}
