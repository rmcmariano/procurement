<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "profile".
 *
 * @property int $id
 * @property int $user_id
 * @property string $lname
 * @property string $fname
 * @property string $mi
 * @property string|null $extname
 * @property string|null $gender
 * @property string $address
 * @property int $contact
 */
class Profile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'profile';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('userDb');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'lname', 'fname', 'mi', 'address', 'contact'], 'required'],
            [['user_id', 'contact'], 'integer'],
            [['lname', 'fname', 'mi', 'extname', 'gender', 'address'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'lname' => 'Lname',
            'fname' => 'Fname',
            'mi' => 'Mi',
            'extname' => 'Extname',
            'gender' => 'Gender',
            'address' => 'Address',
            'contact' => 'Contact',
        ];
    }

    public function getFullName()
    {
        return $this->fname . ' ' . $this->lname;
    }

    public function getFirstName()
    {
        return ucwords(strtolower($this->fname));
    }

    public function getLastName()
    {
        return ucwords(strtolower($this->lname));
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
