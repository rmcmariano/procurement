<?php

namespace app\modules\document_tracking\controllers;

use Yii;
use yii\web\Controller;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

use app\modules\document_tracking\models\DocumentDetails;
use app\modules\document_tracking\models\DocumentResponse;
use app\modules\document_tracking\models\ResponseTable;

use app\models\profile\Profile;

class RedirectDocumentController extends Controller
{
  
    public function actionIndex(){

        if(!isset($_GET['type']) || $_GET['type'] == NULL){

            return $this->redirect('error');
        }

        if ($_GET['type'] == 'IC'){

            $internal_communication = ResponseTable::find()
                                        ->select('response_table.tracking_number, response_table.id')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 1],
                                            ['recipient_id' => Yii::$app->user->identity->id],
                                            ['status' => 0],
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

            $incoming = ResponseTable::find()
                                        ->select('response_table.tracking_number, response_table.id')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 2],
                                            ['recipient_id' => Yii::$app->user->identity->id],
                                            ['status' => 0],
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

            $outgoing = ResponseTable::find()
                                        ->select('response_table.tracking_number, response_table.id')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 3],
                                            ['recipient_id' => Yii::$app->user->identity->id],
                                            ['status' => 0],
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

        $redirect = new DocumentResponse;
        $new_response = new ResponseTable;

        $link = $_GET['type'];

        //Document Details Checking
        $check = ResponseTable::find()
                ->where(['id' => $_GET['id']])
                ->andWhere(['recipient_id' => Yii::$app->user->identity->id])
                ->one();

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

        //<--Dropdown Query
        $users = Profile::find()
                    ->where(['not', ['user_id' => Yii::$app->user->identity->id]])
                    ->andWhere(['not', ['lname' => 'admin']])
                    ->all();

        $individual = ArrayHelper::map($users, 'user_id' , function($model) {
                return $model['fname'] . ' ' . $model['mi'] . ' ' . $model['lname'];
        });
        //-->

        //<--On Submit
        if ($redirect->load(Yii::$app->request->post()))
        {

            $check->status = 2;

            $redirect->tracking_number = $check->tracking_number;
            $redirect->action_by = Yii::$app->user->identity->id;
            $redirect->action = 'REDIRECT';

            $new_response->tracking_number = $check->tracking_number;
            $new_response->user_id = Yii::$app->user->identity->id;
            $new_response->recipient_id = $redirect->individual;

            if ($redirect->save()){

                $new_response->release_id = $redirect->id;

                $check->save();
                $new_response->save();
                return $this->redirect(['../document_tracking/redirect-document/index?type=' . $link]);    
            }

        }

        //<--Check for URL Alterations
        if ($check)
        {
            return $this->render('view',
            [
                'document_details' => $document_details,
                'full' => $full,
                'redirect' => $redirect,
                'individual' => $individual,
                'new_response' => $new_response,
            ]);
        } else {
            return $this->redirect(['error']);
        }
        //-->
    }

    public function actionError(){
        return $this->render('error');
    }
}
