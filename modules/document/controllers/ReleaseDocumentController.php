<?php

namespace app\modules\document_tracking\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

use mdm\admin\models\User;
use app\models\profile\Profile;
use app\models\lab\Section;

use app\modules\document_tracking\models\DocumentDetails;
use app\modules\document_tracking\models\DocumentResponse;
use app\modules\document_tracking\models\ResponseTable;
use app\modules\document_tracking\models\FileUpload;

class ReleaseDocumentController extends Controller
{
    public function actionIndex(){

        if(!isset($_GET['type']) || $_GET['type'] == NULL){

            return $this->redirect('error');
        }

        if ($_GET['type'] == 'IC'){

            $internal_communication = DocumentResponse::find()
                                        ->select('document_response.tracking_number, document_response.id, document_response.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 1],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 1],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->distinct()
                                        ->all();
            $dataProvider = new ArrayDataProvider([
                'allModels' => $internal_communication,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }

        if ($_GET['type'] == 'I'){

            $incoming = DocumentResponse::find()
                                        ->select('document_response.tracking_number, document_response.id, document_response.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 2],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 2],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->distinct()
                                        ->all();
            $dataProvider = new ArrayDataProvider([
                'allModels' => $incoming,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }

        if ($_GET['type'] == 'O'){

            $outgoing = DocumentResponse::find()
                                        ->select('document_response.tracking_number, document_response.id, document_response.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 3],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 3],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->distinct()
                                        ->all();

            $dataProvider = new ArrayDataProvider([
                'allModels' => $outgoing,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView(){

        $send = new DocumentResponse;

        $files = new FileUpload();

        $link = $_GET['type'];

        //Document Details Checking
        $a = DocumentResponse::find()
                ->where(['id' => $_GET['id']])
                ->one();

        $view = DocumentResponse::find()
        ->where(['id' => $_GET['id']])
        ->one();

        $view->view = '1';

        $view->save();

        if ($a == NULL) {
            return $this->redirect(['error']);
        }

        $document_details = DocumentDetails::find()
                        ->where(['tracking_number' => $a['tracking_number']])
                        ->one();

        if ($document_details == NULL) {
            return $this->redirect(['error']);
        }
        // -->

        //<--Profile of User
        $profile = Profile::find()
                        ->where(['user_id' => $document_details->created_by])
                        ->one();

        $full = $profile['fname'] . " " . $profile['mi'] . " " . $profile['lname'];
        //-->

        //<--Dropdown Query
        $section = Section::find()->all();
        $listData = ArrayHelper::map($section, 'id' , function($model) {
            $exp = explode('-', $model['section_code']);

            return $exp[0] . '-' . $model['section_name'];
        });
        $all = ['All' => 'All'];
        $group = ArrayHelper::merge($all, $listData);

        $users = Profile::find()
                    ->where(['not', ['user_id' => Yii::$app->user->identity->id]])
                    ->andWhere(['not', ['lname' => 'admin']])
                    ->all();
        $individual = ArrayHelper::map($users, 'user_id' , function($model) {
                return $model['fname'] . ' ' . $model['mi'] . ' ' . $model['lname'];
        });
        
        $pending = ArrayHelper::map($users, 'id' , function($model) {
                return $model['user_id'];
        });
        //-->

        $check = DocumentResponse::find()
                    ->where(['action_by' => Yii::$app->user->identity->id])
                    ->andWhere(['id' => $_GET['id']])
                    ->andWhere(['status' => 0])
                    ->andWhere(['not', ['action' => 'RELEASED']])
                    ->andWhere(['not', ['action' => 'REDIRECT']])
                    ->one();
        //<--On Submit
        if ($send->load(Yii::$app->request->post()))
        {
            if ($send->section == '' && $send->individual == ''){
                return $this->redirect(['blank']);
            }

            else {

                $send->tracking_number = $check->tracking_number;
                $send->action_by = Yii::$app->user->identity->id;
                $send->action = 'RELEASED';
                $check->status = 1;
                $send->save();
                $check->save();

                $group = $send->section;
                $solo = $send->individual;

                //  Multiple Group Selection
                if ($solo == '' && $group[0] != '' && $group[0] != 'All'){

                    $section_under = User::find()
                                        ->where(['not', ['id' => Yii::$app->user->identity->id]])
                                        ->andWhere(['not', ['code_name' => 'ADMIN']])
                                        ->andWhere(['section_id' => $group])
                                        ->all();
                    $sent_logs = ArrayHelper::map($section_under, 'id' , function($model) {
                                    return $model['id'];
                    });

                    foreach ($sent_logs as $sent_log) {

                        $response_log = new ResponseTable;

                        $response_log->tracking_number = $check->tracking_number;
                        $response_log->user_id = Yii::$app->user->identity->id;
                        $response_log->recipient_id = $sent_log;
                        $response_log->release_id = $send->id;
                        $response_log->save();
                    }
                    
                }

                //  All Selection
                elseif ($solo == '' && $group[0] == 'All'){

                    $itdi_under = User::find()
                                    ->where(['not', ['id' => Yii::$app->user->identity->id]])
                                    ->andWhere(['not', ['code_name' => 'ADMIN']])
                                    ->all();

                    $sent_logs = ArrayHelper::map($itdi_under, 'id' , function($model) {
                                    return $model['id'];
                    });

                    foreach ($sent_logs as $sent_log) {

                        $response_log = new ResponseTable;

                        $response_log->tracking_number = $check->tracking_number;
                        $response_log->user_id = Yii::$app->user->identity->id;
                        $response_log->recipient_id = $sent_log;
                        $response_log->release_id = $send->id;
                        $response_log->save();
                    }

                }

                // Multiple Individual Selection
                elseif ($solo != '' && $group == '' && $group != 'All'){

                    foreach ($solo as $sent_log) {

                        $response_log = new ResponseTable;

                        $response_log->tracking_number = $check->tracking_number;
                        $response_log->user_id = Yii::$app->user->identity->id;
                        $response_log->recipient_id = $sent_log;
                        $response_log->release_id = $send->id;
                        $response_log->save();
                    }

                }

                // Both Individual and Group Selection
                elseif ($solo != '' && $group[0] != '' && $group[0] != 'All'){

                    $section_under = User::find()
                                        ->where(['not', ['id' => [Yii::$app->user->identity->id, $solo]]])
                                        ->andWhere(['not', ['code_name' => 'ADMIN']])
                                        ->andWhere(['section_id' => $group])
                                        ->all();
                    $sent_logs = ArrayHelper::map($section_under, 'id' , function($model) {
                                    return $model['id'];
                    });

                    

                    $mixed_logs = array_unique(ArrayHelper::merge($sent_logs, $solo));

                    foreach ($mixed_logs as $sent_log) {

                        $response_log = new ResponseTable;

                        $response_log->tracking_number = $check->tracking_number;
                        $response_log->user_id = Yii::$app->user->identity->id;
                        $response_log->recipient_id = $sent_log;
                        $response_log->release_id = $send->id;
                        $response_log->save();
                    }

                }

                elseif ($solo != '' && $group[0] != '' && $group[0] == 'All'){

                    $itdi_under = User::find()
                                    ->where(['not', ['id' => Yii::$app->user->identity->id]])
                                    ->andWhere(['not', ['code_name' => 'ADMIN']])
                                    ->all();

                    $sent_logs = ArrayHelper::map($itdi_under, 'id' , function($model) {
                                    return $model['id'];
                    });

                    foreach ($sent_logs as $sent_log) {

                        $response_log = new ResponseTable;

                        $response_log->tracking_number = $check->tracking_number;
                        $response_log->user_id = Yii::$app->user->identity->id;
                        $response_log->recipient_id = $sent_log;
                        $response_log->release_id = $send->id;
                        $response_log->save();
                    }
                }

                if ($send->validate()){
                $names = UploadedFile::getInstances($files, 'attachment');

                    

                    if ($names == '' || $names == null) {
                    
                        return $this->redirect(['../document_tracking/release-document/index?type=' . $link]);
                    }

                    else {

                        foreach ($names as $name) {

                        $path = 'uploads/'.md5($name->baseName) . $check->id . '.' .$name->extension;

                            if($name->saveAs($path)){

                                $files->attachment = $name->baseName . '.' . $name->extension;
                                $files->attachment_path = $path;
                                $files->release_id = $send->id;
                                Yii::$app->itdidb_dt->createCommand()->insert('file_uploads', ['attachment' => $files->attachment, 'attachment_path' => $path, 'release_id' => $send->id])->execute();
                            }
                            
                        }

                        return $this->redirect(['../document_tracking/release-document/index?type=' . $link]);
                    }
                }
            }
        }
        //-->
        //<--Check for URL Alterations
        if ($check)
        {
            return $this->render('view',
            [
                'document_details' => $document_details,
                'full' => $full,
                'send' => $send,
                'section' => $group,
                'individual' => $individual,
                'files' => $files,
            ]);
        } else {
            return $this->redirect(['error']);
        }
        //-->
    }

    public function actionBlank(){
        
        return $this->render('blank');
    }

    public function actionError(){
        
        return $this->render('error');
    }
}
?>