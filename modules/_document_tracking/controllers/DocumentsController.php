<?php

namespace app\modules\document_tracking\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

use app\modules\document_tracking\models\DocumentDetails;
use app\modules\document_tracking\models\DocumentResponse;
use app\modules\document_tracking\models\DocumentSequence;
use app\modules\document_tracking\models\ResponseTable;
use app\modules\document_tracking\models\FileUpload;

use app\models\profile\Profile;

class DocumentsController extends Controller
{

    public function actionIndex(){


        if(!isset($_GET['type']) || $_GET['type'] == NULL){

            return $this->redirect('error');
        }

        if ($_GET['type'] == 'IC'){

            $internal_communication = DocumentResponse::find()
                                        ->select('document_response.tracking_number')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 1],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 1],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
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
                                        ->select('document_response.tracking_number')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 2],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 2],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
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
                                        ->select('document_response.tracking_number')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 3],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 3],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
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

    public function actionCreate(){

        $document_details = new DocumentDetails;
        $document_logs = new DocumentResponse;
        
        if ($document_details->load(Yii::$app->request->post())) {

            //<--Query Document Type Series
            $sequence = DocumentSequence::find()
                            ->where(['id' => $document_details->document_type])
                            ->one();
            //-->
            $date = Yii::$app->formatter->asDate('now', 'yyMM');

            //<--Record details of created document
            if ($sequence['document_type'] == 'Internal Communication') {
                $document_details->tracking_number = 'INTER-' . $date . '-' . str_pad($sequence->sequence, 5, '0', STR_PAD_LEFT);
                $type = 'IC';                
            }
            if ($sequence['document_type'] == 'Incoming') {
                $document_details->tracking_number = 'INC-' . $date . '-' . str_pad($sequence->sequence, 5, '0', STR_PAD_LEFT);
                $type = 'I';
            }
            if ($sequence['document_type'] == 'Outgoing') {
                $document_details->tracking_number = 'OUT-' . $date . '-' . str_pad($sequence->sequence, 5, '0', STR_PAD_LEFT);
                $type = 'O';
            }

            $document_details->created_by = Yii::$app->user->identity->id;
            //-->

            //<--Increment document type sequence
            $sequence->sequence = $sequence->sequence + 1;
            //-->

            //<--Document logs
            $document_logs->tracking_number = $document_details->tracking_number;
            $document_logs->action_by = Yii::$app->user->identity->id;
            $document_logs->action = 'CREATED';
            //-->

            if ($document_details->save()) {
                
                $document_logs->save();
                $sequence->save();

                return $this->redirect(['view', 'tracking_number' => $document_details->tracking_number, 'type' => $type]);

            }

        }

        return $this->render('create', 
        [
            'document_details' => $document_details,
        ]);
    }

    public function actionView(){

        if(!isset($_GET['tracking_number']) || $_GET['tracking_number'] == NULL){

            return $this->redirect('error');
        }

        //Document Details
        $document_details = DocumentDetails::find()
                        ->where(['tracking_number' => $_GET['tracking_number']])
                        ->one();

        if ($document_details == NULL) {
            return $this->redirect(['error']);
        }
        //-->

        //<--Profile of User
        $profile = Profile::find()
                        ->where(['user_id' => $document_details->created_by])
                        ->one();

        $full = $profile['fname'] . " " . $profile['mi'] . " " . $profile['lname'];
        //-->

        //<--Document Logs
        $history = DocumentResponse::find()
                        ->where(['tracking_number' => $_GET['tracking_number']])
                        ->orderBy(['id' => SORT_DESC])
                        ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $history,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]); 
        //-->

        $close = new DocumentResponse;

        if ($close->load(Yii::$app->request->post())) {

            $document_details->is_closed = '1';

            $close->action_by = Yii::$app->user->identity->id;
            $close->action = 'CLOSED';

            if($document_details->save()){

                $close->save();

                return $this->redirect(['view', 'tracking_number' => $_GET['tracking_number'], 'type' => $_GET['type']]);
            }
        }

        //<--Check for URL Alterations
        $check = DocumentResponse::find()
                    ->where(['action_by' => Yii::$app->user->identity->id])
                    ->andWhere(['tracking_number' => $_GET['tracking_number']])
                    ->all();

        if ($check)
        {
            return $this->render('view',
            [
                'document_details' => $document_details,
                'full' => $full,
                'dataProvider' => $dataProvider,
                'close' => $close,
            ]);
        } else {
            return $this->redirect(['error']);
        }
        //-->
    }

    public function actionError(){

        return $this->render('error');
    }

    public function actionRecipients(){

        $recipients = ResponseTable::find()
                        ->where(['release_id' => $_GET['id']])
                        ->all();

        $response_details = DocumentResponse::find()
                                ->where(['id' => $_GET['id']])
                                ->one();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $recipients,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        return $this->render('recipients', [
            
            'dataProvider' => $dataProvider,
            'response_details' => $response_details,
        ]);
    }

    public function actionDownloadlist(){

        $file_list = FileUpload::find()
                        ->where(['release_id' => $_GET['id']])
                        ->all();

        $response_details = DocumentResponse::find()
                                ->where(['id' => $_GET['id']])
                                ->one();

        $listData = ArrayHelper::map($file_list, 'id' , function($model) {
                return $model['attachment'] . ':' . $model['id'];
        });

        return $this->renderAjax('downloadlist', [

            'listData' => $listData,
            'response_details' => $response_details,
        ]);

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
}
?>