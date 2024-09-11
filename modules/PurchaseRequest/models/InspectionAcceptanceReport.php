<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "inspection_acceptance_report".
 *
 * @property int $id
 * @property int $po_id
 * @property int $iar_number
 * @property int $iar_date
 * @property string $iar_item_changes
 * @property string $sales_invoice_number
 * @property int $sales_invoice_date
 * @property string $inspection_id
 * @property int $inspection_date
 * @property string $time_stamp
 */
class InspectionAcceptanceReport extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inspection_acceptance_report';
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
            [['po_id', 'pr_id', 'iar_number', 'iar_date', 'iar_request_id', 'sales_invoice_number', 'sales_invoice_date', 'inspector_id', 'inspection_date', 'request_id', 'iar_status'], 'safe'],
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
            'pr_id' => 'PR ID',
            'po_id' => 'Po ID',
            'iar_number' => 'IAR Number',
            'iar_date' => 'IAR Date',
            'iar_request_id' => 'IAR Item Changes',
            'sales_invoice_number' => 'SI Number',
            'sales_invoice_date' => 'SI Date',
            'inspector_id' => 'Inspector',
            'inspection_date' => 'Inspection Date',
            'time_stamp' => 'Time Stamp',
            'request_id' => 'Request ID'
        ];
    }

    public function getPurchaseorderDisplay()
    {
        return $this->hasOne(PurchaseOrder::className(), ['iar_id' => 'id']);
    }
}
