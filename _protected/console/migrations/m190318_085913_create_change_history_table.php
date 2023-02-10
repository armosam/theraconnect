<?php

use common\helpers\ConstHelper;
use common\models\User;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%change_history}}`.
 */
class m190318_085913_create_change_history_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%change_history}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'field_name' => $this->string()->notNull(),
            'old_value' => $this->string(),
            'new_value' => $this->string()->notNull(),
            'verification_code' => $this->string(255)->unique()->defaultExpression('NULL'),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' =>  'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::FLAG_NO),
        ]);

        // create indexes and foreign key for article translation table
        $this->createIndex('idx-change_history-user_id', '{{%change_history}}', ['user_id']);

        $this->addForeignKey('fk-change_history-user_id-user-id',
            '{{%change_history}}',
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
        $this->dropForeignKey('fk-change_history-user_id-user-id', '{{%change_history}}');
        $this->dropTable('{{%change_history}}');
    }
}
