<?php

namespace app\modules\PurchaseRequest\models;

use Yii;


/**
 * This is the model class for table "{{%user}}".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $division_id
 * @property int $section_id
 * @property string $code_name
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
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
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at', 'division_id', 'section_id', 'code_name'], 'required'],
            [['status', 'created_at', 'updated_at', 'division_id', 'section_id'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email', 'code_name'], 'string', 'max' => 300],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'division_id' => 'Division ID',
            'section_id' => 'Section ID',
            'code_name' => 'Code Name',
        ];
    }
    
    public function getPurchaseRequest()
    {
        return $this->hasMany(PurchaseRequest::className(), ['requested_by' => 'id']);
    }

    public function getBidding()
    {
        return $this->hasMany(BiddingList::className(), ['assign_twg' => 'id']);
    }

    


    
}
