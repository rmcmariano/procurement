<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\ItemSpecification;

/**
 * PrDescripSeacrh represents the model behind the search form about `app\modules\PurchaseRequest\models\PrItems`.
 */
class ItemSpecificationSearch extends ItemSpecification
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_id', 'description', 'quantity', 'property_no', 'evaluation_status', 'bidbulletin_changes', 'request_changes', 'bidbulletin_id', 'bidbulletin_status', 'bidbulletin_remarks', 'time_stamp'], 'safe'],
            [['description', 'bidbulletin_changes', 'bidbulletin_remarks'], 'string'],
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
        $query = ItemSpecification::find(); 
        // var_dump($params);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'item_id' => $this->item_id,
            'description' => $this->description,
            'quantity' => $this->quantity,
            'bidbulletin_id' => $this->bidbulletin_id
         
        ]);

        $query->andFilterWhere(['like', 'item_id', $this->item_id])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
 
    public function acceptedBidbulletin($params)
    {
        $query = ItemSpecification::find()->where(['bidbulletin_status' => ['2', '5']]); 
        // var_dump($params);die;
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'item_id' => $this->item_id,
            'description' => $this->description,
            'quantity' => $this->quantity,
        
        ]);

        $query->andFilterWhere(['like', 'item_id', $this->item_id])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }
 
    

  

}
