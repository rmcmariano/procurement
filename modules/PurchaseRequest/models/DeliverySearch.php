<?php

namespace app\modules\PurchaseRequest\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\Delivery;

class DeliverySearch extends Delivery
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['po_id', 'actual_date_delivery', 'type_delivery', 'remarks', 'type_delivery', 'delivery_receipt_no', 'delivery_amount'], 'safe'],
            [['time_stamp'], 'safe'],
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

        $query = Delivery::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'po_id' => $this->po_id,
            'actual_date_delivery' => $this->actual_date_delivery,
            'type_delivery' => $this->type_delivery,
            'remarks' => $this->remarks,
            'time_stamp' => $this->time_stamp,
            'delivery_receipt_no' => $this->delivery_receipt_no,
            'delivery_amount' => $this->delivery_amount
        ]);

        $query->andFilterWhere(['like', 'actual_date_delivery', $this->actual_date_delivery])
            ->andFilterWhere(['like', 'type_delivery', $this->type_delivery]);

        return $dataProvider;
    }
}
