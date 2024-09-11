<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * BiddingListSearch represents the model behind the search form about `app\modules\PurchaseRequest\models\PurchaseOrderItems`.
 */
class BiddingListSearch extends BiddingList
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'supplier_id', 'supplier_price', 'pr_id', 'po_id', 'assign_twg', 'item_remarks', 'supplier_payment_term', 'bid_bond', 'resolution_no', 'resolution_date'], 'safe'],
            [['supplier_price',], 'double']
        ];
    }

    /**
     * @inheritdoc
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
        $query = BiddingList::find()->where(['status' => ['21', '24', '23', '52', '25']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'item_id' => $this->item_id,
            'supplier_id' => $this->supplier_id,
            'supplier_price' => $this->supplier_price,
            'pr_id' => $this->pr_id,
            'po_id' => $this->po_id,
            'assign_twg' => $this->assign_twg,
            'item_remarks' => $this->item_remarks,
            'supplier_payment_term' => $this->supplier_payment_term,
            'bid_bond' => $this->bid_bond,
            'resolution_no' => $this->resolution_no,
            'resolution_date' => $this->resolution_date,
            'supplier_price' => $this->supplier_price,

        ]);

        return $dataProvider;
    }

    public function budgetPo($params)
    {
        $query = BiddingList::find()->where(['status' => ['32', '22']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'item_id' => $this->item_id,
            'supplier_id' => $this->supplier_id,
            'supplier_price' => $this->supplier_price,
            'pr_id' => $this->pr_id,
            'po_id' => $this->po_id,
            'assign_twg' => $this->assign_twg,
            'item_remarks' => $this->item_remarks,
            'supplier_payment_term' => $this->supplier_payment_term,
            'bid_bond' => $this->bid_bond,
            'resolution_no' => $this->resolution_no,
            'resolution_date' => $this->resolution_date,
            'supplier_price' => $this->supplier_price,

        ]);

        return $dataProvider;
    }

    public function resolution($params)
    {
        $query = BiddingList::find()
            ->where(['status' => ['16']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'item_id' => $this->item_id,
            'supplier_id' => $this->supplier_id,
            'supplier_price' => $this->supplier_price,
            'pr_id' => $this->pr_id,
            'po_id' => $this->po_id,
            'assign_twg' => $this->assign_twg,
            'item_remarks' => $this->item_remarks,
            'supplier_payment_term' => $this->supplier_payment_term,
            'bid_bond' => $this->bid_bond,
            'resolution_no' => $this->resolution_no,
            'resolution_date' => $this->resolution_date,
            'supplier_price' => $this->supplier_price,

        ]);

        return $dataProvider;
    }
}
