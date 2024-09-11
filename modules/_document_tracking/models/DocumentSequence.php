<?php

namespace app\modules\document_tracking\models;

use Yii;

class DocumentSequence extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'document_sequence';
    }

    public static function getDb()
    {
        return Yii::$app->get('itdidb_dt');
    }

    public function rules()
    {
        return [
            [['document_type', 'sequence'], 'safe'],
        ];
    }
}
