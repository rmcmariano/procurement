<?php

namespace app\modules\document_tracking\models;

use Yii;

class FileUpload extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'file_uploads';
    }

    public static function getDb()
    {
        return Yii::$app->get('itdidb_dt');
    }

    public function rules()
    {
        return [
            [['release_id', 'attachment', 'attachment_path'], 'safe'],
            [['attachment'], 'file', 'maxFiles' => 10],
        ];
    }
}
