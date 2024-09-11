<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "awarding_list".
 *
 * @property int $id
 * @property int $bid_id
 * @property string $time_stamp
 */
class AwardingList extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%awarding_list}}';
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
            [['bid_id'], 'safe'],
            [['bid_id', 'item_id'], 'integer'],
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
            'bid_id' => 'Bid ID',
            'item_id' => 'Description ID',
            'time_stamp' => 'Time Stamp',
        ];
    }

    public function getDescriptiondisplay()
    {
        return $this->hasMany(PrItems::className(), ['id' => 'item_id']);
    } 
    
        

}
