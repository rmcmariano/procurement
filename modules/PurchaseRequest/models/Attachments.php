<?php

namespace app\modules\PurchaseRequest\models;

use Yii;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "attachments".
 *
 * @property int $id
 * @property int $pr_id
 * @property string $file_directory
 * @property string $file_name
 * @property string $time_stamp
 */
class Attachments extends \yii\db\ActiveRecord
{
    public $files;

    public static function tableName()
    {
        return 'attachments';
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
            [['pr_id', 'file_directory', 'file_name'], 'safe'],
            [['pr_id', 'archived'], 'integer'],
            [['time_stamp'], 'safe'],
            // [['files'], 'file',  'skipOnEmpty' => false, 'maxFiles' => 5],
            // [['file_directory', 'file_name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'pr_id' => 'Pr ID',
            'file_directory' => 'File Directory',
            'file_name' => 'File Name',
            'file_extension' => 'File Extension',
            'time_stamp' => 'Date',
            'archived' => ''
        ];
    }

    public function getPurchaserequest()
    {
        return $this->hasOne(PurchaseRequest::className(), ['id' => 'pr_id']);
    }

    public function updateImage($isUpdate = false, $subfolder = null)
    {
        $fileUpload = new Attachments();
        if ($this->validate()) {

            if ($isUpdate)

                $hashedName = md5($this->img->name . date("m/d/y G:i:s:u")) . "." . $this->img->extension;
                $fpath = Yii::$app->params["base_upload_folder"] . "/" . $hashedName;
            // $fpath = Yii::getAlias('@uploads') . "/" . $hashedName;

            if (isset($subfolder))
                $fpath = Yii::$app->params["base_upload_folder"] . "/{$subfolder}/" . $hashedName;
            // $fpath = Yii::getAlias('@uploads') . "/{$subfolder}/" . $hashedName;
                $fileUpload->profile_id = $this['profile_id'];
                $fileUpload->file_name = $this->img->name;
                $fileUpload->generated_file_name = $hashedName;
                $fileUpload->file_path = $fpath;
                $fileUpload->type = $this->img->type;
                $fileUpload->size = $this->img->size;
                $fileUpload->extension = $this->img->extension;
                
            if (!is_dir(dirname($fileUpload->file_path)))
                FileHelper::createDirectory(dirname($fileUpload->file_path));

            if ($fileUpload->validate() && $fileUpload->save()) {
                $this->upload_file = (string)$fileUpload->id;
                $this->img->saveAs($fileUpload->file_path);
                // var_dump($this->upload_file);die;
            }
        }
    }
}
