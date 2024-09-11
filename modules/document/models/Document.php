<?php

namespace app\modules\document\models;

use Yii;

class Document extends \yii\db\ActiveRecord
{
    public $time;
    public static function tableName()
    {
        return 'pr_tracking';
    }

    public static function getDb()
    {
        return Yii::$app->get('document');
    }

    public function rules()
    {
        return [
            [['date_time_received', 'date_time_released', 'pr_id', 'type_id', 'status', 'received_by', 'released_by', 'time'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pr_id' => 'PR ID',
            'type_id' => 'Type',
            'date_time_received' => 'Date and Time Received',
            'date_time_released' => 'Date and Time Released',
            'status' => 'Status',
        ];
    }

    public function getPurchaseRequestNumber()
    {
        return $this->hasOne(PurchaseRequest::class, ['id' => 'pr_id']);
    }
}
