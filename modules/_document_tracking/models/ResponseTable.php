<?php

namespace app\modules\document_tracking\models;

use Yii;
use app\models\profile\Profile;

class ResponseTable extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'response_table';
    }

    public static function getDb()
    {
        return Yii::$app->get('itdidb_dt');
    }

    public function rules()
    {
        return [
            [['tracking_number', 'user_id', 'recipient_id', 'release_id', 'status', 'view'], 'safe'],
        ];
    }

    public function getDocumentDetails()
    {
        return $this->hasOne(DocumentDetails::class, ['tracking_number' => 'tracking_number']);
    }

    public function getStatusDescription()
    {
        $text = '';
        switch ($this->status) {
            case 0:
                $text = 'Pending';
                break;
            
            case 1:
                $text = 'Received';
                break;
            
            case 2:
                $text = 'Redirected';
                break;
        }
        return $text;
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'recipient_id']);
    }
}
