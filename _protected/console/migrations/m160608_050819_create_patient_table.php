<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * Class m160608_050819_create_patient_table
 */
class m160608_050819_create_patient_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%patient}}', [
            'id' => $this->primaryKey(),
            'customer_id' => $this->integer()->notNull(),
            'patient_number' => $this->string(15)->notNull(),
            'start_of_care' => $this->date()->notNull(),
            'first_name' => $this->string(255)->notNull(),
            'middle_name' => $this->string(255),
            'last_name' => $this->string(255)->notNull(),
            'gender' => $this->string(1),
            'address' => $this->string(255),
            'city' => $this->string(255),
            'state' => $this->string(255),
            'country' => $this->string(255),
            'zip_code' => $this->string(15),
            'birth_date' => $this->date(),
            'ssn' => $this->string(255),
            'phone_number' => $this->string(15),
            'preferred_language' => $this->string(15),
            'preferred_gender' => $this->string(1),
            'emergency_contact_name' => $this->string(255),
            'emergency_contact_number' => $this->string(15),
            'emergency_contact_relationship' => $this->string(255),
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE), // A-active,P-passive,D-deleted
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
        ], $tableOptions);

        // create indexes for table `patient`
        $this->createIndex('idx-patient-customer_id', '{{%patient}}', 'customer_id');
        $this->createIndex('idx-patient-status', '{{%patient}}', 'status');

        
        // add foreign key for table `patient`
        $this->addForeignKey(
            'fk-patient-customer_id-user-id',
            '{{%patient}}',
            'customer_id',
            '{{%user}}',
            'id',
            'NO ACTION',
            'NO ACTION'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-patient-customer_id-user-id',
            '{{%patient}}'
        );

        // Drop indexes
        $this->dropIndex('idx-patient-customer_id', '{{%patient}}');
        $this->dropIndex('idx-patient-status', '{{%patient}}');
      
        // Drop tables
        $this->dropTable('{{%patient}}');
    }

}
