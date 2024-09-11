<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\Supplier;

/**
 * PurchaseRequestSearch represents the model behind the search form about `app\modules\PurchaseRequest\models\PurchaseRequest`.
 */
class SupplierSearch extends Supplier
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tin_no', 'supplier_name', 'supplier_address', 'account_no', 'tel_no', 'owner_name', 'contact_id', 'fax_no', 'classification_philgeps', 'business_type_id', 'action_by', 'action_date' ], 'safe'],
            [['tin_no', 'account_no', 'tel_no'], 'integer'],
            [['supplier_name', 'supplier_address', 'owner_name'], 'string', 'max' => 200],
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

        $query = Supplier::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        
        $query->andFilterWhere([
            'id' => $this->id,
            'tin_no' => $this->tin_no,
            'supplier_name' => $this->supplier_name,
            'supplier_address' => $this->supplier_address,
            'account_no' => $this->account_no,
            'tel_no' => $this->tel_no,
            'owner_name' => $this->owner_name,
            'contact_id' => $this->contact_id,
            'fax_no' => $this->fax_no,
            'classification_philgeps' => $this->classification_philgeps,
            'business_type_id' => $this->business_type_id,
        ]);

        $query->andFilterWhere(['like', 'supplier_name', $this->supplier_name])
            ->andFilterWhere(['like', 'supplier_address', $this->supplier_address]);

        return $dataProvider;
    }
}
