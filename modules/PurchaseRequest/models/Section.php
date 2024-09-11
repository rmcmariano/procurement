<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "{{%section}}".
 *
 * @property int $id
 * @property string $section_name
 * @property string $section_code
 * @property int $division_id
 *
 * @property PrMain[] $prMains
 */
class Section extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'section';
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
            [['section_name', 'section_code', 'division_id'], 'required'],
            [['division_id'], 'integer'],
            [['section_name', 'section_code'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_name' => 'Section Name',
            'section_code' => 'Section Code',
            'division_id' => 'Division ID',
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseRequest()
    {
        return $this->hasOne(PurchaseRequest::className(), ['section' => 'id']);
    }

    public static function getSection($div_id)
    {
        $section = self::find()
        ->select(['id', 'section_name as name'])
        ->where(['division_id'=>$div_id])
        ->asArray()
        ->all();

        return $section;
    }
}
