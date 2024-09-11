<?php

namespace app\models\lab;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\lab\Division;

class DivisionSearch extends Division
{
    public function rules()
    {
        return [
            [['division_name'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Division::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        if($this->division_name){
          $query->andWhere(['division_name' => $this->division_name]);
        }
        $query->orderBy([
            'division_field' => SORT_ASC,
            'division_code' => SORT_ASC,
        ]);

        return $dataProvider;
    }
}
