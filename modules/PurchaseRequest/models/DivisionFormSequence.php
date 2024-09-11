<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "division_form_sequence".
 *
 * @property int $id
 * @property string $division_code
 * @property int $form_sequence
 */
class DivisionFormSequence extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'division_form_sequence';
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
            [['division_code', 'form_sequence'], 'required'],
            [['form_sequence'], 'integer'],
            [['division_code'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'division_code' => 'Division Code',
            'form_sequence' => 'Form Sequence',
        ];
    }
}
