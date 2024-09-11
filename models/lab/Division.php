<?php

namespace app\models\lab;

use Yii;

/**
 * This is the model class for table "division".
 *
 * @property int $id
 * @property int $agency_id
 * @property string $division_code
 * @property string $division_name
 * @property string $division_address
 * @property string $division_contact
 * @property string $division_email
 * @property string $division_head
 * @property int $division_field
 * @property int $division_status
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
        return Yii::$app->get('human_resources');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agency_id', 'division_code', 'division_name', 'division_address', 'division_contact', 'division_email', 'division_head', 'division_field', 'division_status'], 'required'],
            [['agency_id', 'division_field', 'division_status'], 'integer'],
            [['division_code'], 'string', 'max' => 50],
            [['division_name', 'division_address', 'division_contact', 'division_email', 'division_head'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'agency_id' => 'Agency ID',
            'division_code' => 'Division Code',
            'division_name' => 'Division Name',
            'division_address' => 'Division Address',
            'division_contact' => 'Division Contact',
            'division_email' => 'Division Email',
            'division_head' => 'Division Head',
            'division_field' => 'Division Field',
            'division_status' => 'Division Status',
        ];
    }
}
