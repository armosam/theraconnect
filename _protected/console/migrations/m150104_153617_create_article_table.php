<?php

use common\models\base\Article;
use common\models\User;
use yii\db\Migration;

/**
 * Class m150104_153617_create_article_table
 */
class m150104_153617_create_article_table extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%article}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'summary' => $this->text()->notNull(),
            'content' => $this->text()->notNull(),
            'embed_content' => $this->text()->defaultValue(null),
            'start_date' => $this->date(),
            'end_date' => $this->date(),
            'category' => $this->string(1)->notNull()->defaultValue(Article::CATEGORY_NEWS),
            'status' => $this->string(1)->notNull()->defaultValue(Article::STATUS_DRAFT),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'email_sent_by' => $this->integer(),
            'email_sent_at' => 'TIMESTAMPTZ(0)',
            'FOREIGN KEY (user_id) REFERENCES {{%user}}(id) ON DELETE CASCADE ON UPDATE CASCADE',

        ], $tableOptions);

    }

    public function down()
    {
        $this->dropTable('{{%article}}');
    }
}
