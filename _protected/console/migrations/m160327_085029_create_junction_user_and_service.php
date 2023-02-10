<?php

use yii\db\Migration;

/**
 * Class m160327_085029_create_junction_user_and_service
 *
 */
class m160327_085029_create_junction_user_and_service extends Migration
{
    public function up()
    {
        $tableOptions = null;
        
        if ($this->db->driverName === 'mysql') 
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        $this->createTable('{{%user_service}}', [
            'user_id' => $this->integer()->notNull(),
            'service_id' => $this->integer()->notNull(),
            'PRIMARY KEY(user_id, service_id)'
        ], $tableOptions);

        $this->createIndex('idx-unique-user_service-user_id-service-id', 'user_service', ['user_id', 'service_id'], true);

        $this->addForeignKey('fk-user_service-user_id', 'user_service', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk-user_service-service_id', 'user_service', 'service_id', 'service', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-user_service-user_id', '{{%user_service}}');
        $this->dropForeignKey('fk-user_service-service_id', '{{%user_service}}');

        $this->dropIndex('idx-unique-user_service-user_id-service-id', '{{%user_service}}');

        $this->dropTable('{{%user_service}}');
    }

}
