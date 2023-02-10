<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * Handles the creation for table `user_avatar`.
 */
class m160511_062141_create_user_avatar_table extends Migration
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

        $this->createTable('{{%user_avatar}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'mime_type' => $this->string(50),
            'file_size' => $this->integer(),
            'file_name' => $this->string(255)->notNull(),
            'file_content' => $this->binary(),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE),
        ], $tableOptions);

        $this->createIndex('idx-user_avatar-user_id-status', '{{%user_avatar}}', ['user_id', 'status']);

        // add foreign key for `user.id`
        $this->addForeignKey(
            'fk-user-id-user_avatar-user_id',
            '{{%user_avatar}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user-id-user_avatar-user_id',
            '{{%user_avatar}}'
        );
        $this->dropIndex('idx-user_avatar-user_id-status', '{{%user_avatar}}');
        $this->dropTable('{{%user_avatar}}');
    }

}
