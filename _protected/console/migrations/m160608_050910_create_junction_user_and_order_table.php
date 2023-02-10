<?php

use yii\db\Migration;

/**
 * Class m160608_050910_create_junction_user_and_order_table
 *
 */
class m160608_050910_create_junction_user_and_order_table extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql')
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%user_order}}', [
            'user_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'status' => $this->string(1)->notNull()->defaultValue('A'),
            'PRIMARY KEY(user_id, order_id)'
        ], $tableOptions);

        $this->createIndex('idx-user_order-user_id', 'user_order', 'user_id');
        $this->createIndex('idx-user_order-order_id', 'user_order', 'order_id');

        $this->addForeignKey('fk-user_order-user_id', 'user_order', 'user_id', 'user', 'id', 'CASCADE');
        $this->addForeignKey('fk-user_order-order_id', 'user_order', 'order_id', 'order', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk-user_order-user_id', '{{%user_order}}');
        $this->dropForeignKey('fk-user_order-order_id', '{{%user_order}}');

        $this->dropIndex('idx-user_order-user_id', '{{%user_order}}');
        $this->dropIndex('idx-user_order-order_id', '{{%user_order}}');

        $this->dropTable('{{%user_order}}');
    }

}
