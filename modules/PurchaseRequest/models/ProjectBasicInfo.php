<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "project_basic_info".
 *
 * @property int $id
 * @property string $program_status
 * @property string $program_title
 * @property string $imp_agency
 * @property string $economic_agenda
 * @property string $eleven_point_agenda
 * @property string $rd_agenda
 * @property string $theme_applying_for
 * @property string $monitoring_agency
 * @property string $prexc
 * @property string $project_title
 * @property string $project_desc
 * @property string $project_obj
 * @property string $project_leader
 * @property string $date_of_prep
 * @property string $co_project_leader
 * @property string $daterange_from
 * @property string $daterange_to
 * @property int $archive
 * @property int $approve_status
 */
class ProjectBasicInfo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_basic_info';
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
            [['program_status', 'program_title', 'imp_agency', 'economic_agenda', 'eleven_point_agenda', 'rd_agenda', 'theme_applying_for', 'monitoring_agency', 'prexc', 'project_title', 'project_desc', 'project_obj', 'project_leader', 'date_of_prep', 'co_project_leader', 'daterange_from', 'daterange_to', 'archive', 'approve_status'], 'required'],
            [['date_of_prep'], 'safe'],
            [['archive', 'approve_status'], 'integer'],
            [['program_status', 'program_title', 'imp_agency', 'economic_agenda', 'eleven_point_agenda', 'rd_agenda', 'theme_applying_for', 'monitoring_agency', 'prexc', 'project_title', 'project_desc', 'project_obj', 'project_leader', 'co_project_leader', 'daterange_from', 'daterange_to'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_status' => 'Program Status',
            'program_title' => 'Program Title',
            'imp_agency' => 'Imp Agency',
            'economic_agenda' => 'Economic Agenda',
            'eleven_point_agenda' => 'Eleven Point Agenda',
            'rd_agenda' => 'Rd Agenda',
            'theme_applying_for' => 'Theme Applying For',
            'monitoring_agency' => 'Monitoring Agency',
            'prexc' => 'Prexc',
            'project_title' => 'Project Title',
            'project_desc' => 'Project Desc',
            'project_obj' => 'Project Obj',
            'project_leader' => 'Project Leader',
            'date_of_prep' => 'Date Of Prep',
            'co_project_leader' => 'Co Project Leader',
            'daterange_from' => 'Daterange From',
            'daterange_to' => 'Daterange To',
            'archive' => 'Archive',
            'approve_status' => 'Approve Status',
        ];
    }

    public function getPurchaserequest()
    {
        return $this->hasOne(PurchaseRequest::className(), ['charge_to' => 'id']);
    }
}
