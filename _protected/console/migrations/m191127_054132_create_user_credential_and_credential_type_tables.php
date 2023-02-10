<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;
use common\models\base\UserCredential;

/**
 * Handles the creation of tables `{{%credential_type}}`, `{{%user_credential}}`.
 */
class m191127_054132_create_user_credential_and_credential_type_tables extends Migration
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

        // credential_type table
        $this->createTable('{{%credential_type}}', [
            'id' => $this->primaryKey(),
            'icon_class' => $this->string(255),
            'credential_type_name' => $this->string(255)->notNull(),
            'assigned_number_label' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE),
            'ordering' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx-credential_type-status', '{{%credential_type}}', 'status');


        // user_credential table
        $this->createTable('{{%user_credential}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'credential_type_id' => $this->integer()->notNull(),
            'assigned_number' => $this->string(255),
            'mime_type' => $this->string(255),
            'file_size' => $this->integer(),
            'file_name' => $this->string(255),
            'file_content' => $this->binary(),
            'file_content_uri' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'approved_by' => $this->integer(),
            'approved_at' => 'TIMESTAMPTZ(0)',
            'expire_date' => $this->date()->defaultValue(null),
            'status' => $this->string(1)->notNull()->defaultValue(UserCredential::STATUS_PENDING), //P-pending, A-approved, E-expired
            'ordering' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx-user_credential-user_id', 'user_credential', 'user_id');
        $this->createIndex('idx-user_credential-credential_type_id', '{{%user_credential}}', 'credential_type_id');
        $this->createIndex('idx-user_credential-status', '{{%user_credential}}', 'status');

        $this->addForeignKey('fk-user_credential-user_id-user-id',
            'user_credential',
            'user_id',
            'user',
            'id',
            'CASCADE'
        );

        $this->addForeignKey('fk-user_credential-credential_type_id-credential_type-id',
            '{{%user_credential}}',
            'credential_type_id',
            '{{%credential_type}}',
            'id',
            'CASCADE'
        );

        $this->batchInsert('{{%credential_type}}',
            ['id', 'icon_class', 'credential_type_name', 'assigned_number_label', 'updated_by', 'updated_at', 'status', 'ordering'],
            [
                [1, 'fa fa-id-card', 'ID Card or Driver License', 'ID Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 1],
                [2, 'fa fa-id-card', 'SSN', 'SS Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 2],
                [3, 'fa fa-address-card-o', 'PT License', 'License Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 3],
                [4, 'fa fa-address-card-o', 'PTA License', 'License Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 4],
                [5, 'fa fa-id-card-o', 'Practice Insurance', 'Policy Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 5],
                [6, 'fa fa-id-card-o', 'Physical Examination', 'Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 6],
                [7, 'fa fa-address-card-o', 'Flu Vaccination', 'Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 7],
                [8, 'fa fa-thermometer-half', 'TB Test', 'Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 8],
                [9, 'fa fa-handshake-o', 'W9 Document', 'Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 9],
                [10, 'fa fa-id-card', 'Resume', 'Resume Number', 1, 'now()', ConstHelper::STATUS_ACTIVE, 10],
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-user_credential-user_id-user-id', '{{%user_credential}}');
        $this->dropForeignKey('fk-user_credential-credential_type_id-credential_type-id', '{{%user_credential}}');

        $this->dropIndex('idx-user_credential-user_id', '{{%user_credential}}');
        $this->dropIndex('idx-user_credential-credential_type_id', '{{%user_credential}}');
        $this->dropIndex('idx-user_credential-status', '{{%user_credential}}');

        $this->dropIndex('idx-credential_type-status', '{{%credential_type}}');

        $this->dropTable('{{%user_credential}}');
        $this->dropTable('{{%credential_type}}');
    }
}
