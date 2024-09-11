<?php

namespace app\modules\document\models;

use Yii;
use mdm\admin\models\User;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%purchase_request}}".
 *
 * @property int $id
 * @property string $pr_no
 * @property string|null $responsiblity_code
 * @property string $date_of_pr
 * @property string $purpose
 * @property int $charge_to
 * @property int $type
 * @property string $time_stamp
 * @property int|null $approved_by
 * @property int $created_by
 * @property int $archived
 */
class PurchaseRequest extends \yii\db\ActiveRecord
{

    public $action;
    public $date;
    public $time;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%pr_main}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('pr');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_of_pr', 'purpose', 'pr_type_id'], 'required'],
            [['pr_no', 'temp_no', 'created_by', 'section', 'division', 'mode_pr_id', 'revised_series_no', 'budget_clustering_id', 'requested_by', 'fund_source_id'], 'safe'],
            [['time_stamp', 'action', 'date', 'time'], 'safe'],
            [['purpose'], 'string'],
            [['warranty',], 'default', 'value' => 'N/A'],
            [['charge_to', 'type', 'approved_by', 'created_by', 'archived',], 'safe'],
            [['pr_no', 'responsibility_code'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_no' => 'PR No',
            'responsibility_code' => 'Responsibility Code',
            'requested_by' => 'Requested By',
            'division' => 'Division',
            'section' => 'Section',
            'date_of_pr' => 'Date Prepared',
            'purpose' => 'Purpose',
            'charge_to' => 'Charge To',
            'pr_type_id' => 'Purchasing Type',
            'mode_pr_id' => 'Mode of Procurement',
            'time_stamp' => 'Time Stamp',
            'approved_by' => 'Approved By',
            'created_by' => 'Created By',
            'status' => 'Status',
            'archived' => 'Archived',
            'total_amount' => 'Total Amount',
            'delivery_period' => 'Delivery Period',
            'warranty' => 'Warranty',
            'temp_no' => 'PR Number',
            'budget_clustering_id' => 'Budget Clustering',
            'sdo_officer_id' => 'SDO Officer',
            'proj_accountant_id' => 'Project Accountant',
            'revised_series_no' => 'Revised PR Series',
            'fund_source_id' => 'Fund Source',
            'selectuser' => Yii::t('app', 'Select user')
        ];
    }
}
