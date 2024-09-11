<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\PrItems;
use yii\helpers\ArrayHelper;

/**
 * PrDescripSeacrh represents the model behind the search form about `app\modules\PurchaseRequest\models\PrItems`.
 */
class PrItemSearch extends PrItems
{
    public $purchase;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pr_id', 'stock', 'quantity', 'unit_cost', 'total_cost', 'archived', 'status'], 'safe'],
            [['unit', 'item_name', 'time_stamp', 'purchase'], 'safe'],
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
    public function all($params)
    {
        // $query = PrItems::find()
        //     ->where(['not', ['status' => ['31', '1', '8']]])
        //     ->orderBy(['id' => SORT_DESC]);

        $query = PrItems::find();
        $query->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 30,
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_id', $this->pr_id])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);
            $query->orderBy(['time_stamp' => SORT_DESC]);

        return $dataProvider;
    }

    public function search($params)
    {
        // $query = PrItems::find()
        //     ->where(['not', ['status' => ['31', '1', '8']]])
        //     ->orderBy(['id' => SORT_DESC]);

        $query = PrItems::find();
        $query->where(['not', ['pr_items.status' => ['31', '1', '8', '18', '58']]]);
        $query->joinWith(['purchase']);
        $query->andWhere(['pr_main.created_by' => Yii::$app->user->identity->id]);
        $query->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 30,
            ],
        ]);
        $this->load($params);

        if (!$this->validate()) {

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_id', $this->pr_id])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);
            $query->orderBy(['time_stamp' => SORT_DESC]);

        return $dataProvider;
    }

    public function pending($params)
    {
        $query = PrItems::find();
        $query->where(['pr_items.status' => ['1', '8', '3', '42', '43', '18', '58']]);
        $query->orderBy(['id' => SORT_DESC]);
        $query->joinWith(['purchase']);
        $query->andWhere(['pr_main.created_by' => Yii::$app->user->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_id', $this->pr_id])
            ->andFilterWhere(['like', 'pr_main.temp_no', $this->purchase]);

        return $dataProvider;
    }

    public function archiveIndex($params)
    {
        $query = PrItems::find()->where(['status' => [31, 18]])->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name]);

        return $dataProvider;
    }

    public function procurementIndex($params)
    {

        // $query = PrItems::find()->where(['status' => TrackStatus::PPMS_STATUS])->orderBy(['time_stamp' => SORT_DESC]);

        $query = PrItems::find();
        $query->where(['pr_items.status' => TrackStatus::PPMS_STATUS]);
        $query->orderBy(['time_stamp' => SORT_DESC]);
        $query->joinWith(['purchase']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);

        return $dataProvider;
    }

    public function bacIndex($params)
    {

        // $query = PrItems::find()
        //     ->where(['status' => ['4', '9', '10', '11', '12', '13', '14', '15', '16', '36', '37', '38', '39', '40', '47']])
        //     ->orderBy(['time_stamp' => SORT_DESC]);

        $query = PrItems::find();
        $query->where(['pr_items.status' => ['4', '9', '10', '11', '12', '13', '14', '15', '16', '36', '37', '38', '39', '40', '47', '54', '56', '57', '60', '61']]);
        $query->orderBy(['time_stamp' => SORT_DESC]);
        $query->joinWith(['purchase']);

        // var_dump($query);die;

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);

        return $dataProvider;
    }

    // pending
    public function fmdIndex($params)
    {

        $pr = PurchaseRequest::find()
            ->where(['pr_type_id' => '3'])
            ->all();

        $listData = ArrayHelper::map($pr, 'id', function ($model) {
            return $model['id'];
        });

        $query = PrItems::find();
        $query->where(['pr_id' => $listData]);
        $query->where(['pr_items.status' => ['2']]);
        $query->orderBy(['time_stamp' => SORT_DESC]);
        $query->joinWith(['purchase']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            // 'pagination' => [
            //     'pagesize' => 20,
            // ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);

        return $dataProvider;
    }

    // approved
    public function fmdApprovedIndex($params)
    {
        $pr = PurchaseRequest::find()
            ->where(['pr_type_id' => '3'])
            ->all();

        $listData = ArrayHelper::map($pr, 'id', function ($model) {
            return $model['id'];
        });

        $query = PrItems::find();
        $query->where(['pr_id' => $listData]);
        $query->where(['pr_items.status' => ['32']]);
        $query->orderBy(['time_stamp' => SORT_DESC]);
        $query->joinWith(['purchase']);

        // $query = PrItems::find()
        //     ->where(['pr_id' => $listData])
        //     ->andWhere(['status' => ['32']])
        //     ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);

        return $dataProvider;
    }

    public function accountingIndex($params)
    {
        $pr = PurchaseRequest::find()
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['7']]
            ])
            ->orWhere([
                'AND',
                ['pr_type_id' => 2],
                ['status' => ['2']]
            ])->all();

        $listData = ArrayHelper::map($pr, 'id', function ($model) {
            return $model['id'];
        });

        $query = PrItems::find();
        $query->where(['pr_id' => $listData]);
        $query->where(['pr_items.status' => ['2', '22', '5']]);
        $query->orderBy(['time_stamp' => SORT_DESC]);
        $query->joinWith(['purchase']);

        // $query = PrItems::find()
        //     ->where(['pr_id' => $listData])
        //     ->andWhere(['status' => ['2', '22', '5']])
        //     ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_main.pr_no', $this->purchase]);

        return $dataProvider;
    }

    public function chiefIndex($params)
    {
        $query = PrItems::find();
        $query->where(['pr_items.status' => ['1', '41']]);
        $query->orderBy(['time_stamp' => SORT_DESC]);
        $query->joinWith(['purchase']);
        $query->andWhere(['pr_main.approved_by' => Yii::$app->user->identity->id]);

        // $query = PrItems::find()
        //     ->where(['status' => [1, 41]])
        //     ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name])
            ->andFilterWhere(['like', 'pr_main.temp_no', $this->purchase]);

        return $dataProvider;
    }

    public function sdoIndex($params)
    {
        $pr = PurchaseRequest::find()
            ->andWhere([
                'AND',
                ['pr_type_id' => 1],
                ['status' => ['2']]
            ])->all();

        $listData = ArrayHelper::map($pr, 'id', function ($model) {
            return $model['id'];
        });

        $query = PrItems::find()
            ->where(['pr_id' => $listData])
            ->andWhere(['status' => ['2']])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name]);

        return $dataProvider;
    }

    public function acceptedBidbulletin($params)
    {
        $bidbulletinAccpt = ItemSpecification::find()
            ->where(['bidbulletin_status' => ['2', '5']])
            ->asArray()->all();

        $listData = ArrayHelper::map($bidbulletinAccpt, 'item_id', function ($model) {
            return $model['item_id'];
        });

        $query = PrItems::find()
            ->where(['id' => $listData])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name]);

        return $dataProvider;
    }

    public function resolutionList($params)
    {
        $bidbulletinAccpt = ItemSpecification::find()
            // ->where(['status' => ['2', '5']])
            ->asArray()->all();

        $listData = ArrayHelper::map($bidbulletinAccpt, 'item_id', function ($model) {
            return $model['item_id'];
        });

        $query = PrItems::find()
            ->where(['id' => $listData])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 20,
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
            'pr_id' => $this->pr_id,
            'stock' => $this->stock,
            'quantity' => $this->quantity,
            'item_name' => $this->item_name,
            'unit_cost' => $this->unit_cost,
            'total_cost' => $this->total_cost,
            'time_stamp' => $this->time_stamp,
            'archived' => $this->archived,
        ]);

        $query->andFilterWhere(['like', 'unit', $this->unit])
            ->andFilterWhere(['like', 'item_name', $this->item_name]);

        return $dataProvider;
    }

    // public function topManagementSearch($params)
    // {
    //     $query = PrItems::find()
    //         ->orderBy(['time_stamp' => SORT_DESC]);

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //     ]);

    //     $this->load($params);

    //     if (!$this->validate()) {
    //         // uncomment the following line if you do not want to return any records when validation fails
    //         // $query->where('0=1');
    //         return $dataProvider;
    //     }

    //     $query->andFilterWhere([
    //         'id' => $this->id,
    //         'pr_id' => $this->pr_id,
    //         'stock' => $this->stock,
    //         'quantity' => $this->quantity,
    //         'item_name' => $this->item_name,
    //         'unit_cost' => $this->unit_cost,
    //         'total_cost' => $this->total_cost,
    //         'time_stamp' => $this->time_stamp,
    //         'archived' => $this->archived,
    //     ]);

    //     $query->andFilterWhere(['like', 'unit', $this->unit])
    //         ->andFilterWhere(['like', 'item_name', $this->item_name]);

    //     return $dataProvider;
    // }
}
