<?php

namespace app\modules\PurchaseRequest\models;

use Yii;

/**
 * This is the model class for table "conforme_attachments".
 *
 * @property int $id
 * @property int $po_id
 * @property string $file_directory
 * @property string $file_name
 * @property string $file_extension
 * @property string $remarks
 * @property string $time_stamp
 */
class ConformeAttachments extends \yii\db\ActiveRecord
{
    public $files;

    public static function tableName()
    {
        return 'conforme_attachments';
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
            [['po_id', 'file_directory', 'file_name', 'file_extension', 'remarks'], 'safe'],
            // [['po_id'], 'integer'],
            [['time_stamp'], 'safe'],
            // [['file_name'], 'file',   'skipOnEmpty' => false, 'extensions' => 'pdf'],
            [['file_extension'], 'string', 'max' => 20],
            [['file_directory', 'file_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'po_id' => 'Po ID',
            'file_directory' => 'File Directory',
            'file_name' => 'File Name',
            'file_extension' => 'File Extension',
            'remarks' => 'Remarks',
            'time_stamp' => 'Time Stamp',
        ];
    }

    public function getPurchaseorder()
    {
        return $this->hasOne(PurchaseOrder::className(), ['id' => 'po_id']);
    }

    public function getWorkorder()
    {
        return $this->hasOne(WorkOrder::className(), ['id' => 'wo_id']);
    }

}
