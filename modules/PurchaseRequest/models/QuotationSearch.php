<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * BiddingListSearch represents the model behind the search form about `app\modules\PurchaseRequest\models\PurchaseOrderItems`.
 */
class QuotationSearch extends Quotation
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pr_id', 'quotation_no', 'option_date', 'remarks', 'date_posted_philgeps', 'reference_no', 'status', 'date', 'time'], 'safe'],
            [['pr_id', 'option_id', 'reference_no'], 'safe'],
            [['option_date'], 'safe'],
            [['remarks'], 'string'],
            [['time_stamp'], 'safe']
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
        $query = Quotation::find()
            ->orderBy(['time_stamp' => SORT_DESC]);

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
            'pr_id' => $this->pr_id,
            'quotation_no' =>  $this->quotation_no,
            'option_id' =>  $this->option_id,
            'option_date' =>  $this->option_date,
            'remarks' =>  $this->remarks,
            'reference_no' =>  $this->reference_no,
            'status' =>  $this->status
        ]);

        return $dataProvider;
    }
}
