<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\data\ActiveDataProvider;

class DateOptions extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%quotation_options}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('itdidb_procurement_system');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['options'], 'safe'],
            [['options'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'options' => 'Options',
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuotation()
    {
        return $this->hasMany(Quotation::className(), ['option_id' => 'id']);
    }

    
    public function searchOption($params)
    {
        $query = DateOptions::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!$this->load($params) && $this->validate()) {
            return $dataProvider;
        }

        // Add filters to the query
        $query->andFilterWhere(['id' => $this->id]);
        $query->andFilterWhere(['like', 'options', $this->name]);
        // $query->andFilterWhere(['like', 'email', $this->email]);
        // Add filters for other fields as needed

        return $dataProvider;
    }
}
