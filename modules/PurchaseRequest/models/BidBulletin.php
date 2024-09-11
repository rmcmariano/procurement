<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "bid_bulletin".
 *
 * @property int $id
 * @property int $item_id
 * @property int $bid_bulletin_no
 * @property string $item_changes
 * @property string $time_stamp
 */
class BidBulletin extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bid_bulletin';
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
            [['bidbulletin_no', 'status', 'date_posted'], 'safe'],
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
            'bidbulletin_no' => 'Bid Bulletin No',
            'time_stamp' => 'Time Stamp',
            'date_posted' => 'Date Posted',
            'status' => 'Status',
        ];
    }

    public function getPrItems()
    {
        return $this->hasMany(PrItems::className(), ['bidbulletin_id' => 'id']);
    }





    
}
