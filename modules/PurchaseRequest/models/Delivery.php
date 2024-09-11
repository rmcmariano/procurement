<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "delivery".
 *
 * @property int $id
 * @property int $po_id
 * @property string $actual_date_delivery
 * @property int $type_delivery
 * @property string $remarks
 * @property string $time_stamp
 */
class Delivery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'delivery';
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
            [['po_id', 'actual_date_delivery', 'type_delivery', 'remarks', 'type_delivery', 'delivery_receipt_no', 'delivery_amount'], 'safe'],
            [['po_id'], 'integer'],
            [['actual_date_delivery', 'time_stamp'], 'safe'],
            [['remarks'], 'string', 'max' => 200],
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
            'actual_date_delivery' => 'Actual Date Delivery',
            'type_delivery' => 'Type Delivery',
            'remarks' => 'Remarks',
            'time_stamp' => 'Time Stamp',
            'delivery_receipt_no' => 'Delivery Receipt Number',
            'delivery_amount' => 'Delivery Amount'
        ];
    }
}
