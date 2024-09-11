<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

class InventoryCustodianSlip extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inventory_custodian_slip';
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
            [['item_id', 'bid_id', 'iar_id', 'ics_no', 'inventory_item_no', 'estimated_useful_life', 'date_acquire', 'received_from', 'received_by', 'created_by', 'ics_status'], 'safe'],
            [['time_stamp'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'item ID',
            'bid_id' => 'Bid ID',
            'iar_id' => 'IAR Id',
            'ics_no' => 'ICS No.',
            'inventory_item_no' => 'Inventory Item No.',
            'estimated_useful_life' => 'Estimated Useful Life',
            'date_acquire' => 'Date Acquire',
            'received_from' => 'Received From',
            'received_by' => 'Received By',
            'created_by' => 'Created By',
            'time_stamp' => 'Timestamp',
            'ics_status' => 'Status'
        ];
    }

 
}
