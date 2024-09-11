<?php

namespace app\modules\PurchaseRequest\models;

use app\modules\user\models\Profile;
use Yii;

/**
 * This is the model class for table "pbw_lib_ps".
 *
 * @property int $id
 * @property string $employment_status
 * @property string $positions
 * @property int|null $rate
 * @property int $nom
 * @property int $row_total
 * @property int $employee
 * @property string $expertise
 * @property int $participation
 * @property int $ps_id
 * @property string $ps_sof
 * @property int $info_id
 */
class PbwLibPs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pbw_lib_ps';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('itdidb_pmis');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['employment_status', 'positions', 'row_total', 'employee', 'expertise', 'participation', 'ps_id', 'info_id'], 'required'],
            [['rate', 'nom', 'row_total', 'employee', 'participation', 'ps_id', 'info_id'], 'integer'],
            [['employment_status', 'positions', 'expertise', 'ps_sof'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'employment_status' => 'Employment Status',
            'positions' => 'Positions',
            'rate' => 'Rate',
            'nom' => 'Nom',
            'row_total' => 'Row Total',
            'employee' => 'Employee',
            'expertise' => 'Expertise',
            'participation' => 'Participation',
            'ps_id' => 'Ps ID',
            'ps_sof' => 'Ps Sof',
            'info_id' => 'Info ID',
        ];
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['id' => 'employee']);
    }

    public static function getPsId($charge_to)
    {
        $requested_by = self::find()
        ->select(['id', 'employee as name'])
        ->where(['ps_id'=>$charge_to])
        ->asArray()
        ->all();

        return $requested_by;
        // var_dump($requested_by);die;
    }

    public static function getPsIdupdate($charge_to)
    {
        $requested_by = self::find()
            ->select(['id'])
            ->where(['ps_id' => $charge_to])
            ->all();

        $result = [];
        foreach ($requested_by as $model) {

            $test = self::find()->where(['id' => $model['id']])->all();
            foreach ($test as $self) {
                $testing = Profile::find()->where(['id' => $self['employee']])->all();

                foreach ($testing as $name) {

                    $result[] = [
                        'id' => $model->id,
                        'name' => $name->lname . ', ' . $name->fname, // Use getFullName method here
                    ];
                }
            }
        }

        return $result;
    }

    public function getFullName(){
        return ucwords(strtolower($this->profile->lname)) . ', ' . ucwords(strtolower($this->profile->fname));
    }

   
}
