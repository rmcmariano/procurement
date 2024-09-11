<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "request_table".
 *
 * @property int $id
 * @property int $bid_id
 * @property int $request_changes
 * @property int $request_status
 * @property string $time_stamp
 */
class RequestModel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_table';
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
            [['bid_id', 'item_specs_id', 'request_changes', 'request_status', 'request_approved_by', 'requested_by'], 'safe'],
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
            'bid_id' => 'BID ID',
            'request_changes' => 'Request Changes',
            'request_status' => 'Request Status',
            'time_stamp' => 'timestamp',
            'request_approved_by' => 'Approved By',
            'requested_by' => 'Requested By',
            'item_specs_id' => 'Item Specs ID'
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(ItemSpecification::className(), ['id' => 'item_specs_id']);
    }



}
