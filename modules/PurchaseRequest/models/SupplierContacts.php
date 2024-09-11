<?php

namespace app\modules\PurchaseRequest\models;

use Yii;


class SupplierContacts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%supplier_contacts}}';
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
            [['assigned_dept','contact_person'], 'safe'],
            [['contact_person'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'supplier_id' => 'Supplier ID',
            'assigned_dept' => 'Assigned Department',
            'contact_person' => 'Contact Person',
            'contact_no' => 'Contact No.',

        ];
    }

    public function getSuppliers()
    {
        return $this->hasOne(Supplier::className(), ['id' => 'supplier_id']); 
    }

}
