<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\WorkOrderItems;
use yii\helpers\ArrayHelper;

/**
 * PurchaseOrderItemsSearch represents the model behind the search form about `app\modules\PurchaseRequest\models\PurchaseOrderItems`.
 */
class WorkOrderItemsSearch extends WorkOrderItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'wo_id', 'item_id', 'deduction_id', 'deduction_amount'], 'safe'],
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
        $query = WorkOrderItems::find();
        
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
            'id' => $this->id,
            'wo_id' => $this->wo_id,
            'item_id' => $this->item_id,
            'deduction_id' => $this->deduction_id,
            'deduction_amount' => $this->deduction_amount
        ]);

        return $dataProvider;
    }
}
