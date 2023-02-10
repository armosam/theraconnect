<?php

namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use Throwable;
use DateTime;
use Exception;

/**
 * Class LogArchiveComponent Archives all logs from configuration
 * older than given setting in the `preventDeletingLastDays` parameter
 *
 * Usage:
 * Add this configuration to the main config
 *
 * 'logArchive' => [
 *      'class' => 'common\components\LogArchiveComponent',
 *      'config' => [
 *          'common\models\Log' => [
 *              'archiveModel' => 'common\models\LogArchive',
 *              'logTimeAttribute' => 'log_time',
 *              'allowedToDelete' => false,
 *              'preventDeletingLastDays' => 5,
 *          ]
 *      ]
 * ],
 *
 * Then in console utility use command
 * Yii::$app->logArchive->archiveAll();
 *
 */
class LogArchiveComponent extends Component
{
    const LOG_TIME_ATTR_KEY = 'logTimeAttribute';
    const ARCHIVE_MODEL_KEY = 'archiveModel';
    const ALLOWED_TO_DELETE = 'allowedToDelete';
    const PREVENT_DELETING_LAST_DAYS = 'preventDeletingLastDays';

    public $config = [];

    /**
     * Returns configuration for given model and config key
     * @param string $modelClass
     * @param string $key
     * @return mixed
     * @throws InvalidConfigException
     */
    private function getValueAfterCheck(string $modelClass, string $key)
    {
        if (!isset($this->config[$modelClass][$key])) {
            throw new InvalidConfigException("'$modelClass' must contain " . $key);
        }
        return $this->config[$modelClass][$key];
    }

    /**
     * Gets log time attribute for model.
     * @param string $modelClass Model to find attribute.
     * @return string
     * @throws InvalidConfigException
     */
    public function getTimeAttribute(string $modelClass): string
    {
        $logTimeAttribute = $this->getValueAfterCheck($modelClass, static::LOG_TIME_ATTR_KEY);

        return $logTimeAttribute;
    }

    /**
     * Gets archive model for given log model from config.
     * @param string $modelClass Model to find archive model.
     * @return string
     * @throws InvalidConfigException
     */
    public function getArchiveModel(string $modelClass): string
    {
        $archiveModelName = $this->getValueAfterCheck($modelClass, static::ARCHIVE_MODEL_KEY);

        return $archiveModelName;
    }

    /**
     * Gets number of days to prevent deletion of records.
     * @param string $modelClass Model to find prevent to delete days.
     * @return string
     * @throws InvalidConfigException
     */
    public function getPreventDeletingLastDays(string $modelClass): string
    {
        $preventDeletingLastDays = $this->getValueAfterCheck($modelClass, static::PREVENT_DELETING_LAST_DAYS);

        return $preventDeletingLastDays;
    }

    /**
     * Checks whether model allowed for delete.
     * @param string $modelClass Model class name to find attribute.
     * @return bool Returns `true` if `$modelClass` is allowed to delete.
     * @throws InvalidConfigException
     */
    public function isAllowedToDelete(string $modelClass): bool
    {
        $allowedToDelete = $this->getValueAfterCheck($modelClass, static::ALLOWED_TO_DELETE);

        return $allowedToDelete;
    }

    /**
     * Gets prevent deleting last days value in unix timestamp.
     *
     * @param string $modelClass
     * @return int
     * @throws Exception
     */
    public function getPreventDeletingLastDaysTimeStamp(string $modelClass): int
    {
        $preventDeletingLastDays = $this->getPreventDeletingLastDays($modelClass);
        $modify = "- $preventDeletingLastDays days";

        $preventDeletingLastDaysDate = new DateTime();
        $preventDeletingLastDaysDate->modify($modify);
        $preventDeletingLastDaysTimeStamp = $preventDeletingLastDaysDate->getTimestamp();

        return $preventDeletingLastDaysTimeStamp;
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();

        if (empty($this->config)) {
            throw new InvalidConfigException('Add the component config to the main config file.');
        }
    }

    /**
     * Archives all logs given in the configuration to be archived
     * It will archive older than given in config days
     *
     * @throws InvalidConfigException
     * @return string
     */
    public function archiveAll(): string
    {
        $result = [];
        $start = time();

        foreach ($this->config as $logModel => $settings) {
            $result[] = $this->archiveLog($logModel, $this->getArchiveModel($logModel));
        }

        $end = time();

        $done = $end - $start;

        Yii::info("Processed in $done seconds. ".join(',', $result), __CLASS__);
        return "Processed in $done seconds. ". PHP_EOL .join(',', $result);
    }

    /**
     * Archives specific log
     *
     * @param string|ActiveRecord $logModel
     * @param string|ActiveRecord $archiveModel
     *
     * @return string
     * @throws InvalidConfigException
     */
    private function archiveLog(string $logModel, string $archiveModel): string
    {
        $logTable = $logModel::tableName();
        $logArchiveTable = $archiveModel::tableName();

        $timeAttribute = $this->getTimeAttribute($logModel);
        $filterTimeValue = $this->getPreventDeletingLastDaysTimeStamp($logModel);

        $where = "WHERE $logTable.$timeAttribute < $filterTimeValue";
        $insertSql = "INSERT INTO $logArchiveTable SELECT * FROM $logTable $where";
        $deleteSql = "DELETE FROM $logTable $where";

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $insertedLogs = Yii::$app->db->createCommand($insertSql)->execute();
            $deletedLogs = 0;
            if($this->isAllowedToDelete($logModel)){
                $deletedLogs = Yii::$app->db->createCommand($deleteSql)->execute();
            }

            $transaction->commit();
            Yii::info("Successfully Archived $insertedLogs logs and removed $deletedLogs logs", __CLASS__);
            $result = "Successfully Archived $insertedLogs logs and removed $deletedLogs logs".PHP_EOL;
        } catch (Throwable $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage(), __CLASS__);
            $result = "Failed to Archive '$logTable' logs. ".$e->getMessage().PHP_EOL;
        }
        return $result;
    }

}
