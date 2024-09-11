<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%division}}".
 *
 * @property int $id
 * @property string $division_name
 * @property string $division_code
 *
 * @property PrMain $prMain
 */
class Division extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'division';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('itdidb_hris');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['division_name', 'division_code'], 'required'],
            [['division_name'], 'string', 'max' => 64],
            [['division_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_name' => 'Division Name',
            'division_code' => 'Division Code',
        ];
    }

    /**
     * Gets query for [[PrMain]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseRequest()
    {
        return $this->hasMany(PurchaseRequest::className(), ['division' => 'id']);
    }
}
