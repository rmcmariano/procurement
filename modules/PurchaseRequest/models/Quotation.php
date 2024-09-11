<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "quotation".
 *
 * @property int $pr_id
 * @property int $quotation_no
 * @property string $date_preprocurement
 * @property string $date_posted_pb
 * @property string $date_prebid_conf
 * @property string $option_date
 * @property string $remarks
 */
class Quotation extends \yii\db\ActiveRecord
{
    public $time;
    public $date;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'quotation';
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
            [['pr_id', 'quotation_no', 'option_date', 'remarks', 'date_posted_philgeps', 'reference_no', 'status', 'date', 'time'], 'safe'],
            [['pr_id', 'option_id', 'reference_no'], 'safe'],
            [['option_date'], 'safe'],
            [['remarks'], 'string'],
            [['time_stamp'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'time_stamp' => 'Date Created',
            'pr_id' => '',
            'quotation_no' => 'Quotation Number',
            'option_id' => 'Details',
            'option_date' => 'Date',
            'remarks' => 'Remarks',
            'reference_no' => 'Reference Number: ',
            'status' => 'Status'

        ];
    }

    public function getOptionsdisplay()
    {
        return $this->hasOne(DateOptions::className(), ['id' => 'option_id']);
    }

    public function getPurchaseRequest()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    }

    public function getPrItems()
    {
        return $this->hasMany(PrItems::className(), ['pr_id' => 'pr_id']);
    }

    public function getBidding()
    {
        return $this->hasMany(BiddingList::className(), ['pr_id' => 'pr_id']);
    }

    public function getPrebid()
    {
        $prebid = $this->option_id = 1;
        return $prebid;
    }
}
