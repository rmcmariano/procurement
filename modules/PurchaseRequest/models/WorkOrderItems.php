<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "work_order_items".
 *
 * @property int $id
 * @property int $wo_id
 * @property int $item_id
 */
class WorkOrderItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'work_order_items';
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
            [['wo_id', 'item_id', 'deduction_id', 'deduction_amount'], 'safe'],
            [['wo_id', 'item_id', 'deduction_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wo_id' => 'Po ID',
            'item_id' => 'Bid ID',
            'deduction_id' => 'Deduction ID',
            'deduction_amount' => 'Deduction Amount'
        ];
    }
    
    public function getBiddingdisplay()
    {
        return $this->hasMany(BiddingList::className(), ['id' => 'item_id']);
    } 

    public function getWorkorder()
    {
        return $this->hasOne(WorkOrder::className(), ['id' => 'wo_id']);
    } 
}
