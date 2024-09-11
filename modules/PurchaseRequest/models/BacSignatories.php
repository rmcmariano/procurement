<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "bac_signatories".
 *
 * @property int $id
 * @property int $bid_id
 * @property int $member_id
 * @property string $chairperson_id
 * @property int $co_chairperson_id
 */
class BacSignatories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bac_signatories';
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
            [['bid_id', 'chairperson_id', 'co_chairperson_id'], 'safe'],
            [['bid_id', 'chairperson_id', 'co_chairperson_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bid_id' => 'Bid ID',
            'chairperson_id' => 'Chairperson',
            'co_chairperson_id' => 'Co-Chairperson',
        ];
    }

    public function getBiddingdisplay()
    {
        return $this->hasOne(BiddingList::className(), ['id' => 'bid_id']);
    } 
    
    public function getBac(){
        return $this->hasMany(MemberSignatories::className(), ['bac_signatories_id' => 'id']);
    }

}
