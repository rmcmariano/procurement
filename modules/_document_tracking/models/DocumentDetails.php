<?php

namespace app\modules\document_tracking\models;

use Yii;

class DocumentDetails extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'document_details';
    }

    public static function getDb()
    {
        return Yii::$app->get('itdidb_dt');
    }

    public function rules()
    {
        return [
            [['created_by', 'tracking_number', 'date_created', 'is_closed'], 'safe'],
            [['title', 'details', 'document_type'], 'required'],
            ];
    }

    public function attributeLabels()
    {
        return [
            'document_type' => 'Document Type',
            'title' => 'Title',
            'details' => 'Details',
        ];
    }
}
