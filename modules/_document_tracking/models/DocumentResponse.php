<?php

namespace app\modules\document_tracking\models;

use Yii;

class DocumentResponse extends \yii\db\ActiveRecord
{

    public $section;
    public $individual;
    public $attachment;

    public static function tableName()
    {
        return 'document_response';
    }

    public static function getDb()
    {
        return Yii::$app->get('itdidb_dt');
    }

    public function rules()
    {
        return [
            [['id', 'tracking_number', 'action_by', 'section', 'individual', 'attachment', 'date', 'action', 'remarks', 'view'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section' => 'Section',
            'individual' => 'Individual',
            'attachment' => 'Attachment/s',
        ];
    }

    public function getDocumentDetails()
    {
        return $this->hasOne(DocumentDetails::class, ['tracking_number' => 'tracking_number']);
    }
}
