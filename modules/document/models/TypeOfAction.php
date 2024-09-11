<?php

namespace app\modules\document\models;

use Yii;

class TypeOfAction extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'type_of_action';
    }

    public static function getDb()
    {
        return Yii::$app->get('document');
    }

    public function rules()
    {
        return [
            [['code', 'name',], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => 'Code',
            'name' => 'Name',
        ];
    }
}
