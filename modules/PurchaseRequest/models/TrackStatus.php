<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

class TrackStatus extends \yii\db\ActiveRecord
{
    const PPMS_STATUS = ['16','17', '20', '21', '23', '24', '25', '26', '27', '28', '29', '34', '35', '44', '46', '52'];
    const PPMS_VIEW = ['17', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '32', '18', '34', '35', '44', '46', '52'];
    const BAC_STATUS = ['4' , '9' , '10' , '11' , '12', '13', '14', '15', '16', '36', '37', '38', '39', '40', '41', '44', '48', '49', '54', '47', '57', '60', '61'];
    const BUDGET_STATUS = ['2', '32', '33', '36', '37', '21', '22', '24'];
    const BUDGET_VIEW = ['2', '32', '33', '17', '22', '23', '16', '18'];
    const ACCOUNTING_STATUS = ['4', '22', '5'];
    const ACCOUNTING_VIEW = ['4', '22', '5' ];
    

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%track_status}}'; 
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('itdidb_procurement_system');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status'], 'safe'],
            [['status'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
   
    public function getPurchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::className(), ['status' => 'id']);
    }

    public function getHistorydisplay()
    {
        return $this->hasMany(HistoryLog::className(), ['action_status' => 'id']);
    } 

    public function getBidding()
    {
        return $this->hasMany(BiddingList::className(), ['status' => 'id']);
    }

    public function getDescription()
    {
        return $this->hasMany(PrItems::className(), ['status' => 'id']);
    }

    public function getItemhistorydisplay()
    {
        return $this->hasMany(ItemHistoryLogs::className(), ['action_status' => 'id']);
    }
}
    