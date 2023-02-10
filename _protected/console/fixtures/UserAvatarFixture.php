<?php

namespace console\fixtures;

use Yii;
use PDO;
use yii\db\Expression;
use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * UserAvatar fixture.
 */
class UserAvatarFixture extends PostgresActiveFixture
{
    public $tableName = 'user_avatar';
    public $modelClass = 'common\models\UserAvatar';
    public $dataFile = '@console/fixtures/data/user_avatar.php';
    public $depends = [
        'console\fixtures\UserFixture',
    ];

    /**
     * This is a customized loader of fixtures that gets file path loads file and saves as BLOB
     *
     * It populate the table with the data returned by [[getData()]].
     *
     * If you override this method, you should consider calling the parent implementation
     * so that the data returned by [[getData()]] can be populated into the table.
     */
    public function load()
    {
        parent::load();
        foreach ($this->data as $alias => $row)
        {
            if (is_file(Yii::getAlias($row['file_content'])) && is_readable(Yii::getAlias($row['file_content']))) {
                $file_content = file_get_contents(Yii::getAlias($row['file_content']));
                $sql = new Expression("UPDATE {{%user_avatar}} SET file_content = :file WHERE id = :id;");
                $q = $this->db->createCommand($sql);
                $q->bindParam(':id', $row['id']);
                $q->bindParam(':file', $file_content, PDO::PARAM_LOB);
                $q->execute();
            }
        }
    }
}
