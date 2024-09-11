<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "deduction_lists".
 *
 * @property int $id
 * @property string $code
 */
class AddservicesLists extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'add_services_list';
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
            [['service_name'], 'safe'],
            [['service_name'], 'string', 'max' => 60],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'service_name' => 'Code',
        ];
    }

    public function getAdditionalServices()
    {
        return $this->hasMany(AdditionalServices::className(), ['add_service_id' => 'id']);
    }

}
