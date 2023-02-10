<?php

namespace common\jobs;

use yii\base\Event;
use yii\base\BaseObject;
use yii\queue\RetryableJobInterface;
use Exception;
use common\models\User;
use common\models\Article;

/**
 * Class ArticleEmailNotification.
 *
 * @property int $ttr
 */
class ArticleEmailSenderJob extends BaseObject implements RetryableJobInterface
{
    /**
     * @var integer $user_id
     */
    public $user_id;

    /**
     * @var integer $article_id
     */
    public $article_id;

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function execute($queue)
    {
        if (empty($this->user_id)){
            throw new Exception('User Id is missing parameter');
        }
        if (empty($this->article_id)){
            throw new Exception('Article Id is missing parameter');
        }

        $article = Article::findOne($this->article_id);
        $user = User::findOne($this->user_id);
        $article->on(Article::EVENT_NEWS_CREATED, [$user, 'newsCreatedEventHandler'], 'email');
        $article->trigger(Article::EVENT_NEWS_CREATED, new Event(['sender' => $user]));
    }

    /**
     * @inheritdoc
     */
    public function getTtr()
    {
        return 60;
    }

    /**
     * @inheritdoc
     */
    public function canRetry($attempt, $error)
    {
        return $attempt < 3;
    }
}
