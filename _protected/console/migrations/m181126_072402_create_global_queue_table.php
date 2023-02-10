<?php

use yii\db\Migration;

/**
 * Class m181126_072402_create_global_queue_table
 */
class m181126_072402_create_global_queue_table extends Migration
{
    public $tableName = '{{%global_queue}}';

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

        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'channel' => $this->string()->notNull(),
            'job' => $this->binary()->notNull(),
            'pushed_at' => $this->integer()->notNull(),
            'ttr' => $this->integer()->notNull(),
            'delay' => $this->integer()->notNull()->defaultValue(0),
            'priority' => $this->integer()->notNull()->unsigned()->defaultValue(1024),
            'reserved_at' => $this->integer(),
            'attempt' => $this->integer(),
            'done_at' => $this->integer(),
            ], $tableOptions
        );

        $this->createIndex('global_queue_channel_index', $this->tableName, 'channel');
        $this->createIndex('global_queue_reserved_at_index', $this->tableName, 'reserved_at');
        $this->createIndex('global_queue_priority_index', $this->tableName, 'priority');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('global_queue_channel_index', $this->tableName);
        $this->dropIndex('global_queue_reserved_at_index', $this->tableName);
        $this->dropIndex('global_queue_priority_index', $this->tableName);

        // Drop table
        $this->dropTable($this->tableName);
    }
}
