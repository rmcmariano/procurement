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
class Resolution extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resolution_tbl';
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
            [['resolution_no', 'resolution_date', 'created_by', 'pr_id'], 'safe'],
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
            'pr_id' => 'PR Id',
            'resolution_no' => 'Reso No',
            'time_stamp' => 'Time Stamp',
            'resolution_date' => 'Date Posted',
            'created_by' => 'Created By',
        ];
    }





    
}
