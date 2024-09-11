<?php

namespace app\models\lab;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\lab\Section;

class SectionSearch extends Section
{
    public function rules()
    {
        return [
            [['section_name'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params)
    {
        $query = Section::find();

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andWhere(['division_id' => $_GET['id']]);
        if($this->section_name){
          $query->andWhere(['section_name' => $this->section_name]);
        }
          
        $query->orderBy([
            'id' => SORT_ASC,
        ]);

        return $dataProvider;
    }
}
