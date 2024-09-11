<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "less_deductions".
 *
 * @property int $id
 * @property int $po_id
 * @property int $deduction_id
 * @property float $deduction_amount
 */
class AdditionalServices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'additional_services';
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
            [[ 'add_service_id', 'additional_amount'], 'safe'],
            [['po_id', 'add_service_id'], 'integer'],
            [['additional_amount'], 'number'],
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
            'add_service_id' => 'Additional Services ID',
            'additional_amount' => 'Additional Amount',
        ];
    }

    public function getAddservicesLists()
    {
        return $this->hasOne(AddservicesLists::className(), ['id' => 'add_service_id']);
    } 


}
