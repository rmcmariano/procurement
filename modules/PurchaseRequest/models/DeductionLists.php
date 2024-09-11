<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "deduction_lists".
 *
 * @property int $id
 * @property string $code
 */
class DeductionLists extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'deduction_lists';
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
            [['code'], 'required'],
            [['code'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
        ];
    }

    public function getLessDeductions()
    {
        return $this->hasMany(LessDeductions::className(), ['deduction_id' => 'id']);
    }

    public function getPurchaseorderItems()
    {
        return $this->hasMany(PurchaseOrderItems::className(), ['deduction_id' => 'id']);
    }
}

