<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "purchase_order_items".
 *
 * @property int $id
 * @property int $po_id
 * @property int $bid_id
 */
class PurchaseOrderItems extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_order_items';
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
            [['po_id', 'bid_id', 'deduction_id', 'deduction_amount'], 'safe'],
            [['po_id', 'bid_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'po_id' => 'Po ID',
            'bid_id' => 'Bid ID',
            'deduction_id' => 'Deduction ID',
            'deduction_amount' => 'Deduction Amount'
        ];
    }
    
    public function getBiddingdisplay()
    {
        return $this->hasMany(BiddingList::className(), ['id' => 'bid_id']);
    } 

    public function getPurchaseorder()
    {
        return $this->hasOne(PurchaseOrder::className(), ['id' => 'po_id']);
    } 

    public function getDeductionList()
    {
        return $this->hasOne(DeductionLists::className(), ['id' => 'deduction_id']);
    } 
}
