<?php

namespace common\models;

use Yii;
use yii\base\Event;
use yii\db\Expression;
use common\models\notification\EmailNotification;
use common\models\notification\SMSNotification;
use common\exceptions\EmailNotificationException;
use common\exceptions\SMSNotificationException;

/**
 * Class Article
 * @package frontend\models\base
 *
 * @property int $createdBy
 * @property mixed $authorName
 * @property string $articleStatusName
 * @property array|string $articleStatusList
 * @property array|string $articleCategoryList
 *
 */
class Article extends base\Article
{
    /**
     * Setting some attributes by default before an insert or update of the table
     *
     * @param bool $insert
     * @return boolean
     */
    public function beforeSave($insert) {

        $this->setAttributes([
            'user_id' => Yii::$app->has('user') ? Yii::$app->user->id : User::USER_SYSTEM_ADMIN_ID
        ]);

        if (!parent::beforeSave($insert)) {
            return false;
        }

        return true;
    }

    /**
     * Resize uploaded photos to correct size
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        /*$transaction = Yii::$app->db->beginTransaction();
        try {

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            echo $e->getTraceAsString();
        }*/

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * After finding record it will load particular parameters of model
     */
    public function afterFind()
    {
        parent::afterFind();
        // load translations from translation relation to particular parameters

    }

    /**
     * Returns an ID that can uniquely identify a user file.
     *
     * @return int|mixed|string
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Gets the id of the article creator.
     * NOTE: needed for RBAC Author rule.
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->user_id;
    }

    /**
     * Gets the author name from the related User table.
     *
     * @return mixed
     */
    public function getAuthorName()
    {
        return $this->user->username;
    }

    /**
     * Returns the article status in nice format.
     *
     * @param  bool|integer $status Status integer value if sent to method.
     * @return string  Nicely formatted status.
     */
    public function getArticleStatusName($status = null)
    {
        $status = ($status === false) ? $this->status : $status ;
        return self::getArticleStatusList($status);
    }

    /**
     * Returns the array of possible article status values.
     * @param bool|string $selected
     * @return string|array
     */
    public static function getArticleStatusList($selected = false)
    {
        $data = [
            self::STATUS_DRAFT     => Yii::t('app', 'Draft'),
            self::STATUS_PUBLISHED => Yii::t('app', 'Published'),
        ];
        if($selected !== false){
            return isset($data[$selected]) ? $data[$selected] : $selected;
        }
        return $data;
    }

    /**
     * Returns the article category in the nice format.
     *
     * @param  null|integer $category Category integer value if sent to method.
     * @return string A nicely formatted category.
     */
    public function getArticleCategoryName($category = null)
    {
        $category = empty($category) ? $this->category : $category;
        return self::getArticleCategoryList($category);
    }

    /**
     * Returns the list of article categories or selected category name if category provided.
     *
     * @param  false|string $selected Category integer value if sent to method.
     * @return string|array Nicely formatted categories.
     */
    public static function getArticleCategoryList($selected = false)
    {
        $data = [
            self::CATEGORY_DISCOUNT => Yii::t('app', 'Discounts'),
            self::CATEGORY_NEWS => Yii::t('app', 'News'),
            self::CATEGORY_SOCIETY => Yii::t('app', 'Society'),
        ];
        if($selected !== false){
            return isset($data[$selected]) ? $data[$selected] : $selected;
        }
        return $data;
    }

    /**
     * Returns true if there is any active article.
     *
     * @return boolean
     */
    public static function hasActiveArticle()
    {
        $activeArticles = Article::find()
            ->where(['OR',
                ['IS', 'start_date', (new Expression('NULL'))],
                ['<=', 'start_date', (new Expression('CURRENT_DATE'))],
            ])
            ->andWhere(['OR',
                ['IS', 'end_date', (new Expression('NULL'))],
                ['>=', 'end_date', (new Expression('CURRENT_DATE'))],
            ])
            ->andWhere(['status' => Article::STATUS_PUBLISHED])
            ->all();
        if (!empty($activeArticles)) {
            return true;
        }
        return false;
    }

    /**  Article Event Handlers */

    /**
     * Handler method for newsCreatedEvent
     * @param Event $event
     */
    public function newsCreatedEventHandler(Event $event)
    {
        /** @var User $user */
        $user = $event->sender;
        $data = ['article' => $this];
        try{
            if ($event->sender->note_email_news_and_promotions === 'Y') {
                $subject = Yii::t('app', 'News and Promotions.');
                $emailNotification = new EmailNotification($user, self::NOTIFICATION_NEWS_CREATED, $subject, $data);
                if(!$emailNotification->send()){
                    throw new EmailNotificationException(Yii::t('app', 'Sorry, we are unable to send you an email notification about news creation by provided email address.'));
                }
            }
            if ($event->sender->note_sms_news_and_promotions === 'Y') {
                $message = Yii::t('app', 'News and Promotions. {url}', ['url' => Yii::$app->urlManagerToFront->createAbsoluteUrl(['article/item', 'id' => $this->id])]);
                $smsNotification = new SMSNotification($user, $message);
                if(!$smsNotification->send()){
                    throw new SMSNotificationException(Yii::t('app', 'Sorry, we are unable to send you SMS notification about news creation by provided phone number.'));
                }
            }
        }catch(\Exception $e){
            Yii::error($e->getMessage().PHP_EOL.$e->getTraceAsString(), 'Article-'.__FUNCTION__);
            Yii::$app->session->addFlash('error', $e->getMessage());
        }
    }
}