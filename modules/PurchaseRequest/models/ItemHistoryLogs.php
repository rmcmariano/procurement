<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "item_history_logs".
 *
 * @property int $id
 * @property int $item_id
 * @property string $action_date
 * @property int $action_status
 * @property int $action_by
 */
class ItemHistoryLogs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%item_history_logs}}';
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
            [['item_id', 'action_status', 'action_by', 'action_remarks'], 'safe'],
            [['item_id', 'action_status', 'action_by'], 'integer'],
            [['action_date'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'item_id' => 'Description ID',
            'action_date' => 'Action Date',
            'action_status' => 'Action Status',
            'action_by' => 'Action By',
            'action_remarks' => 'Action Remarks',
        ];
    }

    public function getTrackstatusDisplay()
    {
        return $this->hasOne(TrackStatus::className(), ['id' => 'action_status']);
    }

    public function getDescriptionDisplay()
    {
         return $this->hasMany(PrItems::className(), ['id' => 'item_id']);
    } 

    public static function itemStatus($id, $status){

        $itemHistorylog = new ItemHistoryLogs();

        $itemHistorylog->item_id = $id;
        $itemHistorylog->action_date = date('Y-m-d h:i');
        $itemHistorylog->action_status = $status;
        $itemHistorylog->action_by = Yii::$app->user->identity->id;
        $itemHistorylog->action_remarks = 'Reference #: '. $_POST['remarks'];
    
        $itemHistorylog->save();
    }
}
