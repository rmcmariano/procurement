<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "supplier".
 *
 * @property int $id
 * @property int $tin_no
 * @property string $supplier_name
 * @property string $supplier_address
 * @property int $account_no
 * @property int $tel_no
 * @property string $owner_name
 */
class Supplier extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'supplier';
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
            [['tin_no', 'supplier_name', 'supplier_address', 'account_no', 'tel_no', 'owner_name', 'contact_id', 'fax_no', 'classification_philgeps', 'business_type_id', 'action_by', 'action_date' ], 'safe'],
            [['tin_no', 'account_no', 'tel_no'], 'integer'],
            [['supplier_name'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tin_no' => 'Tin No',
            'supplier_name' => 'Supplier Name: ',
            'supplier_address' => 'Supplier Address',
            'account_no' => 'Account No',
            'tel_no' => 'Tel No',
            'owner_name' => 'Owner Name',
            'contact_id' => 'Contact Person',
            'fax_no' => 'Fax No.',
            'classification_philgeps' => 'PHILGEPS', 
            'business_type_id' => 'Business Type',
            'action_by' => 'Action By',
            'action_date' => 'Action Date'
        ];
    }

    public function getBiddinglist()
    {
        return $this->hasMany(BiddingList::className(), ['supplier_id' => 'id']); 
    }

    public function getPurchaseorder()
    {
        return $this->hasMany(PurchaseOrder::className(), ['supplier_id' => 'id']); 
    }

    public function getSuppliersContact()
    {
        return $this->hasMany(SupplierContacts::className(), ['supplier_id' => 'id']); 
    }



}
