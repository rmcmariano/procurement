<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\PurchaseOrderItems;
use yii\helpers\ArrayHelper;

/**
 * PurchaseOrderItemsSearch represents the model behind the search form about `app\modules\PurchaseRequest\models\PurchaseOrderItems`.
 */
class PurchaseOrderItemsSearch extends PurchaseOrderItems
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'po_id', 'bid_id'], 'integer'],
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
        $query = PurchaseOrderItems::find();
       

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
            'po_id' => $this->po_id,
            'bid_id' => $this->bid_id,
        ]);

        return $dataProvider;
    }
}
