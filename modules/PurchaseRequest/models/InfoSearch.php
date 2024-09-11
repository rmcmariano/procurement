<?php

namespace app\modules\pmis\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\pmis\models\Info;

/**
 * InfoSearch represents the model behind the search form of `app\modules\pmis\models\Info`.
 */
class InfoSearch extends Info
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['sdg', 'imp_agency', 'theme_applying_for', 'monitoring_agency', 'prexc', 'program_title', 'project_title', 'project_desc', 'project_obj', 'program_status', 'project_leader', 'date_of_prep', 'co_project_leader', 'archive', 'approve_status'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        
        $query = Info::find();

        $this->load($params);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Sort ID Descending in Gridview:
        $dataProvider->sort = ['defaultOrder' => ['id' => 'DESC']];

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            // var_dump($this->errors);die;
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'approve_status' => $this->approve_status,
            'archive' => $this->archive,
        ]);

        $query
            ->andFilterWhere(['like', 'sdg', $this->sdg])
            ->andFilterWhere(['like', 'program_title', $this->program_title])
            ->andFilterWhere(['like', 'project_title', $this->project_title])
            ->andFilterWhere(['like', 'imp_agency', $this->imp_agency])
            ->andFilterWhere(['like', 'theme_applying_for', $this->theme_applying_for])
            ->andFilterWhere(['like', 'monitoring_agency', $this->monitoring_agency])
            ->andFilterWhere(['like', 'prexc', $this->prexc])
            ->andFilterWhere(['like', 'project_desc', $this->project_desc])
            ->andFilterWhere(['like', 'project_obj', $this->project_obj])
            ->andFilterWhere(['like', 'program_status', $this->program_status])
            ->andFilterWhere(['like', 'project_leader', $this->project_leader])
            ->andFilterWhere(['like', 'date_of_prep', $this->date_of_prep])
            ->andFilterWhere(['like', 'co_project_leader', $this->co_project_leader])
            ->orderBy(['id' => SORT_DESC]);

        return $dataProvider;
    }
}
