<?php

namespace app\models\profile;

use Yii;
use app\models\User;

class Profile extends \yii\db\ActiveRecord
{
  
    public static function tableName()
    {
        return 'profile';
    }
  
    public static function getDb()
    {
        return Yii::$app->get('userDb');
    }
  
    public function rules()
    {
        return [
            [['fname', 'lname', 'mi', 'address', 'contact', 'section_id', 'code_name'], 'required'],
            [['section_id'], 'integer'],
            [['fname', 'lname', 'mi', 'address', 'contact'], 'string', 'max' => 255],
        ];
    }
  
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'fname' => 'First Name',
            'lname' => 'Last Name',
            'mi' => 'MI',
            'address' => 'Address',
            'contact' => 'Contact',
            'section_id' => 'Section',
            'code_name' => 'Code Name',
        ];
    }

    public function getFullName()
    {
        return $this->fname . ' ' . $this->mi . ' ' . $this->lname;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
    public function getFirstName()
    {
        return ucwords(strtolower($this->fname));
    }

    public function getLastName()
    {
        return ucwords(strtolower($this->lname));
    }
}
