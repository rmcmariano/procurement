<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "budget_clustering".
 *
 * @property int $id
 * @property string $expenses_name
 */
class BudgetClustering extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'budget_clustering';
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
            [['expenses_name'], 'required'],
            [['expenses_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expenses_name' => 'Expenses Name',
        ];
    }

    public function getPurchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::className(), ['budget_clustering_id' => 'id']);
    }
}
