<?php

namespace app\modules\document\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\document\models\PurchaseRequest;
use yii\helpers\ArrayHelper;

/**
 * PurchaseRequestSearch represents the model behind the search form about `app\modules\PurchaseRequest\models\PurchaseRequest`.
 */
class PurchaseRequestSearch extends PurchaseRequest
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pr_no'], 'safe'],

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

        $query = PurchaseRequest::find()->where(['not in', 'status', [1, 8, 6, 31, 41]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC, // Replace 'your_attribute_name' with the attribute you want to sort by
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'pr_no', $this->pr_no]);

        return $dataProvider;
    }
}
