<?php

namespace app\modules\PurchaseRequest\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\PurchaseRequest\models\ConformeAttachments;
use yii\helpers\ArrayHelper;

/**
 * PurchaseOrderSearch represents the model behind the search form of `app\modules\PurchaseRequest\models\PurchaseOrder`.
 */
class ConformeAttachmentsSearch extends ConformeAttachments
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['po_id', 'file_directory', 'file_name', 'file_extension', 'remarks'], 'safe'],
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
    public function search($params, $id)
    {
        $po = PurchaseOrder::find()->where(['pr_id' => $id])->asArray()->all();

        $listData = ArrayHelper::map($po, 'id', function ($model) {
            return $model['id'];
        });

        $query = ConformeAttachments::find()->where(['po_id' => $listData]);
        // var_dump($po);die;
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
            'file_directory' => $this->file_directory,
            'file_name' => $this->file_name,
            'file_extension' => $this->file_extension,
            'remarks' => $this->remarks,
            'time_stamp' => $this->time_stamp,
        ]);

        $query->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'file_extension', $this->file_extension]);

        return $dataProvider;
    }

    public function conformeLists($params)
    {
        // $po = PurchaseOrder::find()->where(['pr_id' => $id])->asArray()->all();

        // $listData = ArrayHelper::map($po, 'id', function ($model) {
        //     return $model['id'];
        // });

        // $query = ConformeAttachments::find()->where(['po_id' => $listData]);
        $query = ConformeAttachments::find();
        // var_dump($po);die;
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
            'file_directory' => $this->file_directory,
            'file_name' => $this->file_name,
            'file_extension' => $this->file_extension,
            'remarks' => $this->remarks,
            'time_stamp' => $this->time_stamp,
        ]);

        $query->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'file_extension', $this->file_extension]);

        return $dataProvider;
    }
}
