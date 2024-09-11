<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\TrackStatus;
use yii\helpers\ArrayHelper;
use app\modules\user\models\Profile;
use app\models\HistoryLog;

use app\modules\user\models\User;

class PurchaseRequestSearch extends PurchaseRequest
{
    public $selectuser;
    public $prtype;
    public $quotation;
    public $chargedisplay;
    public $sectiondisplay;
    public $procurementmode;
    public $budgetdisplay;
    public $pr_no;
    public $temp_no;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'charge_to', 'pr_type_id', 'approved_by', 'requested_by', 'division', 'section', 'created_by', 'archived', 'status', 'temp_no', 'mode_pr_id', 'budget_clustering_id'], 'safe'],
            [['pr_no', 'responsibility_code', 'date_of_pr', 'purpose', 'time_stamp'], 'safe'],
            [['selectuser', 'prtype', 'chargedisplay', 'procurementmode', 'quotation'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */

    public function all($params)
    {
        // $profile = Profile::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
        $query = PurchaseRequest::find()
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['pr_no'] = [
            'asc' => ['pr_no' => SORT_ASC],
            'desc' => ['pr_no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
            'temp_no' => $this->temp_no
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'temp_no', $this->temp_no])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);

        return $dataProvider;
    }

    public function search($params)
    {
        // $profile = Profile::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
        $query = PurchaseRequest::find()
            ->where([
                'AND',
                // ['created_by' => Yii::$app->user->identity->id],
                ['not', ['status' => ['1', '8', '3', '31', '42', '43', '18', '58']]]
            ])
            //     ->orWhere([
            //         'AND',
            //         ['requested_by' => $profile->id],
            //         ['not', ['status' => ['1', '8', '3', '31', '42', '43', '18', '58']]]
            //     ])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $query->joinWith(['prtype', 'chargedisplay', 'sectiondisplay']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['pr_no'] = [
            'asc' => ['pr_no' => SORT_ASC],
            'desc' => ['pr_no' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
            'temp_no' => $this->temp_no
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'temp_no', $this->temp_no])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);

        return $dataProvider;
    }

    public function pending($params)
    {
        $profile = Profile::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
        $query = PurchaseRequest::find()
            ->where([
                'AND',
                ['created_by' => Yii::$app->user->identity->id],
                ['status' => ['1', '8', '3', '42', '43', '18', '58']]
            ])
            ->orWhere([
                'AND',
                ['requested_by' => $profile->id],
                ['status' => ['1', '8', '3', '42', '43', '18', '58']]
            ])
            // ->orWhere([
            //     'AND',
            //     ['approved_by' => Yii::$app->user->identity->id],
            //     ['status' => ['1', '8', '3', '42', '43', '18', '58']]
            // ])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $query->joinWith(['prtype', 'chargedisplay', 'sectiondisplay']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
            'temp_no' => $this->temp_no
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'temp_no', $this->temp_no])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);

        return $dataProvider;
    }

    public function bacMonitoring($params)
    {

        $bacStatus = PrItems::find()->select('pr_id')->column();

        $query = PurchaseRequest::find()
            // ->where(['pr_main.status' => $bacStatus]) // Specify the table name or alias for the id column
            ->where(['pr_type_id' => '3']) // Also specify the table name for pr_type_id
            ->andWhere(['not', ['quotation.quotation_no' => null]])
            ->joinWith(['quotation']); // Ensure to join with the 'quotation' relation


        $dataProvider = new ActiveDataProvider([
            'query' => $query,

        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'mode_pr_id' => $this->mode_pr_id,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay])
            ->andFilterWhere(['like', 'procurementmode.mode_name', $this->procurementmode])
            ->andFilterWhere(['like', 'quotation.quotation_no', $this->quotation]);

        return $dataProvider;
    }

    public function budgetMonitoring($params)
    {
        $query = PurchaseRequest::find()
            ->innerJoinWith('historyprnumber', false)
            ->where(['pr_logs.action_status' => 4]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'mode_pr_id' => $this->mode_pr_id,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay])
            ->andFilterWhere(['like', 'procurementmode.mode_name', $this->procurementmode])
            ->andFilterWhere(['like', 'quotation.quotation_no', $this->quotation]);

        return $dataProvider;
    }

    public function chiefMonitoring($params)
    {
        $query = PurchaseRequest::find()
            ->innerJoinWith('historyprnumber', false)
            ->where([
                'AND',
                ['approved_by' => Yii::$app->user->identity->id],
                ['pr_logs.action_status' => 2]
              
            ])
            ->orWhere([
                'AND',
                ['division' => Yii::$app->user->identity->division_id],
                ['pr_logs.action_status' => 2]
            ])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'mode_pr_id' => $this->mode_pr_id,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay])
            ->andFilterWhere(['like', 'procurementmode.mode_name', $this->procurementmode])
            ->andFilterWhere(['like', 'quotation.quotation_no', $this->quotation]);

        return $dataProvider;
    }

    public function archive($params)
    {
        $profile = Profile::find()->where(['user_id' => Yii::$app->user->identity->id])->one();
        $query = PurchaseRequest::find()
            ->where([
                'AND',
                ['requested_by' => $profile->id],
                ['status' => ['31', '18']]
            ])
            ->orWhere([
                'AND',
                ['created_by' => Yii::$app->user->identity->id],
                ['status' => ['31', '18']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);

        return $dataProvider;
    }


    public function Chief($params)
    {
        $query = PurchaseRequest::find()
            ->where([
                'AND',
                ['approved_by' => Yii::$app->user->identity->id],
                ['status' =>  ['1', '41', '2']]
            ])
            ->orWhere([
                'AND',
                ['division' => Yii::$app->user->identity->division_id],
                ['status' =>  ['1', '41', '2']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
            'temp_no' => $this->temp_no
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'temp_no', $this->temp_no])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->sectiondisplay]);

        return $dataProvider;
    }

    public function Budget($params)
    {
        $items = PrItems::find()
            ->where(['status' => [2, 32,]])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $query = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['2', '51', '59']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);


        return $dataProvider;
    }

    public function budgetApproved($params)
    {
        $items = PrItems::find()
            ->where(['status' => [2, 32,]])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $query = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['7']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);


        return $dataProvider;
    }

    public function Accounting($params)
    {
        $items = PrItems::find()
            ->where(['status' => ['22', '5']])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $query = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['7', '5']]
            ])
            ->orWhere([
                'AND',
                ['pr_type_id' => 2],
                ['status' => ['2', '5']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);

        return $dataProvider;
    }

    public function sdo($params)
    {
        $query = PurchaseRequest::find()
            ->where(['division' => Yii::$app->user->identity->division_id])
            ->andWhere(['status' => [2]])
            ->andWhere(['pr_type_id' => 1])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay]);

        return $dataProvider;
    }

    public function Payment($params)
    {
        $query = PurchaseRequest::find()
            ->where(['pr_type_id' => [2, 3]])
            ->andWhere(['status' => [18]])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'requested_by' => $this->requested_by,
            'division' => $this->division,
            'section' => $this->section,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose]);

        return $dataProvider;
    }

    public function Ppms($params)
    {
        $items = PrItems::find()
            ->where(['status' => TrackStatus::PPMS_STATUS])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        // $query = PurchaseRequest::find()
        //     ->where(['id' => $listData])
        //     ->andWhere(['pr_type_id' => ['8', '6', '3']])
        //     ->andWhere(['status' => ['7', '44', '45', '46', '51']])
        //     ->orderBy(['time_stamp' => SORT_DESC]);

        $query = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['7', '44', '45', '46', '51']]
            ])
            ->orWhere([
                'AND',
                ['pr_type_id' => ['8']],
                ['status' => ['5', '4']]
            ])->orderBy(['time_stamp' => SORT_DESC]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['procurementmode'] = [
            'asc' => ['procurementmode.mode_name' => SORT_ASC],
            'desc' => ['procurementmode.mode_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['budgetdisplay'] = [
            'asc' => ['budgetdisplay.expenses_name' => SORT_ASC],
            'desc' => ['budgetdisplay.expenses_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'status' => $this->status,
            'archived' => $this->archived,
            'budget_clustering_id' => $this->budget_clustering_id
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay])
            ->andFilterWhere(['like', 'budgetdisplay.expenses_name', $this->budgetdisplay])
            ->andFilterWhere(['like', 'procurementmode.mode_name', $this->procurementmode]);

        return $dataProvider;
    }

    public function Bac($params)
    {
        $bacStatus = PrItems::find()
            ->where(['status' => TRACKSTATUS::BAC_STATUS])
            ->asArray()->all();

        $listData = ArrayHelper::map($bacStatus, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $query = PurchaseRequest::find()->where(['id' => $listData])
            ->andWhere(['status' => ['7', '4', '44', '45', '36', '49', '54', '56', '48', '61', '60']])
            ->andWhere(['pr_type_id' => ['3', '4']])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $dataProvider->sort->attributes['procurementmode'] = [
            'asc' => ['procurementmode.mode_name' => SORT_ASC],
            'desc' => ['procurementmode.mode_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['prtype'] = [
            'asc' => ['tbl_prtype.type_name' => SORT_ASC],
            'desc' => ['tbl_prtype.type_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['chargedisplay'] = [
            'asc' => ['chargedisplay.project_title' => SORT_ASC],
            'desc' => ['chargedisplay.project_title' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['sectiondisplay'] = [
            'asc' => ['sectiondisplay.section_code' => SORT_ASC],
            'desc' => ['sectiondisplay.section_code' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'date_of_pr' => $this->date_of_pr,
            'charge_to' => $this->charge_to,
            'pr_type_id' => $this->pr_type_id,
            'division' => $this->division,
            'section' => $this->section,
            'requested_by' => $this->requested_by,
            'time_stamp' => $this->time_stamp,
            'approved_by' => $this->approved_by,
            'created_by' => $this->created_by,
            'mode_pr_id' => $this->mode_pr_id,
            'status' => $this->status,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no])
            ->andFilterWhere(['like', 'responsibility_code', $this->responsibility_code])
            ->andFilterWhere(['like', 'purpose', $this->purpose])
            ->andFilterWhere(['like', 'tbl_prtype.type_name', $this->prtype])
            ->andFilterWhere(['like', 'chargedisplay.project_title', $this->chargedisplay])
            ->andFilterWhere(['like', 'sectiondisplay.section_code', $this->chargedisplay])
            ->andFilterWhere(['like', 'procurementmode.mode_name', $this->procurementmode]);

        return $dataProvider;
    }
}
