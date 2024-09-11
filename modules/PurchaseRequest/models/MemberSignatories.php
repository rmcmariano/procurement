<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "bac_signatories".
 *
 * @property int $id
 * @property int $bid_id
 * @property int $member_id
 * @property string $chairperson_id
 * @property int $co_chairperson_id
 */
class MemberSignatories extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bac_signatories_members';
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
            [['bac_signatories_id', 'members_id'], 'safe'],
            [['bac_signatories_id', 'members_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bac_signatories_id' => 'BAC Signatories ID',
            'members_id' => 'Members',
        ];
    }
}
