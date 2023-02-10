<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * Class m201130_044440_create_table_user_signature
 */
class m201130_044440_create_table_user_signature extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_signature}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'signature' => $this->text(),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE),
        ], $tableOptions);

        $this->createIndex('idx-user_signature-user_id', '{{%user_signature}}', ['user_id']);

        // add foreign key for `user.id`
        $this->addForeignKey(
            'fk-user-id-user_signature-user_id',
            '{{%user_signature}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-user-id-user_signature-user_id',
            '{{%user_signature}}'
        );
        $this->dropIndex('idx-user_signature-user_id', '{{%user_signature}}');
        $this->dropTable('{{%user_signature}}');
    }
}
