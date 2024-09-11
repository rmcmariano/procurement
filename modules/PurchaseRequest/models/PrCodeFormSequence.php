<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

class PrCodeFormSequence extends \yii\db\ActiveRecord
{
    
    public static function tableName()
    {
        return 'pr_code_form_sequence';
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
            [['pr_code', 'form_sequence'], 'safe'],
            [['form_sequence'], 'integer'],
            [['pr_code'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_code' => 'Division Code',
            'form_sequence' => 'Form Sequence',
        ];
    }
}
