<?php

namespace console\fixtures;

use Yii;
use common\models\UserCredential;
use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * UserCredential fixture.
 */
class UserCredentialFixture extends PostgresActiveFixture
{
    public $tableName = 'user_credential';
    public $modelClass = 'common\models\UserCredential';
    public $dataFile = '@console/fixtures/data/user_credential.php';
    public $depends = [
        'console\fixtures\UserFixture',
        'console\fixtures\CredentialTypeFixture'
    ];

    /**
     * This is a customized loader of fixtures that gets file path from data file,
     * generates our file system path
     * detects how to store files (database or file system)
     * stores the file in the new file path if not in the database and keeps a new path in the database
     *
     * It populate also the table with the data returned by [[getData()]].
     *
     * If you override this method, you should consider calling the parent implementation
     * so that the data returned by [[getData()]] can be populated into the table.
     */
    public function load()
    {
        parent::load();
        foreach ($this->data as $alias => $row)
        {
            if (!empty($row['id']) && is_file(Yii::getAlias($row['file_content_uri'])) && is_readable(Yii::getAlias($row['file_content_uri']))) {
                $model = UserCredential::findOne($row['id']);
                $file_content_uri = $model->getFileContentURI('credentialFile');

                $file = null;
                if(empty(Yii::$app->params['credentialFile']['store_in_database'])){
                    //mkdir(dirname(Yii::getAlias($file_content_uri)), 0775, true);
                    copy(Yii::getAlias($row['file_content_uri']), Yii::getAlias($file_content_uri));
                } else {
                    $file = file_get_contents(Yii::getAlias($row['file_content_uri']));
                }
                $model->setAttribute('file_name', basename(Yii::getAlias($row['file_content_uri'])));
                $model->setAttribute('file_size', filesize(Yii::getAlias($row['file_content_uri'])));
                $model->setAttribute('mime_type', mime_content_type(Yii::getAlias($row['file_content_uri'])));
                $model->setAttribute('file_content_uri', $file_content_uri);
                $model->setAttribute('file_content', $file);
                $model->save();
            }
        }
    }
}
