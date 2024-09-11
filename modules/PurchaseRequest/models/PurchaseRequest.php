<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use mdm\admin\models\User;
use app\modules\user\models\Profile;
use app\modules\PurchaseRequest\models\Section;
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

    public $office;
    public $section_id;
    public $division_id;
    public $file;
    public $requested_by1;
    public $requested_by2;
    public $requested_by3;

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
        return Yii::$app->get('itdidb_procurement_system');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_of_pr', 'purpose', 'pr_type_id', 'approved_by', 'requestedby_position', 'approvedby_position', 'delivery_period', 'warranty'], 'safe'],
            [['pr_no', 'temp_no', 'created_by',  'office', 'section', 'division', 'mode_pr_id', 'revised_series_no', 'budget_clustering_id', 'requested_by', 'requested_by1', 'requested_by2', 'requested_by3',  'fund_source_id'], 'safe'],
            [['time_stamp'], 'safe'],
            [['purpose'], 'string'],
            [['indirect_direct_cost'], 'default', 'value' => 0],
            // [['warranty',], 'default', 'value' => 'N/A'],
            [['charge_to', 'type', 'created_by', 'archived',], 'safe'],
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
            'requestedby_position' => 'Position',
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
            'selectuser' => Yii::t('app', 'Select user'),
            'approvedby_position' => 'Approved By Position'
        ];
    }

    public function getPrItems()
    {
        return $this->hasMany(PrItems::className(), ['pr_id' => 'id']);
    }

    public function getPrtype()
    {
        return $this->hasOne(PrType::className(), ['id' => 'pr_type_id']);
    }

    public function getBudgetdisplay()
    {
        return $this->hasOne(BudgetClustering::className(), ['id' => 'budget_clustering_id']);
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['id' => 'requested_by']);
    }


    public function getApprovedBy()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'approved_by']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id'])->via('profile');
    }

    public function getSectionThruUser()
    {

        return $this->hasOne(Section::class, ['id' => 'section_id'])->via('user');
    }

    public function getDivisionThruUser()
    {

        return $this->hasOne(Division::class, ['id' => 'division_id'])->via('user');
    }

    public function getPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'requestedby_position']);
    }

    public function getApprovedbyPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'approvedby_position']);
    }

    // public function getApprovedChief()
    // {
    //     return $this->hasOne(Profile::class, ['user_id' => 'approved_by']);
    // }

    public function getDivisiondisplay()
    {
        return $this->hasOne(Division::className(), ['id' => 'division']);
    }

    public function getSectiondisplay()
    {
        return $this->hasOne(Section::className(), ['id' => 'section']);
    }

    public function getChargedisplay()
    {
        return $this->hasOne(Info::className(), ['id' => 'charge_to']);
    }

    public function getStatusdisplay()
    {
        return $this->hasOne(TrackStatus::className(), ['id' => 'status']);
    }

    public function getHistoryprnumber()
    {
        return $this->hasMany(HistoryLog::className(), ['pr_id' => 'id']);
    }

    public function getProcurementmode()
    {
        return $this->hasOne(ProcurementMode::className(), ['mode_id' => 'mode_pr_id']);
    }

    public function getQuotation()
    {
        return $this->hasMany(Quotation::className(), ['pr_id' => 'id']);
    }

    public function getBidding()
    {
        return $this->hasMany(BiddingList::className(), ['pr_id' => 'id']);
    }

    public function getOrderdisplay()
    {
        return $this->hasMany(PurchaseOrder::className(), ['pr_id' => 'id']);
    }

    public function getInputfile()
    {
        return $this->hasMany(Attachments::className(), ['pr_id' => 'id'])->andWhere(['archived' => 0]);
    }

    public function getEndUsers()
    {
        return $this->hasMany(Profile::class, ['id' => 'user_id'])
            ->via('selectuser');
    }

    public function getEndUserNames()
    {
        return ArrayHelper::getColumn($this->endUsers, function ($element) {
            return $element->fullName;
        });
    }

    public function getEnduser()
    {
        $model = $this->id;
        $end_user = PrEnduser::find()->where(['pr_id' => $model])->all();
        $usernames = '';

        foreach ($end_user as $val) {
            $user = Profile::find()->where(['id' => $val->user_id])->one();
            if ($user) {
                $usernames .= $user->fullname . "<br>";
            }
        }

        return $usernames;
    }

    public function getSelectuser()
    {
        return $this->hasMany(PrEnduser::className(), ['pr_id' => 'id']);
    }

    public function getCountprApproved()
    {
        $modelPr = PurchaseRequest::find()->where(['status' => ['4', '49']])->all();
        $count = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPr])->distinct();
        $countPr = $count->count();
        return $countPr;
    }
}
