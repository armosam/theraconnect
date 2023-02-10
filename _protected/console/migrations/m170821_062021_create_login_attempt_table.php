<?php

use yii\db\Migration;

/**
 * Class m170821_062021_create_login_attempt_table
 */
class m170821_062021_create_login_attempt_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%login_attempt}}', [
            'id' => $this->primaryKey(),
            'ip' => $this->string(64)->notNull(),
            'failed_attempts' => $this->integer()->notNull()->defaultValue(1),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_at' => 'TIMESTAMPTZ(0)',
        ], $tableOptions);

        $this->createIndex('idx-login_attempt-ip', '{{%login_attempt}}', 'ip');
    }

    public function safeDown()
    {
        $this->dropIndex('idx-login_attempt-ip', '{{%login_attempt}}');
        $this->dropTable('{{%login_attempt}}');
    }

}
