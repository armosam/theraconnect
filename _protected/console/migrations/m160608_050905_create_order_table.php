<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * Class m160608_050905_create_order_table
 */
class m160608_050905_create_order_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%order}}', [
            'id' => $this->primaryKey(),
            'order_number' => $this->string(10)->notNull(),
            'patient_id' => $this->integer()->notNull(),
            'patient_name' => $this->string(255)->notNull(),
            'patient_number' => $this->string(255)->notNull(),
            'physician_name' => $this->string(255),
            'physician_address' => $this->string(255),
            'physician_phone_number' => $this->string(255),
            'service_id' => $this->integer()->notNull(),
            'service_name' => $this->string(255)->notNull(),
            'service_frequency' => $this->string(255),
            'frequency_status' => $this->string(1), //S - submitted, A - Approved
            'service_rate' => $this->integer(),
            'certification_start_date' => $this->date(),
            'certification_end_date' => $this->date(),
            'allow_transfer_to' => $this->string(1), //Y,N
            'comment' => $this->text(),
            'submitted_by' => $this->integer(),
            'submitted_at' => 'TIMESTAMPTZ(0)',
            'accepted_by' => $this->integer(),
            'accepted_at' => 'TIMESTAMPTZ(0)',
            'completed_by' => $this->integer(),
            'completed_at' => 'TIMESTAMPTZ(0)',
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue('P'), // P-pending,S-submitted,A-accepted,C-completed
        ], $tableOptions);

        // create indexes for table `order`
        $this->createIndex('idx-order-patient_id', '{{%order}}', 'patient_id');
        $this->createIndex('idx-order-service_id', '{{%order}}', 'service_id');
        $this->createIndex('idx-order-status', '{{%order}}', 'status');

        // add foreign key for table `patient`
        $this->addForeignKey(
            'fk-order-patient_id-patient-id',
            '{{%order}}',
            'patient_id',
            '{{%patient}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
        // add foreign key for table `service`
        $this->addForeignKey(
            'fk-order-service_id-service-id',
            '{{%order}}',
            'service_id',
            '{{%service}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-order-patient_id-patient-id',
            '{{%order}}'
        );
        $this->dropForeignKey(
            'fk-order-service_id-service-id',
            '{{%order}}'
        );

        // Drop indexes
        $this->dropIndex('idx-order-patient_id', '{{%order}}');
        $this->dropIndex('idx-order-service_id', '{{%order}}');
        $this->dropIndex('idx-order-status', '{{%order}}');

        // Drop tables
        $this->dropTable('{{%order}}');
    }

}
