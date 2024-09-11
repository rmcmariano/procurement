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
class LessDeductions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'less_deductions';
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
            [[ 'deduction_id', 'deduction_amount'], 'required'],
            [['po_id', 'deduction_id'], 'integer'],
            [['deduction_amount'], 'number'],
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
            'deduction_id' => 'Deduction ID',
            'deduction_amount' => 'Deduction Amount',
        ];
    }

    public function getDeductionList()
    {
        return $this->hasOne(DeductionLists::className(), ['id' => 'deduction_id']);
    } 

    public function getTotalCostDecimal()
    {
        $whole = (int) $this->total_cost;

        $decimal = $this->total_cost - $whole;

        return Yii::$app->formatter->asDecimal($decimal, 2) * 100;
    }

}
