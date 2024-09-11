<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "item_specification".
 *
 * @property int $id
 * @property int $item_id
 * @property string $description
 * @property int $evalution_status
 * @property string $bidbulletin
 * @property int $bidbulletin_status
 */
class ItemSpecification extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'item_specification';
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
            [['item_id', 'description', 'quantity', 'property_no', 'evaluation_status', 'bidbulletin_changes', 'request_changes', 'bidbulletin_id', 'bidbulletin_status', 'bidbulletin_remarks', 'time_stamp'], 'safe'],
            [['description', 'bidbulletin_changes', 'bidbulletin_remarks'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Item ID',
            'description' => 'Description',
            'quantity' => 'Quantity',
            'property_no' => 'Property #',
            'evaluation_status' => 'Status',
            'bidbulletin_id' => 'Bid Bulletin ID',
            'bidbulletin_changes' => 'Bid Bulletin',
            'bidbulletin_status' => 'Bid Bulletin status',
            'bidbulletin_remarks' => 'Bid Bulletin Remarks',
            'request_changes' => 'Request Changes'
        ];
    }

    public function getItem()
    {
        return $this->hasOne(PrItems::className(), ['id' => 'item_id']);
    }

    public function getRequest()
    {
        return $this->hasMany(RequestModel::className(), ['item_specs_id' => 'id']);
    }
}
