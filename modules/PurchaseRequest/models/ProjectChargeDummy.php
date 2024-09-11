<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%project_charge}}".
 *
 * @property int $id
 * @property string $type_name
 *
 * @property PrMain[] $prMains
 */
class ProjectChargeDummy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%project_charge}}';
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
            [['project_code', 'project_name'], 'safe'],
            [['project_code', 'project_name'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'project_id' => 'ID',
            'project_code' => 'Code',
            'project_name' =>'Name',
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
   
    public function getPurchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::className(), ['charge_to' => 'project_id']);
    }
}
