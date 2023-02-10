<?php

use yii\db\Migration;
use common\models\User;
use common\helpers\ConstHelper;

/**
 * Class m160326_072216_create_service_table
 * Handles the creation of table `service`.
 */
class m160326_072216_create_service_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') 
        {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Service table
        $this->createTable('{{%service}}', [
            'id' => $this->primaryKey(),
            'service_name' => $this->string(255),
            'created_by' => $this->integer()->notNull()->defaultValue(User::USER_SYSTEM_ADMIN_ID),
            'created_at' => 'TIMESTAMPTZ(0) NOT NULL DEFAULT CURRENT_TIMESTAMP',
            'updated_by' => $this->integer(),
            'updated_at' => 'TIMESTAMPTZ(0)',
            'status' => $this->string(1)->notNull()->defaultValue(ConstHelper::STATUS_ACTIVE),
            'ordering' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('idx-service-status', '{{%service}}', 'status');

        $this->batchInsert('{{%service}}',
            ['id', 'service_name', 'updated_by', 'updated_at', 'status', 'ordering'],
            [
                [1, 'Physical Therapy', 1, 'now()', ConstHelper::STATUS_ACTIVE, 1],
                [2, 'Occupational Therapy', 1, 'now()', ConstHelper::STATUS_ACTIVE, 2],
                [3, 'Speech Therapy', 1, 'now()', ConstHelper::STATUS_ACTIVE, 3]
            ]
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx-service-status', '{{%service}}');
        $this->dropTable('{{%service}}');
    }

}
