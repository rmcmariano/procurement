<?php

namespace app\modules\document\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\document\models\Document;

class DocumentSearch extends Document
{
    public $pr_no;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_time', 'pr_id', 'type_id', 'status', 'received_by',  'released_by', 'pr_no'], 'safe'],
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
    public function allreceived($params)
    {

        $query = Document::find();


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_time_received' => SORT_DESC, // Replace 'your_attribute_name' with the attribute you want to sort by
                ],
            ],
        ]);

        $query->andFilterWhere([
            'pr_id' => $this->pr_id,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'received_by' => $this->received_by,
            'released_by' => $this->released_by,

        ]);

        $query->andFilterWhere(['like', 'date_time_received', $this->date_time_received])
            ->andFilterWhere(['like', 'date_time_released', $this->date_time_released]);

        return $dataProvider;
    }
    
     public function received($params)
    {

        $query = Document::find()->where(['pr_id' => $_GET['id']]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_time_received' => SORT_DESC, // Replace 'your_attribute_name' with the attribute you want to sort by
                ],
            ],
        ]);

        $query->andFilterWhere([
            'pr_id' => $this->pr_id,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'received_by' => $this->received_by,
            'released_by' => $this->released_by,

        ]);

        $query->andFilterWhere(['like', 'date_time_received', $this->date_time_received])
            ->andFilterWhere(['like', 'date_time_released', $this->date_time_released]);

        return $dataProvider;
    }

    public function allrelease($params)
    {
        
        $query = Document::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_time_received' => SORT_DESC, // Replace 'your_attribute_name' with the attribute you want to sort by
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pr_id' => $this->pr_id,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'received_by' => $this->received_by,
            'released_by' => $this->released_by,

        ]);

        $query->andFilterWhere(['like', 'date_time_received', $this->date_time_received])
            ->andFilterWhere(['like', 'date_time_released', $this->date_time_released]);

        return $dataProvider;
    }

    public function forrelease($params)
    {

        $query = Document::find()->where(['status' => 1]);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_time_received' => SORT_DESC, // Replace 'your_attribute_name' with the attribute you want to sort by
                ],
            ],
        ]);

        $query->andFilterWhere([
            'pr_id' => $this->pr_id,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'received_by' => $this->received_by,
            'released_by' => $this->released_by,

        ]);

        $query->andFilterWhere(['like', 'date_time_received', $this->date_time_received])
            ->andFilterWhere(['like', 'date_time_released', $this->date_time_released]);

        return $dataProvider;
    }

    public function released($params)
    {

        $query = Document::find()->where(['status' => 2]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'date_time_released' => SORT_DESC, // Replace 'your_attribute_name' with the attribute you want to sort by
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'pr_id' => $this->pr_id,
            'type_id' => $this->type_id,
            'status' => $this->status,
            'received_by' => $this->received_by,
            'released_by' => $this->released_by,

        ]);

        $query->andFilterWhere(['like', 'date_time_received', $this->date_time_received])
            ->andFilterWhere(['like', 'date_time_released', $this->date_time_released]);

        return $dataProvider;
    }
    
}
