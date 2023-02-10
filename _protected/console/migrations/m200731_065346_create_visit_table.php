<?php

use yii\db\Migration;
use common\models\User;

/**
 * Handles the creation of table `{{%visit}}`.
 */
class m200731_065346_create_visit_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%visit}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'visited_at' => 'TIMESTAMPTZ(0)',
            'comment' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
        ], $tableOptions);

        // create indexes for table `order`
        $this->createIndex('idx-visit-order_id', '{{%visit}}', 'order_id');

        // add foreign key for table `patient`
        $this->addForeignKey(
            'fk-visit-order_id-order-id',
            '{{%visit}}',
            'order_id',
            '{{%order}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-visit-order_id-order-id',
            '{{%visit}}'
        );

        // Drop indexes
        $this->dropIndex('idx-visit-order_id', '{{%visit}}');

        // Drop table
        $this->dropTable('{{%visit}}');
    }
}
