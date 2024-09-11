<?php

namespace app\models\lab;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property string $agency_code
 * @property string $agency_name
 * @property string $agency_address
 * @property string $agency_contact
 * @property string $agency_email
 */
class Agency extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('human_resources');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_code', 'agency_name', 'agency_address', 'agency_contact', 'agency_email'], 'required'],
            [['agency_code'], 'string', 'max' => 50],
            [['agency_name', 'agency_address', 'agency_contact', 'agency_email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_code' => 'Agency Code',
            'agency_name' => 'Agency Name',
            'agency_address' => 'Agency Address',
            'agency_contact' => 'Agency Contact',
            'agency_email' => 'Agency Email',
        ];
    }
}
