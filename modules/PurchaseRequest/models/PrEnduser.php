<?php

namespace app\modules\PurchaseRequest\models;

use app\modules\user\models\Profile;
use Yii;

/**
 * This is the model class for table "{{%pr_enduser}}".
 *
 * @property int $id
 * @property int $pr_id
 * @property int $user_id
 */
class PrEnduser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%pr_enduser}}';
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
            [['pr_id', 'user_id'], 'safe'],

        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_id' => 'PR ID',
            'user_id' => 'End User'
        ];
    }

    /**
     * Gets query for [[PrMains]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPurchaseRequests()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    }

    public function getProfileuser()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'user_id']);
    }

    public function getEnduser()
    {
        $model = $this->user_id;
        $array = explode(",", $model);
        foreach ($array as $x => $val) {
           $user = profile::find()->where(['user_id' => $val])->one();
           echo $user->fullname ," , ";
        }
    }
}
