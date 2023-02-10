<?php

namespace console\fixtures;

use Yii;
use common\models\OrderDocument;
use tests\codeception\common\_support\yii\PostgresActiveFixture;

/**
 * OrderDocument fixture.
 */
class OrderDocumentFixture extends PostgresActiveFixture
{
    public $tableName = 'order_document';
    public $modelClass = 'common\models\OrderDocument';
    public $dataFile = '@console/fixtures/data/order_document.php';
    public $depends = [
        'console\fixtures\OrderFixture'
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
                $model = OrderDocument::findOne($row['id']);
                $file_content_uri = $model->getFileContentURI('orderDocumentFile');

                $file = null;
                if(empty(Yii::$app->params['orderDocumentFile']['store_in_database'])){
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
