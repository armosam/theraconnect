<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * Class m160608_050906_create_order_document_table
 */
class m160608_050906_create_order_document_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order_document}}', [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer()->notNull(),
            'document_type' => $this->string(1)->notNull(), // I -> intake, F -> Form-485, H -> MedicalHistory
            'mime_type' => $this->string(255),
            'file_size' => $this->integer(),
            'file_name' => $this->string(255),
            'file_content' => $this->binary(),
            'file_content_uri' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE), // A-active,P-passive,D-deleted
            'ordering' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        // create indexes for table `order`
        $this->createIndex('idx-order_document-order_id', '{{%order_document}}', 'order_id');
        $this->createIndex('idx-order_document-status', '{{%order_document}}', 'status');
        
        // add foreign key for table `order`
        $this->addForeignKey(
            'fk-order_document-order_id-order-id',
            '{{%order_document}}',
            'order_id',
            '{{%order}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-order_document-order_id-order-id',
            '{{%order_document}}'
        );

        // Drop indexes
        $this->dropIndex('idx-order_document-order_id', '{{%order_document}}');
        $this->dropIndex('idx-order_document-status', '{{%order_document}}');
      
        // Drop tables
        $this->dropTable('{{%order_document}}');
    }

}
