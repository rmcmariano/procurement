<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%division}}".
 *
 * @property int $id
 * @property string $position_title
 * @property string $position_code
 *
 * @property PrMain $prMain
 */
class Position extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'position';
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
            [['position_title', 'position_code'], 'required'],
            [['position_title'], 'string', 'max' => 64],
            [['position_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'position_title' => 'Position Title',
            'position_code' => 'Position Code',
        ];
    }

    /**
     * Gets query for [[PrMain]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseRequest()
    {
        return $this->hasMany(PurchaseRequest::className(), ['requestedby_position' => 'id']);
    }
}
