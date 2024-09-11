<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\rbac\Item;

/**
 * This is the model class for table "bidding_list".
 *
 * @property int $id
 * @property int $item_id
 * @property int $supplier_id
 * @property int $supplier_price
 */
class BiddingList extends \yii\db\ActiveRecord
{
    public $yourCheckboxAttribute;
    
    public static function tableName()
    {
        return 'bidding_list';
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
            [['item_id', 'supplier_id', 'supplier_price', 'pr_id', 'po_id', 'assign_twg', 'item_remarks', 'supplier_payment_term', 'bid_bond', 'resolution_no', 'resolution_date', 'yourCheckboxAttribute'], 'safe'],
            [['supplier_price',], 'double']
            // [['item_id', 'supplier_id', 'supplier_price'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Description ID',
            'pr_id' => 'PR ID',
            'supplier_id' => 'Supplier Name',
            'supplier_price' => 'Supplier Price',
            'supplier_payment_term' => 'Payment Term',
            'item_remarks' => 'Item Remarks',
            'assign_twg' => 'Assigned TWG',
            'bid_bond' => 'BID Bond',
            'resolution_no' => 'Resolution No.',
            'resolution_date' => 'Date of Resolution',
        ];
    }


    public function getSupplierdisplay()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getPrItemsdisplay()
    {
        return $this->hasOne(PrItems::className(), ['id' => 'item_id']);
    }

    public function getPurchaserequest()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    }

    public function getStatusdisplay()
    {
        return $this->hasOne(TrackStatus::className(), ['id' => 'status']);
    }

    public function getUserdisplay()
    {
        return $this->hasOne(User::className(), ['id' => 'assign_twg']);
    }

    public function getQuotationdisplay()
    {
        return $this->hasOne(Quotation::className(), ['pr_id' => 'id'])->via('purchaserequest');
    }

    public function getSignatoriesdisplay()
    {
        return $this->hasMany(BacSignatories::className(), ['bid_id' => 'id']);
    }

    public function getPurchaseorderdisplay()
    {
        return $this->hasOne(PurchaseOrderItems::className(), ['bid_id' => 'id'])->via('PrItemsdisplay');
    }

    public function getWorkorderdisplay()
    {
        return $this->hasOne(WorkOrderItems::className(), ['item_id' => 'id'])->via('PrItemsdisplay');
    }

    public static function getTotalprice($model, $fieldName)
    {
        $total = 0;
        $totalAmount = 0;

        foreach ($model as $item) {

            $test = BiddingList::find()->where(['item_id' => $item['id']])->andWhere(['status' => ['17',  '19', '20', '21']])->asArray()->all();
            $total += $test['total_cost'];

            if (in_array($item->status, ['17', '19', '20', '21'])) {
                $testPrice = $item['quantity'] * $test['supplier_price'];
                $totalAmount += $testPrice;
                return $totalAmount;
            }
        }
        return ($total);
    }

    public static function getTotalbudget($model, $fieldName)
    {
        $total = 0;
        $totalAmount = 0;
        $superTotal = 0;

        foreach ($model as $item) {
            $test = BiddingList::find()->where(['item_id' => $item['id']])->one();
            $total += $item['total_cost'];


            if (in_array($item->status, ['40', '41', '35'])) {
                $testPrice = $item['quantity'] * $test['supplier_price'];
                $totalAmount += $testPrice;
            }
            $superTotal = $total + $totalAmount;
        }

        return ($superTotal);
    }

    public static function getItemremarksDisplay ($id)
    {
        $bidding = self::find()->one();
        $item = PrItems::find()->where(['id' => $bidding->item_id])->one();
        
        if ($bidding->item_remarks == $item->id) {
            $item->item_name;
        }
        return $bidding->item_remarks;
    }

    public function getTotalCostDecimal()
    {
        $whole = (int) $this->supplier_price;

        $decimal = $this->supplier_price - $whole;

        return Yii::$app->formatter->asDecimal($decimal, 2) * 100;
    }
 

   
}
