<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "purchase_order".
 *
 * @property int $id
 * @property int $po_no
 * @property int $pr_id
 * @property int $bid_id
 * @property string $place_delivery
 * @property string $date_delivery
 * @property int $ors_burs_num
 * @property string $po_date_created
 * @property int $created_by
 * @property string $time_stamp
 */
class PurchaseOrder extends \yii\db\ActiveRecord
{
    public $file;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchase_order';
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
            [['po_no', 'pr_id', 'place_delivery', 'date_delivery', 'ors_burs_num', 'created_by', 'po_status', 'conforme_status', 'project_type_series_id' , 'item_type_series_id', 'dv_num'], 'safe'],
            [['po_no', 'pr_id', 'ors_burs_num', 'created_by', 'dv_num', 'iar_id', 'iar_date_created'], 'safe'],
            [['date_delivery', 'po_date_created','date_conforme', 'payment_term','time_stamp', 'actual_date_delivery'], 'safe'],
            // [['place_delivery'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'po_no' => 'Po ID',
            'pr_id' => 'Pr ID',
            'place_delivery' => 'Place Delivery',
            'date_delivery' => 'Date Delivery',
            'ors_burs_num' => 'Ors Burs Num',
            'po_date_created' => 'Date Created',
            'created_by' => 'Created By',
            'actual_date_delivery' => 'Actual Date Delivery',
            'time_stamp' => 'Time Stamp',
            'payment_term' => 'Payment Term',
            'po_status' => 'Status',
            'date_conforme' => 'Date of Conforme',
            'conforme_status' => 'Conforme Status',
            'dv_num' => 'DV Num',
            'iar_id' => 'IAR ID',
            'iar_date_created' => 'IAR Date Created'
        ];
    }

    public function getPrItemsdisplay()
    {
        return $this->hasOne(PrItems::className(), ['pr_id' => 'id']);
    }

    public function getBidding()
    {
        return $this->hasMany(BiddingList::className(), ['id' => 'bid_id']);  
    }

    public function getPurchasedisplay()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    } 

    public function getOrderItemdisplay()
    {
        return $this->hasMany(PurchaseOrderItems::className(), ['po_id' => 'id']);
    } 

    public function getDelivery(){
        return $this->hasMany(Delivery::className(), ['po_id' => 'id']);
    }

    public function getPurchaseorder()
    {
        return $this->hasMany(ConformeAttachments::className(), ['po_id' => 'id']);
    }

    public function getSupplierdisplay()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']);
    }

    public function getIardisplay()
    {
        return $this->hasOne(InspectionAcceptanceReport::className(), ['id' => 'iar_id']);
    }

   


}
