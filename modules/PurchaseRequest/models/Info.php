<?php

namespace app\modules\PurchaseRequest\models;

use app\models\profile\Profile;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "project_basic_info".
 *
 * @property int $id
 * @property string $source_of_fund
 * @property string $funding_agency
 * @property string $approved_budget
 * @property string $imp_agency
 * @property string $theme_applying_for
 * @property string $monitoring_agency
 * @property string $prexc
 * @property string $program_title
 * @property string $project_title
 * @property string $project_desc
 * @property string $project_obj
 * @property string $program_status
 * @property string $project_leader
 * @property string $date_of_prep
 * @property string $co_project_leader
 */
class Info extends \yii\db\ActiveRecord
{
    public $file;
    public $po_file;
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
        return Yii::$app->get('itdidb_pmis');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['program_status', 'imp_agency', 'sdg', 'economic_agenda', 'eleven_point_agenda', 'rd_agenda', 'theme_applying_for', 'monitoring_agency', 'prexc', 'project_title', 'project_desc', 'project_obj', 'date_of_prep', 'daterange_from', 'daterange_to',], 'required'],
            [['program_title', 'date_approved', 'project_leader', 'co_project_leader', 'update_logs', 'update_logs_user', 'lib_status', 'accountant_id'], 'safe'],
            [['program_status', 'program_title', 'imp_agency', 'economic_agenda', 'eleven_point_agenda', 'rd_agenda', 'theme_applying_for', 'monitoring_agency', 'prexc', 'project_title', 'project_desc', 'project_obj', 'project_leader', 'date_of_prep', 'co_project_leader', 'daterange_from', 'daterange_to', 'date_approved',], 'string', 'max' => 255],

            [['program_title', 'project_title', 'project_desc', 'project_obj',], 'default', 'value' => 'N/A'],
            // [['program_title'], 'default', 'value' => 1],
            [['approve_status', 'lib_status', 'accountant_id'], 'default', 'value' => 0],
            [['date_approved', 'update_logs',], 'default', 'value' => '0000-00-00'],

            [['project_leader', 'co_project_leader',], 'default', 'value' => NULL],
            [['project_leader', 'co_project_leader',], 'trim'],
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
            'imp_agency' => 'Implementing Agency',
            'sdg' => 'Sustainable Development Goals (SDGs)',
            'economic_agenda' => 'National Socio-Economic Agenda',
            'eleven_point_agenda' => 'DOST Agenda',
            'rd_agenda' => 'Harmonized National R&D Agenda',
            'theme_applying_for' => 'Theme Applying For',
            'monitoring_agency' => 'Monitoring Agency',
            'prexc' => 'Program Expenditure Classification (PREXC)',
            'project_title' => 'Project Title',
            'project_desc' => 'Project Description',
            'project_obj' => 'Project Objectives',
            'project_leader' => 'Project Leader',
            'date_of_prep' => 'Date Of Preparation',
            'co_project_leader' => 'Co Project Leader',
            'daterange_from' => 'Project Duration Date from:',
            'daterange_to' => 'Project Duration Date to:',
        ];
    }

    public function getSofdynamic()
    {
        return $this->hasMany(Sofdynamic::class, ['sof_id' => 'id']);
    }

    public function getAddr()
    {
        return $this->hasMany(Addrdynamic::class, ['addr_id' => 'id']);
    }

    public function getCo_imp_dynamic()
    {
        return $this->hasMany(Co_imp_dynamic::class, ['co_imp_id' => 'id']);
    }

    public function getProjectDetails()
    {
        return $this->hasOne(Projectdetails::class, ['pbinfo_id' => 'id']);
    }

    public function getRiskmp()
    {
        return $this->hasOne(Projectriskmp::class, ['pbinfo_id' => 'id']);
    }

    public function getRiskmps()
    {
        return $this->hasMany(Projectriskmp::class, ['pbinfo_id' => 'id']);
    }

    public function getRiskmpuploads()
    {
        return $this->hasMany(Riskmpuploads::class, ['riskmp_id' => 'id'])->where(['riskmp_archive' => 0]);
    }

    public function getPouploads()
    {
        return $this->hasMany(Pouploads::class, ['po_id' => 'id'])->where(['po_archive' => 0]);
    }

    public function getProjectexp()
    {
        return $this->hasMany(Projectexpoutput::class, ['pbinfo_id' => 'id']);
    }

    public function getPcbs()
    {
        return $this->hasOne(Project_class_by_sta::class, ['pbinfo_id' => 'id']);
    }

    public function getFieldsst()
    {
        return $this->hasOne(Projectfieldsst::class, ['pbinfo_id' => 'id']);
    }

    public function getPoProfile()
    {
        return $this->hasOne(Po_profile::class, ['user_id' => 'co_project_leader']);
    }

    public function getPbw_lib_main()
    {
        return $this->hasOne(Pbw_lib_main::class, ['pbworkplan_id' => 'id']);
    }

    public function getPbw_lib_ps()
    {
        return $this->hasMany(Pbw_lib_ps::class, ['ps_id' => 'id']);
    }

    public function getPddynamic()
    {
        return $this->hasMany(Pddynamic::class, ['pd_id' => 'id']);
    }

    public function getPbwtarget()
    {
        return $this->hasMany(Pbwtarget::class, ['pbworkplan_id' => 'id']);
    }

    public function getPbwworkplan()
    {
        return $this->hasMany(Pbwworkplan::class, ['pbworkplan_id' => 'id']);
    }

    public function getProjectLeader()
    {
        return $this->hasOne(\app\models\profile\Profile::class, ['id' => 'project_leader']);
    }

    public function getCoprojectLeader()
    {
        return $this->hasOne(\app\models\profile\Profile::class, ['id' => 'co_project_leader']);
    }

    public function getOptionValues()
    {
        return $this->hasMany(Pd_option_value::class, ['image_id' => 'id']);
    }

    public function getGadPids()
    {
        return $this->hasOne(Gad_pids::class, ['pbinfo_id' => 'id']);
    }

    public static function getCoProjectLeaders()
    {
        $cplIds = ArrayHelper::getColumn(self::find()->select(['co_project_leader'])->all(), 'co_project_leader');

        return Profile::find()->where(['id' => $cplIds])->all();
    }

    public static function getProjectLeaders()
    {
        $plIds = ArrayHelper::getColumn(self::find()->select(['project_leader'])->all(), 'project_leader');

        return Profile::find()->where(['id' => $plIds])->all();
    }

    public function getImpAgency()
    {
        return $this->hasOne(Faselect::class, ['id' => 'imp_agency']);
    }

    public function getEcoAgenda()
    {
        return $this->hasOne(Np_eco_agenda_select::class, ['id' => 'economic_agenda']);
    }

    public function getElevenAgenda()
    {
        return $this->hasOne(Np_11point_agenda_select::class, ['id' => 'eleven_point_agenda']);
    }

    public function getRdAgenda()
    {
        return $this->hasOne(Np_rd_agenda_select::class, ['id' => 'rd_agenda']);
    }

    public function getThemeApp()
    {
        return $this->hasOne(Tafselect::class, ['id' => 'theme_applying_for']);
    }

    public function getMonAgency()
    {
        return $this->hasOne(Faselect::class, ['id' => 'monitoring_agency']);
    }

    public function getPrexcField()
    {
        return $this->hasOne(Prexcselect::class, ['id' => 'prexc']);
    }

    // public function getprojectStatus1() {
    //     return $this->hasOne(Prstatselect::class, ['id' => 'prstat_name']);
    // }

    // Step 1: Sustainable Development Goals (SDGs)
    public static function getSdgValue()
    {
        $sdg_id = ArrayHelper::getColumn(self::find()->select(['sdg'])->all(), 'sdg');

        return Sdg_select::find()->where(['id' => $sdg_id])->all();
    }

    public function getsdgValue_display()
    {
        return $this->hasOne(\app\modules\pmis\models\Sdg_select::class, ['id' => 'sdg']);
    }
    // /Step 1: Sustainable Development Goals (SDGs)

    // Step 1: Program Status
    public static function getProjectStatus()
    {
        $prs_id = ArrayHelper::getColumn(self::find()->select(['program_status'])->all(), 'program_status');

        return Prstatselect::find()->where(['id' => $prs_id])->all();
    }

    public function getsdgPS_display()
    {
        return $this->hasOne(\app\modules\pmis\models\Prstatselect::class, ['id' => 'program_status']);
    }

    public function getProjectStatus_eye()
    {
        return $this->hasOne(Prstatselect::class, ['id' => 'program_status']);
    }
    // /Step 1: Program Status

    // Step 1: Program Title
    public static function getProgTitle()
    {
        $prg_id = ArrayHelper::getColumn(self::find()->select(['program_title'])->all(), 'program_title');

        return Pt_pr::find()->where(['id' => $prg_id])->all();
    }

    public function getprogTitle_display()
    {
        return $this->hasOne(\app\modules\pmis\models\Pt_pr::class, ['id' => 'program_title']);
    }

    public function getProgTitle_eye()
    {
        return $this->hasOne(Pt_pr::class, ['id' => 'program_title']);
    }
    // /Step 1: Program Title

    public function getChargeto()
    {
        $charge_to = self::find()
            ->select(['id', 'project_ttile as name'])
            ->asArray()
            ->all();

        return $charge_to;
    }
}
