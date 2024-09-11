<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%mode_of_pr}}".
 *
 * @property int $mode_id
 * @property string $mode_name
 *
 * @property PrMain[] $prMains
 */
class ProcurementMode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%mode_of_pr}}';
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
            [['mode_name'], 'safe'],
            [['mode_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'mode_id' => 'ID',
            'mode_name' => 'Type',
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseRequests()
    {
        return $this->hasOne(PurchaseRequest::className(), ['mode_pr_id' => 'mode_id']);
    }
}
