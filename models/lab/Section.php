<?php

namespace app\models\lab;

use Yii;

class Section extends \yii\db\ActiveRecord
{
  
    public static function tableName()
    {
        return 'section';
    }
  
    public static function getDb()
    {
        return Yii::$app->get('human_resources');
    }
  
    public function rules()
    {
        return [
            [['section_name', 'section_code', 'section_head', 'section_deputy', 'section_contact'], 'required'],
            [['section_name', 'section_code', 'section_head', 'section_deputy', 'section_contact'], 'string', 'max' => 255],
        ];
    }
  
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'section_name' => 'Name',
            'section_code' => 'Code',
            'section_head' => 'Head',
            'section_deputy' => 'Deputy',
            'section_contact' => 'Contact',
        ];
    }
}
