<?php

namespace app\modules\PurchaseRequest\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\WorkOrder;

/**
 * PurchaseOrderSearch represents the model behind the search form of `app\modules\PurchaseRequest\models\PurchaseOrder`.
 */
class WorkOrderSearch extends WorkOrder
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'wo_no', 'pr_id', 'ors_burs_num', 'created_by', 'wo_status'], 'integer'],
            [['place_delivery', 'date_delivery', 'payment_term', 'date_ors_burs', 'wo_date_created', 'time_stamp'], 'safe'],
       
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
    public function search($params, $id)
    {
        $query = WorkOrder::find()->where(['pr_id' => $id])->andWhere(['wo_status' => '4']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function searchAll($params)
    {
        $query = PurchaseOrder::find()->where(['not', ['wo_status' => '3']])->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function searchAllcancelled($params)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => '3'])->orderBy(['id' => SORT_DESC]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function euserwowolist($params)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => ['3', '4', '5', '6', '7']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function budgetwowolist($params)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => '2']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function fmd($params, $id)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => ['2', '32']])->andWhere(['pr_id' => $id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function accounting($params, $id)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => ['4']])->andWhere(['pr_id' => $id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function ppms($params, $id)
    {
        $query = PurchaseOrder::find()->andWhere(['pr_id' => $id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function conforme($params, $id)
    {
        $query = PurchaseOrder::find()
            ->where(['pr_id' => $id])
            ->andWhere(['wo_status' => ['6', '7']]);
        // var_dump($query);die;
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function conformeLeft($params)
    {
        $query = PurchaseOrder::find()
            ->where(['conforme_status' => ['1', '2']]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function delivery($params, $id)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => ['6', '7']])->andWhere(['pr_id' => $id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

    public function deliveryLists($params)
    {
        $query = PurchaseOrder::find()->where(['wo_status' => '7']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'wo_no' => $this->wo_no,
            'pr_id' => $this->pr_id,
            'date_delivery' => $this->date_delivery,
            'ors_burs_num' => $this->ors_burs_num,
            'date_ors_burs' => $this->date_ors_burs,
            'wo_date_created' => $this->wo_date_created,
            'created_by' => $this->created_by,
            'time_stamp' => $this->time_stamp,
            'wo_status' => $this->wo_status,
        ]);

        $query->andFilterWhere(['like', 'place_delivery', $this->place_delivery])
            ->andFilterWhere(['like', 'payment_term', $this->payment_term]);

        return $dataProvider;
    }

}
