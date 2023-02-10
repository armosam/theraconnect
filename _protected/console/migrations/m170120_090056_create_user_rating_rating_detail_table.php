<?php

use common\helpers\ConstHelper;
use common\models\User;
use yii\db\Migration;

/**
 * Handles the creation of table `user_rating` and `rating_detail`.
 */
class m170120_090056_create_user_rating_rating_detail_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        $this->createTable('{{%user_rating}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unique()->notNull(),
            'current_rating' => $this->decimal(2, 1)->notNull()->defaultValue(0.0),
            'star1' => $this->integer()->notNull()->defaultValue(0),
            'star2' => $this->integer()->notNull()->defaultValue(0),
            'star3' => $this->integer()->notNull()->defaultValue(0),
            'star4' => $this->integer()->notNull()->defaultValue(0),
            'star5' => $this->integer()->notNull()->defaultValue(0)
        ], $tableOptions);

        $this->createTable('{{%rating_detail}}', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string(40)->notNull()->unique()->defaultExpression('uuid_in((md5((random())::text))::cstring)'),
            'user_id' => $this->integer()->notNull(),
            'patient_id' => $this->integer()->notNull(),
            'review_rate' => $this->integer()->notNull()->defaultValue(0),
            'review_content' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE),
        ], $tableOptions);

        $this->createIndex('idx-rating_detail-user_id', '{{%rating_detail}}', ['user_id', 'patient_id'], true);

        $this->addForeignKey(
            'fk-user_rating-user_id-user-id',
            '{{%user_rating}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE',
            'NO ACTION'
        );
        $this->addForeignKey(
            'fk-rating_detail-user_id-user_rating-user_id',
            '{{%rating_detail}}',
            'user_id',
            '{{%user_rating}}',
            'user_id',
            'CASCADE',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-rating_detail-patient_id-patient-id',
            '{{%rating_detail}}',
            'patient_id',
            '{{%patient}}',
            'id',
            'SET NULL',
            'NO ACTION'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_rating-user_id-user-id', '{{%user_rating}}');
        $this->dropForeignKey('fk-rating_detail-user_id-user_rating-user_id', '{{%rating_detail}}');
        $this->dropForeignKey('fk-rating_detail-patient_id-patient-id', '{{%rating_detail}}');

        $this->dropIndex('idx-rating_detail-user_id', '{{%rating_detail}}');

        $this->dropTable('{{%user_rating}}');
        $this->dropTable('{{%rating_detail}}');
    }
}
