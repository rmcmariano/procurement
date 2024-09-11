<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "bid_forms_details".
 *
 * @property int $id
 * @property string $forms_titlename
 * @property int $pr_id
 * @property int $bac_signatories
 */
class BidFormDetails extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bid_forms_details';
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
            [['forms_titlename', 'pr_id', 'bac_signatories'], 'required'],
            [['pr_id', 'bac_signatories'], 'integer'],
            [['forms_titlename'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'forms_titlename' => 'Forms Titlename',
            'pr_id' => 'Pr ID',
            'bac_signatories' => 'Bac Signatories',
        ];
    }
}
