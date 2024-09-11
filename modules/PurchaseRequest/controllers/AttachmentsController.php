<?php

namespace app\modules\PurchaseRequest\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\modules\PurchaseRequest\models\Attachments;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\Model;
use app\modules\PurchaseRequest\models\PurchaseRequest;


class AttachmentsController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Attachments::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Attachments::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    // end-user
    public function actionAttachmentCreate()
    {
        $request = Yii::$app->request;
        $model = PurchaseRequest::find()->where(['id' => $_GET['id']])->one();
        $modelAttachments = new Attachments();
        $historylog = new HistoryLog();
        $modelAttach = [new Attachments];
        $valid = [];

        $model->status = 45;

        if ($model->load(Yii::$app->request->post())) {
            $modelAttach = Model::createMultiple(Attachments::classname());
            Model::loadMultiple($modelAttach, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelAttach) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {
                            foreach ($modelAttach as $i => $attach) {
                                $file[$i] = UploadedFile::getInstanceByName("Attachments[" . $i . "][file_name]");
                                if ($file[$i] != NULL) {

                                    $path = 'uploads/pr_files/' . md5($file[$i]->baseName . date("m/d/y G:i:s:u")) . '.' . $file[$i]->extension;

                                    if ($file[$i]->saveAs($path)) {
                                        $attach->pr_id = $model->id;
                                        $attach->file_directory = $path;
                                        $attach->file_name = $file[$i]->baseName . '.' . $file[$i]->extension;
                                        $attach->file_extension = $file[$i]->extension;
                                        $attach->time_stamp = date('Y-m-d h:i');
                                        $valid[] = $attach->save();
                                    }
                                } else {
                                    continue;
                                }
                            }

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;

                            $historylog->save();
                            $model->save();

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            return $this->redirect(['purchase-request/on-process-request-items', 'id' => $model->id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/purchase-request/pr_attachments_create', [
            'model' => $model,
            'modelAttachments' => $modelAttachments,
            'modelAttach' => (empty($modelAttach)) ? [new Attachments] : $modelAttach,
            'historylog' => $historylog
        ]);
    }

    // BAC
    public function actionBacAttachmentRequest()
    {
        $id = Yii::$app->request->post('id');

        $model = PurchaseRequest::find()
            ->where(['id' => $id])
            ->one();

        $historylog = new HistoryLog();
        $model->status = 44;

        if ($model->save()) {

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->remarks = $_POST['remarks'];
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->save();
            
        }
        Yii::$app->getSession()->setFlash('success', 'Request Submitted');
        return $this->redirect(['purchase-request/bac-request-index']);
    }

    public function actionBacAttachmentCreate()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();
            
        $modelAttachments = new Attachments();
        $historylog = new HistoryLog();
        $modelAttach = [new Attachments];
        $valid = [];
        $model->status = 45;

        if ($model->load(Yii::$app->request->post()) && $historylog->load(Yii::$app->request->post())) {
            $modelAttach = Model::createMultiple(Attachments::classname());
            Model::loadMultiple($modelAttach, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelAttach) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {
                            foreach ($modelAttach as $i => $attach) {
                                $file[$i] = UploadedFile::getInstanceByName("Attachments[" . $i . "][file_name]");

                                if ($file[$i] != NULL) {

                                    $path = 'uploads/pr_files/' . md5($file[$i]->baseName . date("m/d/y G:i:s:u")) . '.' . $file[$i]->extension;

                                    if ($file[$i]->saveAs($path)) {
                                        $attach->pr_id = $model->id;
                                        $attach->file_directory = $path;
                                        $attach->file_name = $file[$i]->baseName . '.' . $file[$i]->extension;
                                        $attach->file_extension = $file[$i]->extension;
                                        $attach->time_stamp = date('Y-m-d h:i');
                                        $valid[] = $attach->save();
                                    }
                                } else {
                                    continue;
                                }
                            }

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;

                            $historylog->save();
                            $model->save();

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            return $this->redirect(['purchase-request/bac-prview', 'id' => $model->id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/purchase-request/pr_attachments_create', [
            'model' => $model,
            'modelAttachments' => $modelAttachments,
            'modelAttach' => (empty($modelAttach)) ? [new Attachments] : $modelAttach,
            'historylog' => $historylog
        ]);
    }

    // Ppms
    public function actionPpmsAttachmentRequest()
    {
        $id = Yii::$app->request->post('id');

        $model = PurchaseRequest::find()
            ->where(['id' => $id])
            ->one();

        $historylog = new HistoryLog();
        $model->status = 44;

        if ($model->save()) {

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->remarks = $_POST['remarks'];
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->save();

            return $this->redirect(['purchase-request/procurement-request-index', 'id' => $model->id]);
        }
    }

    public function actionPpmsAttachmentCreate()
    {
        $request = Yii::$app->request;
        $model = PurchaseRequest::find()->where(['id' => $_GET['id']])->one();
        $modelAttachments = new Attachments();
        $historylog = new HistoryLog();
        $modelAttach = [new Attachments];
        $valid = [];

        $model->status = 45;

        if ($model->load(Yii::$app->request->post()) && $historylog->load(Yii::$app->request->post())) {

            $modelAttach = Model::createMultiple(Attachments::classname());
            Model::loadMultiple($modelAttach, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelAttach) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {
                            // var_dump($valid);die;
                            foreach ($modelAttach as $i => $attach) {
                                $file[$i] = UploadedFile::getInstanceByName("Attachments[" . $i . "][file_name]");
                                if ($file[$i] != NULL) {

                                    $path = 'uploads/pr_files/' . md5($file[$i]->baseName . date("m/d/y G:i:s:u")) . '.' . $file[$i]->extension;

                                    if ($file[$i]->saveAs($path)) {
                                        $attach->pr_id = $model->id;
                                        $attach->file_directory = $path;
                                        $attach->file_name = $file[$i]->baseName . '.' . $file[$i]->extension;
                                        $attach->file_extension = $file[$i]->extension;
                                        $valid[] = $attach->save();
                                    }
                                } else {
                                    continue;
                                }
                            }

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;

                            $historylog->save();
                            $model->save();

                            return $this->redirect(['purchase-request/procurement-prview', 'id' => $model->id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/purchase-request/pr_attachments_create', [
            'model' => $model,
            'modelAttachments' => $modelAttachments,
            'modelAttach' => (empty($modelAttach)) ? [new Attachments] : $modelAttach,
            'historylog' => $historylog
        ]);
    }
}
