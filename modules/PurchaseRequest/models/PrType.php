<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%pr_type}}".
 *
 * @property int $id
 * @property string $type_name
 *
 * @property PrMain[] $prMains
 */
class PrType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%pr_type}}';
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
            [['type_name'], 'safe'],
            [['type_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_name' => 'Type',
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::className(), ['pr_type_id' => 'id']);
    }
}
