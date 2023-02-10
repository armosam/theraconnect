<?php

use yii\base\InvalidConfigException;
use yii\db\Migration;
use yii\log\DbTarget;

/**
 * Class m141022_115923_create_log_and_log_archive_tables
 */
class m141022_115923_create_log_and_log_archive_tables extends Migration
{
    /**
     * @var DbTarget[] Targets to create log table for
     */
    private $dbTargets = [];

    /**
     * @throws InvalidConfigException
     * @return DbTarget[]
     */
    protected function getDbTargets()
    {
        if ($this->dbTargets === []) {
            $log = Yii::$app->getLog();

            $usedTargets = [];
            foreach ($log->targets as $target) {
                if ($target instanceof DbTarget) {
                    $currentTarget = [
                        $target->db,
                        $target->logTable,
                    ];
                    if (!in_array($currentTarget, $usedTargets, true)) {
                        // do not create same table twice
                        $usedTargets[] = $currentTarget;
                        $this->dbTargets[] = $target;
                    }
                }
            }

            if ($this->dbTargets === []) {
                throw new InvalidConfigException('You should configure "log" component to use one or more database targets before executing this migration.');
            }
        }

        return $this->dbTargets;
    }

    /**
     * @return bool|void
     * @throws InvalidConfigException
     */
    public function up()
    {
        $columns = [
            'id' => $this->bigPrimaryKey(),
            'level' => $this->integer(),
            'category' => $this->string(),
            'log_time' => $this->double(),
            'prefix' => $this->text(),
            'message' => $this->text(),
        ];

        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $tableOptions = null;
            if ($this->db->driverName === 'mysql') {
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
            }

            $this->createTable($target->logTable, $columns, $tableOptions);
            $this->createIndex('idx_log_level', $target->logTable, 'level');
            $this->createIndex('idx_log_category', $target->logTable, 'category');
        }

        $this->createTable('{{%log_archive}}', $columns);
        $this->createIndex('idx_log_archive_level', '{{%log_archive}}', 'level');
        $this->createIndex('idx_log_archive_category', '{{%log_archive}}', 'category');
    }

    /**
     * @return bool|void
     * @throws InvalidConfigException
     */
    public function down()
    {
        foreach ($this->getDbTargets() as $target) {
            $this->db = $target->db;

            $this->dropIndex('idx_log_level', $target->logTable);
            $this->dropIndex('idx_log_category', $target->logTable);
            $this->dropTable($target->logTable);
        }
        $this->dropIndex('idx_log_archive_level', '{{%log_archive}}');
        $this->dropIndex('idx_log_archive_category', '{{%log_archive}}');
        $this->dropTable('{{%log_archive}}');

    }
}