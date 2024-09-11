<?php

namespace app\modules\document_tracking\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

use app\modules\document_tracking\models\DocumentDetails;
use app\modules\document_tracking\models\DocumentResponse;
use app\modules\document_tracking\models\ResponseTable;
use app\modules\document_tracking\models\FileUpload;

use app\models\profile\Profile;




class ReceiveDocumentController extends Controller
{
  
    public function actionIndex(){

        if(!isset($_GET['type']) || $_GET['type'] == NULL){

            return $this->redirect('error');
        }

        if ($_GET['type'] == 'IC'){

            $internal_communication = ResponseTable::find()
                                        ->select('response_table.tracking_number, response_table.id, , response_table.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 1],
                                            ['recipient_id' => Yii::$app->user->identity->id],
                                            ['status' => 0],
                                        ])
                                        ->distinct()
                                        ->orderBy(['id' => SORT_DESC])
                                        ->all();

            $dataProvider = new ArrayDataProvider([
                'allModels' => $internal_communication,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }

        if ($_GET['type'] == 'I'){

            $incoming = ResponseTable::find()
                                        ->select('response_table.tracking_number, response_table.id, response_table.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 2],
                                            ['recipient_id' => Yii::$app->user->identity->id],
                                            ['status' => 0],
                                        ])
                                        ->distinct()
                                        ->orderBy(['id' => SORT_DESC])
                                        ->all();

            $dataProvider = new ArrayDataProvider([
                'allModels' => $incoming,
                'pagination' => [
                    'pageSize' => 10,
                ],
            ]);
        }

        if ($_GET['type'] == 'O'){

            $outgoing = ResponseTable::find()
                                        ->select('response_table.tracking_number, response_table.id, response_table.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 3],
                                            ['recipient_id' => Yii::$app->user->identity->id],
                                            ['status' => 0],
                                        ])
                                        ->distinct()
                                        ->orderBy(['id' => SORT_DESC])
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

        $receive = new DocumentResponse;

        $link = $_GET['type'];

        

        //Document Details Checking
        $check = ResponseTable::find()
                ->where(['id' => $_GET['id']])
                ->andWhere(['recipient_id' => Yii::$app->user->identity->id])
                ->one();

        $files = FileUpload::find()
                    ->where(['release_id' => $check['release_id']])->all();

        $file_list = ArrayHelper::map($files, 'id' , function($model) {
                return $model['attachment'] . ":" .$model['id'];
        });

        if ($check == NULL) {
            return $this->redirect(['error']);
        }

        $document_details = DocumentDetails::find()
                        ->where(['tracking_number' => $check['tracking_number']])
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

        $view = ResponseTable::find()
                    ->where(['id' => $_GET['id']])
                    ->andWhere(['recipient_id' => Yii::$app->user->identity->id])
                    ->one();
        
        $view->view = '1';

        $view->save();

        if ($receive->load(Yii::$app->request->post()))
        {

            $check->status = 1;

            $receive->tracking_number = $check->tracking_number;
            $receive->action_by = Yii::$app->user->identity->id;
            $receive->action = 'RECEIVED';

            if ($receive->save()){

                $check->save();

                return $this->redirect(['../document_tracking/receive-document/index?type=' . $link]);    
            }
        }

        //<--Check for URL Alterations
        if ($check)
        {
        return $this->render('view', [
            'receive' => $receive,
            'document_details' => $document_details,
            'full' => $full,
            'file_list' => $file_list,
        ]);
        } else {
            return $this->redirect(['error']);
        }
        //-->
    }

    public function actionDownload(){

        $dl = FileUpload::find()->where(['id' => $_GET['file_id']])->one();
        $path  = Yii::getAlias('@webroot');
        $file = $path . '/' . $dl->attachment_path;
        if (file_exists($file)) {

            Yii::$app->response->SendFile($file, $dl->attachment);
        }
        else {
            $this->render('download');
        }
    }

    public function actionError(){
        return $this->render('error');
    }
}
