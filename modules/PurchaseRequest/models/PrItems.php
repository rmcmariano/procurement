<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%pr_description}}".
 *
 * @property int $id
 * @property int $pr_id
 * @property int $stock
 * @property string $unit
 * @property string $item_name
 * @property int $quantity
 * @property int $unit_cost
 * @property int $total_cost
 * @property string $time_stamp
 * @property int $archived
 */
class PrItems extends \yii\db\ActiveRecord
{

    public $selectedKeys;
    public $file;
    public $item_specs;
    
    public static function tableName()
    {
        return '{{%pr_items}}';
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
            [['pr_id', 'stock', 'unit', 'item_name', 'quantity', 'unit_cost', 'total_cost', 'status', 'bidbulletin_id'], 'safe'],
            [['pr_id', 'stock', 'quantity',  'archived', 'bid_title'], 'safe'],
            [['unit_cost', 'total_cost', 'bidding_docs_fee',], 'safe'],
            [['item_name'], 'string'],
            [['time_stamp', 'selectedKeys'], 'safe'],
            [['unit'], 'string', 'max' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [

            'id' => 'ID',
            'pr_id' => 'Pr ID',
            'stock' => 'Stock',
            'unit' => 'Unit',
            'item_name' => 'Item Description',
            'quantity' => 'Quantity',
            'unit_cost' => 'Unit Cost',
            'total_cost' => 'Total Cost',
            'time_stamp' => 'Time Stamp',
            'archived' => 'Archived',
            'status' => 'Status',
            'bid_title' => '',
            'bidding_docs_fee' => 'Bidding Documents Fee',
            'bidbulletin_id' => 'Bid Bulletin No.',
        ];
    }

    public static function getTotal($provider, $fieldName)
    {
        $total = 0;

        foreach ($provider as $item) {
            $total += $item['total_cost'];
        }
        return ($total);
    }

    public function getPurchase()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    }

    public function getBiddinglistdisplay()
    {
        return $this->hasMany(BiddingList::className(), ['item_id' => 'id']);
    }

    public function getBiddinglistdisplaying()
    {
        return $this->hasOne(BiddingList::className(), ['item_id' => 'id']);
    }

    public function getOrderdisplay()
    {
        return $this->hasMany(PurchaseOrder::className(), ['pr_id' => 'id']);
    }

    public function getBidfield()
    {
        return $this->biddinglist->supplier_price;
    }

    public function getQuotation()
    {
        return $this->hasOne(Quotation::className(), ['pr_id' => 'pr_id']);
    }

    public function getStatusdisplay()
    {
        return $this->hasOne(TrackStatus::className(), ['id' => 'status']);
    }

    public function getItemhistory()
    {
        return $this->hasOne(ItemHistoryLogs::className(), ['item_id' => 'id']);
    }

    public function getItemexplode()
    {
        $itemName = explode(PHP_EOL, $this->item_name);
        return $itemName[0];
    }

    public function getItemdropdown()
    {
        $items = PrItems::find()->where(['pr_id' => $this->id])->andWhere(['status' => ['10', '11', '12', '13', '14', '15']])->one();
        $itemName = explode(PHP_EOL, $this->item_name);
        return $itemName[0];
    }

    public function getKeyWord()
    {
        $item = self::find()->one();
        $items = PrItems::find()->where(['pr_id' => $item->id])->andWhere(['status' => ['10', '11', '12', '13', '14', '15']])->one();
        $itemName = explode(PHP_EOL, $items->item_name);
        $arr = explode(' ', trim($items->item_name));

        return self::find()->select([$arr[0], 'id'])->indexBy('id')->column();
    }

    public function getItemspecification()
    {
        return $this->hasMany(ItemSpecification::className(), ['item_id' => 'id']);
    }

    public function getTotalCostDecimal()
    {
        $whole = (int) $this->total_cost;

        $decimal = $this->total_cost - $whole;

        return Yii::$app->formatter->asDecimal($decimal, 2) * 100;
    }

    public function getBidbulletin()
    {
        return $this->hasOne(BidBulletin::className(), ['id' => 'bidbulletin_id']);
    }

    
}
