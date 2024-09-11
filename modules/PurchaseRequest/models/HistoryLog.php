<?php

namespace app\modules\PurchaseRequest\models;

use app\modules\user\models\Profile;
use Yii;

/**
 * This is the model class for table "{{%pr_type}}".
 *
 * @property int $id
 * @property string $type_name
 *
 * @property PrMain[] $prMains
 */
class HistoryLog extends \yii\db\ActiveRecord
{

    public $division;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%pr_logs}}';
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
            [['pr_id', 'action_date', 'action_user_id', 'action_status'], 'required'],
            [[ 'remarks'], 'safe'],
            [['remarks'], 'string'],
          //  [['action_date'], 'date','format' => 'd-M-yyyy H:m'],
        ];
    }

    /**
     * {@inheritdoc}
     */

    public function attributeLabels()
    {
        return [

            'id' => 'ID',
            'pr_id' => 'PR Number',
            'action_date' => 'Process Date and Time',
            'action_status' => 'Status',
            'action_user_id' => 'Action By',
            'remarks' => 'Remarks',


        ];
    }

    public function getTrackstatus()
    {
        return $this->hasOne(TrackStatus::className(), ['id' => 'action_status']);
    }

    public function getPurchaserequest()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    }

    public function getProfiledisplay()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'action_user_id']);
    }

     


}
