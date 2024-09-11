<?php

namespace app\modules\PurchaseRequest\controllers;

use Yii;
use app\modules\PurchaseRequest\models\Attachments;
use app\modules\PurchaseRequest\models\BidBulletin;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\Division;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseRequestSearch;
use app\modules\PurchaseRequest\models\Section;
use app\modules\PurchaseRequest\models\Model;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\DivisionFormSequence;
use app\modules\PurchaseRequest\models\ItemHistoryLogs;
use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\PbwLibPs;
use app\modules\PurchaseRequest\models\PrCodeFormSequence;
use app\modules\PurchaseRequest\models\PrEnduser;
use app\modules\PurchaseRequest\models\PrItemSearch;
use app\modules\PurchaseRequest\models\PurchaseOrderSearch;
use app\modules\PurchaseRequest\models\Quotation;
use app\modules\PurchaseRequest\models\TrackStatus;
use app\modules\PurchaseRequest\models\Info;
use app\modules\user\models\Profile;
use app\modules\user\models\User;
use yii\web\UploadedFile;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use kartik\mpdf\Pdf;
use VARIANT;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\helpers\HtmlPurifier;
use yii\web\Response;

class PurchaseRequestController extends Controller
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

    // on process request
    public function actionAllrequestIndex()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->all(Yii::$app->request->queryParams);

        return $this->render('/system-admin/all_pr', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    // On process request items
    public function actionAllrequestItems()
    {
        $model = new PrItems();
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->all(Yii::$app->request->queryParams);
        // var_dump($model);die;

        return $this->render('pr_index_approved_items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // on process request
    public function actionOnProcessRequestIndex()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('pr_index_approved', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    // On process request items
    public function actionOnProcessRequestItems()
    {
        $model = new PrItems();
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // var_dump($model);die;

        return $this->render('pr_index_approved_items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // pending request 
    public function actionPendingRequestIndex()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->pending(Yii::$app->request->queryParams);

        return $this->render('pr_index_pending', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // pending items
    public function actionPendingRequestItems()
    {
        $model = new PrItems();
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->pending(Yii::$app->request->queryParams);

        return $this->render('pr_index_pending_items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // view btn
    public function actionPurchaserequestView()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $endUser = PrEnduser::find()
            ->where(['pr_id' => $model['id']])
            ->all();

        $items = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
        ]);

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        $searchModel = new PurchaseOrderSearch();
        $dataProvider3 = $searchModel->search(Yii::$app->request->queryParams, $_GET['id']);

        return $this->render('pr_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            'items' => $items,
            'endUser' => $endUser
        ]);
    }

    public function actionDownload()
    {
        $fileName = 'Data';
        if (isset($_POST['obj'])) {
            $data = $_POST['obj'];
            $attachment_id = $data['id'];
            $filename = Attachments::find()->where(['id' => $attachment_id])->one();
            $path = $filename->file_directory;
            $file = 'https://procurement.itdi.ph/' . $path;

            // return Yii::$app->response->xSendFile($file);
            return $this->redirect($file);
        }
    }

    // create PR 
    public function actionPurchaserequestCreate()
    {
        $model = new PurchaseRequest();
        $modeldescription = [new PrItems()];
        $modelEnduser = new PrEnduser();
        $modelSpecifications = [[new ItemSpecification]];
        $modelAttachments = new Attachments();
        $historylog = new HistoryLog();
        $modelAttach = [new Attachments];

        $model->time_stamp = date('Y-m-d h:i');

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $modelInfo = Info::find()->where(['id' => $model->charge_to])->one();

            switch ($model->fund_source_id) {
                case 2:
                    $requested_by = $model->requested_by2;
                    break;
                case 1:
                    if ($model->indirect_direct_cost === null) {
                        $requested_by = $model->requested_by1;
                    } elseif ($model->indirect_direct_cost == 0) {
                        $requested_by = $model->requested_by1;
                    } else {
                        $requested_by = $model->requested_by3;
                    }
                    break;
                default:
                    $requested_by = null;
            }

            $model->requested_by = $requested_by;

            if (Yii::$app->request->post('save') == 0) {
                $model->status = 8;
            }
            if (Yii::$app->request->post('submit_save') == 1) {
                $model->status = 1;
            }
       
            $approvedby = User::find()->where(['id' => $model->approved_by])->one();
            $division = Division::find()->where(['id' => $approvedby->division_id])->one();
            $model->division = $division['id'];

            $sequence = DivisionFormSequence::find()->where(['division_code' => 'TEMP'])->one();
            $tempnumber  = $sequence->division_code . '-' . date('m') . '-' . date('Y') . '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
            $sequence->form_sequence = $sequence->form_sequence + 1;
            $model->temp_no = $tempnumber;

            if ($model->fund_source_id == 1) {
                $model->created_by = Yii::$app->user->identity->id;
                $model->proj_accountant_id = $modelInfo->accountant_id;
                $model->requested_by = $requested_by;
                $model->requestedby_position = $model->position->id;
                $requestedBy = Profile::find()->where(['id' => $model->requested_by])->one();
                $div_requestedBy = User::find()->where(['id' => $requestedBy->user_id])->one();
                $model->section = $div_requestedBy['section_id'];
            } else {
                $model->charge_to = 'GAA-';
                $model->created_by = Yii::$app->user->identity->id;
                $model->requested_by = $requested_by;
                $model->requestedby_position = $model->position->id;
                $requestedBy = Profile::find()->where(['id' => $model->requested_by])->one();
                $div_requestedBy = User::find()->where(['id' => $requestedBy->user_id])->one();
                $model->section = $div_requestedBy['section_id'];
            }

            $modeldescription = Model::createMultiple(PrItems::classname());
            Model::loadMultiple($modeldescription, Yii::$app->request->post());

            $modelAttach = Model::createMultiple(Attachments::classname());
            Model::loadMultiple($modelAttach, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modeldescription) && $valid;
            $valid = Model::validateMultiple($modelAttach) && $valid;

            $post = Yii::$app->request->post();
            $postModel = $post['PrEnduser'];
            $postModelMulti =  $postModel['user_id'];

            $model->save();

            if (isset($_POST['ItemSpecification'][0][0])) {
                foreach ($_POST['ItemSpecification'] as $i => $specs) {
                    foreach ($specs as $index => $spec) {
                        $data['ItemSpecification'] = $spec;
                        $modelSpecification = new ItemSpecification();
                        $modelSpecification->load($data);
                        $modelSpecifications[$i][$index] = $modelSpecification;
                        $valid = $modelSpecification->validate();
                    }
                }
            }
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {
                            foreach ($modeldescription as $i => $modelotherdescription) {

                                $modelotherdescription->pr_id = $model->id;
                                $modelotherdescription->save();
                                if (!($valid[] = $modelotherdescription->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }

                                if (isset($modelSpecifications[$i]) && is_array($modelSpecifications[$i])) {
                                    foreach ($modelSpecifications[$i] as $index => $modelSpecification) {
                                        $modelSpecification->item_id = $modelotherdescription->id;

                                        $modelSpecification->save();

                                        if (!($valid[] = $modelSpecification->save(false))) {

                                            $transaction->rollBack();
                                            break;
                                        }
                                    }
                                }
                            }

                            if (!empty($postModelMulti)) {
                                foreach ($postModelMulti as $key => $value) {

                                    $modelEnduser = new PrEnduser();
                                    $modelEnduser->pr_id = $model->id;
                                    $modelEnduser->user_id = $value;
                                    $modelEnduser->save();
                                }
                            }

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

                            foreach ($modeldescription as $modelotherdescription) {

                                $itemHistorylog = new ItemHistoryLogs();
                                $modelotherdescription->status = $model->status;

                                $itemHistorylog->item_id = $modelotherdescription->id;
                                $itemHistorylog->action_date = date('Y-m-d h:i');
                                $itemHistorylog->action_status = $modelotherdescription->status;
                                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                                $modelotherdescription->save(false);
                                $itemHistorylog->save();
                            }

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;

                            $historylog->save();
                            $sequence->save();

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            return $this->redirect(['pending-request-index', 'id' => $model->id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->render('pr_create', [
            'model' => $model,
            'modeldescription' => (empty($modeldescription)) ? [new PrItems] : $modeldescription,
            'modelAttachments' => $modelAttachments,
            'modelAttach' => (empty($modelAttach)) ? [new Attachments] : $modelAttach,
            'modelSpecifications' => (empty($modelSpecifications)) ? [new ItemSpecification()] : $modelSpecifications,
            'modelEnduser' => $modelEnduser
        ]);
    }

    // list of pr (sdo)
    public function actionSdoIndex()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->sdo(Yii::$app->request->queryParams);

        return $this->render('/sdo/sdo_pr_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // list of items tab (sdo)
    public function actionSdoItems()
    {
        $model = new PrItems();
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->sdoIndex(Yii::$app->request->queryParams);

        return $this->renderAjax('/sdo/sdo_pr_items_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // view btn of SDO
    public function actionSdoView()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $endUser = PrEnduser::find()->where(['pr_id' => $model['id']])->all();

        $items = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
        ]);

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        $searchModel = new PurchaseOrderSearch();
        $dataProvider3 = $searchModel->search(Yii::$app->request->queryParams, $_GET['id']);

        return $this->render('/sdo/sdo_pr_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            'items' => $items,
            'endUser' => $endUser
        ]);
    }

    public function actionPurchaserequestLogs()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $searchModel = new PurchaseRequestSearch();

        $historylog = HistoryLog::find()
            ->where(['pr_id' => $_GET['id']])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $historylog,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('pr_logs', [
            'dataProvider' => $dataProvider,
            'model' => $model,
            'searchModel' => $searchModel,
        ]);
    }

    // history btn in item_index
    public function actionPurchaserequestItemlogs()
    {
        $description = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $description->pr_id])
            ->one();

        $itemHistory = ItemHistoryLogs::find()
            ->where(['item_id' => $description['id']])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $itemHistory,
            'pagination' => [
                'pageSize' => 20,
            ]
        ]);

        return $this->render('pr_items_logs', [
            'description' => $description,
            'itemHistory' => $itemHistory,
            'dataProvider' => $dataProvider,
            'purchaserequest' => $purchaserequest,
        ]);
    }

    // update for revision in end user
    public function actionPurchaserequestRevise($id)
    {
        $model = $this->findModel($id);
        $modeldescription = $model->prItems;
        $modelName = Profile::find()->where(['id' => $model->requested_by])->one();
        $modelFullName = $modelName->fname . ' ' . $modelName->lname;
        $modelSpecifications = [];
        $oldSpecification = [];

        $modelEnduser = PrEnduser::find()->where(['pr_id' => $model->id])->all();

        $historylog = new HistoryLog();

        $inputFiles = $model->inputfile;
        $file_preview = [];
        $file_config = [];
        foreach ($inputFiles as $inputfile) {
            $imgUrl = Url::to('@web/') . $inputfile->file_directory;
            $file_preview[] = $imgUrl;
            $file_config[] = array("type" => $inputfile->file_extension, "caption" => $inputfile->file_name, 'key' => $inputfile->id);
        }

        if (!empty($modeldescription)) {
            foreach ($modeldescription as $i => $modelotherdescription) {
                $specs = $modelotherdescription->itemspecification;
                $modelSpecifications[$i] = $specs;
                $oldSpecification = ArrayHelper::merge(ArrayHelper::index($specs, 'id'), $oldSpecification);
            }
        }

        $model->revised_series_no += 1;
        $model->save();

        if ($model->load(Yii::$app->request->post())) {

            $model->status = 41;
            $e = $model;
            $modelSpecifications = [];

            $oldIDs = ArrayHelper::map($modeldescription, 'id', 'id');
            $modeldescription = Model::createMultiple(PrItems::classname(), $modeldescription);
            Model::loadMultiple($modeldescription, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modeldescription, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modeldescription) && $valid;

            // $inputfile->save();
            $inputFiles = UploadedFile::getInstances($model, 'file');

            foreach ($inputFiles as $inputFile) {
                $path = 'uploads/pr_files/' . md5($inputFile->baseName . date("m/d/y G:i:s:u")) . '.' . $inputFile->extension;

                if ($inputFile->saveAs($path)) {
                    $file_name = $inputFile->baseName . '.' . $inputFile->extension;
                    $file_directory = $path;
                    $pr_id = $model->id;
                    $file_extension = $inputFile->extension;

                    if (!empty($deletedIDs_gpf)) {
                        Attachments::deleteAll(['id' => $deletedIDs_gpf]);
                    }
                    switch ($inputFile->extension) {
                        case "pdf":
                            $file_extension = "pdf";
                            break;
                        case "docx":
                            $file_extension = $inputFile->extension;
                            break;
                        case "xlsx":
                            $file_extension = $inputFile->extension;
                            break;
                        default:
                            $file_extension = "image";
                    }
                    Yii::$app->itdidb_procurement_system->createCommand()->insert('attachments', [
                        'file_name' => $file_name,
                        'file_directory' => $file_directory,
                        'pr_id' => $pr_id,
                        'file_extension' => $file_extension,
                    ])->execute();
                }
            }

            // $modelSpecification->save
            $specIDs = [];
            if (isset($_POST['ItemSpecification'][0][0])) {
                foreach ($_POST['ItemSpecification'] as $i => $specs) {
                    $specIDs = ArrayHelper::merge($specIDs, array_filter(ArrayHelper::getColumn($specs, 'id')));

                    foreach ($specs as $index => $spec) {
                        $data['ItemSpecification'] = $spec;
                        $modelSpecification = (isset($spec['id']) && isset($oldSpecification[$spec['id']])) ? $oldSpecification[$spec['id']] : new ItemSpecification();
                        $modelSpecification->load($data);
                        $modelSpecifications[$i][$index] = $modelSpecification;
                        $valid = $modelSpecification->validate();
                    }
                }
            }

            $oldSpecIDs = ArrayHelper::getColumn($oldSpecification, 'id');
            $deletedSpecIDs = array_diff($oldSpecIDs, $specIDs);

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {

                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            PrItems::deleteAll(['id' => $deletedIDs]);
                        }

                        if (!empty($deletedSpecIDs)) {
                            ItemSpecification::deleteAll(['id' => $deletedSpecIDs]);
                        }

                        foreach ($modeldescription as $i => $modelotherdescription) {
                            if ($flag === false) {
                                break;
                            }

                            $modelotherdescription->pr_id = $model->id;
                            $modelotherdescription->status = 41;

                            $itemHistorylog = new ItemHistoryLogs();
                            $itemHistorylog->item_id = $modelotherdescription->id;
                            $itemHistorylog->action_date = date('Y-m-d h:i');
                            $itemHistorylog->action_status = $modelotherdescription->status;
                            $itemHistorylog->action_by = Yii::$app->user->identity->id;
                            $itemHistorylog->save();

                            if (!($flag = $modelotherdescription->save(false))) {
                                break;
                            }

                            if (isset($modelSpecifications[$i]) && is_array($modelSpecifications[$i])) {
                                foreach ($modelSpecifications[$i] as $index => $modelSpecification) {
                                    $modelSpecification->item_id = $modelotherdescription->id;
                                    if (!($flag = $modelSpecification->save(false))) {
                                        break;
                                    }
                                }
                            }
                        }

                        $historylog->pr_id = $model->id;
                        $historylog->action_date = date('Y-m-d h:i');
                        $historylog->action_status = $model->status;
                        $historylog->action_user_id = Yii::$app->user->identity->id;
                        $historylog->remarks = 'END USER - Revised PR';

                        $historylog->save();

                        return $this->redirect(['on-process-request-index', 'id' => $_GET['id']]);
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['on-process-request-index', 'id' => $_GET['id']]);
                    } else {
                        $transaction->rollBack();
                    }
                } catch (\Exception $e) {
                }
            }
            $model->save();
        }

        return $this->render('pr_update_', [
            'modelFullName' => $modelFullName,
            'model' => $model,
            'modeldescription' => (empty($modeldescription)) ? [new PrItems] : $modeldescription,
            'modelSpecifications' => (empty($modelSpecifications)) ? [new ItemSpecification()] : $modelSpecifications,
            'file_preview' => $file_preview,
            'file_config' => $file_config,
            'inputFiles' => $inputFiles,
            'modelEnduser' => $modelEnduser
        ]);
    }

    // public function actionPurchaserequestUpdate($id)
    // {
    //     try {
    //         $model = $this->findModel($id);
    //         $modeldescription = $model->prItems;
    //         $modelName = Profile::find()->where(['id' => $model->requested_by])->one();
    //         $modelFullName = $modelName->fname . ' ' . $modelName->lname;
    //         $modelSpecifications = [];
    //         $oldSpecification = [];

    //         $modelEnduser = PrEnduser::find()->where(['pr_id' => $model->id])->all();

    //         $historylog = new HistoryLog();

    //         $inputFiles = $model->inputfile;
    //         $file_preview = [];
    //         $file_config = [];
    //         foreach ($inputFiles as $inputfile) {
    //             $imgUrl = Url::to('@web/') . $inputfile->file_directory;
    //             $file_preview[] = $imgUrl;
    //             $file_config[] = array("type" => $inputfile->file_extension, "caption" => $inputfile->file_name, 'key' => $inputfile->id);
    //         }

    //         if (!empty($modeldescription)) {
    //             foreach ($modeldescription as $i => $modelotherdescription) {
    //                 $specs = $modelotherdescription->itemspecification;
    //                 $modelSpecifications[$i] = $specs;
    //                 $oldSpecification = ArrayHelper::merge(ArrayHelper::index($specs, 'id'), $oldSpecification);
    //             }
    //         }

    //         if ($model->load(Yii::$app->request->post())) {

    //             if ($model->fund_source_id == 2) {
    //                 $model->charge_to = "";
    //             }
    //             if ($model->charge_to == null) {
    //                 $requested_by = $model->requested_by2;
    //             } else {
    //                 $requested_by = $model->requested_by1;
    //             }
    //             $model->requested_by = $requested_by;
    //             $model->status = 58;
    //             // $modelSpecifications = [];
    //             $model->save();

    //             if (!$model->save()) {
    //                 Yii::error('Model save failed: ' . json_encode($model->errors), 'error');
    //             }

    //             $oldIDs = ArrayHelper::map($modeldescription, 'id', 'id');
    //             $modeldescription = Model::createMultiple(PrItems::classname(), $modeldescription);
    //             Model::loadMultiple($modeldescription, Yii::$app->request->post());
    //             $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modeldescription, 'id', 'id')));

    //             $valid = $model->validate();
    //             $valid = Model::validateMultiple($modeldescription) && $valid;

    //             $inputFiles = UploadedFile::getInstances($model, 'file');

    //             foreach ($inputFiles as $inputFile) {
    //                 $path = 'uploads/pr_files/' . md5($inputFile->baseName . date("m/d/y G:i:s:u")) . '.' . $inputFile->extension;

    //                 if ($inputFile->saveAs($path)) {
    //                     $file_name = $inputFile->baseName . '.' . $inputFile->extension;
    //                     $file_directory = $path;
    //                     $pr_id = $model->id;
    //                     $file_extension = $inputFile->extension;

    //                     if (!empty($deletedIDs_gpf)) {
    //                         Attachments::deleteAll(['id' => $deletedIDs_gpf]);
    //                     }
    //                     switch ($inputFile->extension) {
    //                         case "pdf":
    //                             $file_extension = "pdf";
    //                             break;
    //                         case "docx":
    //                             $file_extension = $inputFile->extension;
    //                             break;
    //                         case "xlsx":
    //                             $file_extension = $inputFile->extension;
    //                             break;
    //                         default:
    //                             $file_extension = "image";
    //                     }
    //                     Yii::$app->itdidb_procurement_system->createCommand()->insert('attachments', [
    //                         'file_name' => $file_name,
    //                         'file_directory' => $file_directory,
    //                         'pr_id' => $pr_id,
    //                         'file_extension' => $file_extension,
    //                     ])->execute();
    //                 }
    //             }

    //             // $modelSpecification->save
    //             $specIDs = [];
    //             if (isset($_POST['ItemSpecification'][0][0])) {
    //                 foreach ($_POST['ItemSpecification'] as $i => $specs) {
    //                     $specIDs = ArrayHelper::merge($specIDs, array_filter(ArrayHelper::getColumn($specs, 'id')));

    //                     foreach ($specs as $index => $spec) {
    //                         $data['ItemSpecification'] = $spec;
    //                         $modelSpecification = (isset($spec['id']) && isset($oldSpecification[$spec['id']])) ? $oldSpecification[$spec['id']] : new ItemSpecification();
    //                         $modelSpecification->load($data);
    //                         $modelSpecifications[$i][$index] = $modelSpecification;
    //                         $valid = $modelSpecification->validate();
    //                     }
    //                 }
    //             }

    //             $oldSpecIDs = ArrayHelper::getColumn($oldSpecification, 'id');
    //             $deletedSpecIDs = array_diff($oldSpecIDs, $specIDs);

    //             if ($valid) {
    //                 $transaction = \Yii::$app->db->beginTransaction();
    //                 try {
    //                     if ($flag = $model->save(false)) {
    //                         if (!empty($deletedIDs)) {
    //                             PrItems::deleteAll(['id' => $deletedIDs]);
    //                         }

    //                         if (!empty($deletedSpecIDs)) {
    //                             ItemSpecification::deleteAll(['id' => $deletedSpecIDs]);
    //                         }

    //                         foreach ($modeldescription as $i => $modelotherdescription) {
    //                             if ($flag === false) {
    //                                 break;
    //                             }

    //                             $modelotherdescription->pr_id = $model->id;
    //                             $modelotherdescription->status = 58;

    //                             $itemHistorylog = new ItemHistoryLogs();
    //                             $itemHistorylog->item_id = $modelotherdescription->id;
    //                             $itemHistorylog->action_date = date('Y-m-d h:i');
    //                             $itemHistorylog->action_status = $modelotherdescription->status;
    //                             $itemHistorylog->action_by = Yii::$app->user->identity->id;
    //                             $itemHistorylog->save();

    //                             if (!($flag = $modelotherdescription->save(false))) {
    //                                 break;
    //                             }

    //                             if (isset($modelSpecifications[$i]) && is_array($modelSpecifications[$i])) {
    //                                 foreach ($modelSpecifications[$i] as $index => $modelSpecification) {
    //                                     $modelSpecification->item_id = $modelotherdescription->id;
    //                                     if (!($flag = $modelSpecification->save(false))) {
    //                                         break;
    //                                     }
    //                                 }
    //                             }
    //                         }

    //                         $historylog->pr_id = $model->id;
    //                         $historylog->action_date = date('Y-m-d h:i');
    //                         $historylog->action_status = $model->status;
    //                         $historylog->action_user_id = Yii::$app->user->identity->id;
    //                         $historylog->remarks = 'END USER - Updated PR';
    //                         $historylog->save();

    //                         $transaction->commit();
    //                         return $this->redirect(['pending-request-index', 'id' => $_GET['id']]);
    //                     }

    //                     $transaction->rollBack();
    //                     Yii::error('Model save failed: ' . json_encode($model->errors), 'error');
    //                 } catch (\Exception $e) {
    //                     $transaction->rollBack();
    //                     Yii::error('Exception during save: ' . $e->getMessage(), 'error');
    //                     throw $e; 
    //                 }
    //             }
    //         }

    //         return $this->render('pr_update_', [
    //             'modelFullName' => $modelFullName,
    //             'model' => $model,
    //             'modeldescription' => (empty($modeldescription)) ? [new PrItems] : $modeldescription,
    //             'modelSpecifications' => (empty($modelSpecifications)) ? [new ItemSpecification()] : $modelSpecifications,
    //             'file_preview' => $file_preview,
    //             'file_config' => $file_config,
    //             'inputFiles' => $inputFiles,
    //             // 'modelEnduser' => $modelEnduser
    //         ]);
    //     } catch (\Exception $e) {
    //         Yii::error('Exception in actionPurchaserequestUpdate: ' . $e->getMessage(), 'error');
    //         throw $e; // rethrow the exception after logging
    //     }
    // }

    public function actionPurchaserequestUpdate($id)
    {
        try {
            $model = $this->findModel($id);
            $modeldescription = $model->prItems;
            $modelName = Profile::find()->where(['id' => $model->requested_by])->one();
            $modelFullName = ($modelName !== null) ? ($modelName->fname . ' ' . $modelName->lname) : null;
            $modelSpecifications = [];
            $oldSpecification = [];
            $modelEnduser = PrEnduser::find()->where(['pr_id' => $model->id])->all();
            $historylog = new HistoryLog();
            $inputFiles = $model->inputfile;
            $file_preview = [];
            $file_config = [];

            foreach ($inputFiles as $inputfile) {
                $imgUrl = Url::to('@web/') . $inputfile->file_directory;
                $file_preview[] = $imgUrl;
                $file_config[] = array("type" => $inputfile->file_extension, "caption" => $inputfile->file_name, 'key' => $inputfile->id);
            }

            if (!empty($modeldescription)) {
                foreach ($modeldescription as $i => $modelotherdescription) {
                    $specs = $modelotherdescription->itemspecification;
                    $modelSpecifications[$i] = $specs;
                    $oldSpecification = ArrayHelper::merge(ArrayHelper::index($specs, 'id'), $oldSpecification);
                }
            }

            if ($model->load(Yii::$app->request->post())) {

                // if ($model->fund_source_id == 2) {
                //     $model->charge_to = "";
                //     $model->requested_by = $model->requested_by2;
                // }
                // if ($model->fund_source_id == 1) {
                //     $model->charge_to = $model['charge_to'];
                //     $model->requested_by = $model->requested_by1;
                // } else {
                //     $model->requested_by = $model->requested_by;
                // }

                switch ($model->fund_source_id) {
                    case 2:
                        $requested_by = $model->requested_by2;
                        break;
                    case 1:
                        if ($model->indirect_direct_cost === null) {
                            $requested_by = $model->requested_by1;
                        } elseif ($model->indirect_direct_cost == 0) {
                            $requested_by = $model->requested_by1;
                        } else {
                            $requested_by = $model->requested_by3;
                        }
                        break;
                    default:
                        $requested_by = null;
                }
    
                $model->requested_by = $requested_by;

                // $model->requested_by = $requested_by;
                $approvedby = User::find()->where(['id' => $model->approved_by])->one();
                $division = Division::find()->where(['id' => $approvedby->division_id])->one();
                $model->division = $division->id;
                $model->status = 58;
                $model->save();
                // var_dump($model);die;
                if (!$model->save()) {
                    Yii::error('Model save failed: ' . json_encode($model->errors), 'error');
                    throw new \Exception('Model save failed.');
                }

                $oldIDs = ArrayHelper::map($modeldescription, 'id', 'id');
                $modeldescription = Model::createMultiple(PrItems::classname(), $modeldescription);
                Model::loadMultiple($modeldescription, Yii::$app->request->post());
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modeldescription, 'id', 'id')));

                $valid = $model->validate();
                $valid = Model::validateMultiple($modeldescription) && $valid;

                $inputFiles = UploadedFile::getInstances($model, 'file');

                foreach ($inputFiles as $inputFile) {
                    $path = 'uploads/pr_files/' . md5($inputFile->baseName . date("m/d/y G:i:s:u")) . '.' . $inputFile->extension;

                    if ($inputFile->saveAs($path)) {
                        $file_name = $inputFile->baseName . '.' . $inputFile->extension;
                        $file_directory = $path;
                        $pr_id = $model->id;
                        $file_extension = $inputFile->extension;

                        if (!empty($deletedIDs_gpf)) {
                            Attachments::deleteAll(['id' => $deletedIDs_gpf]);
                        }
                        switch ($inputFile->extension) {
                            case "pdf":
                                $file_extension = "pdf";
                                break;
                            case "docx":
                                $file_extension = $inputFile->extension;
                                break;
                            case "xlsx":
                                $file_extension = $inputFile->extension;
                                break;
                            default:
                                $file_extension = "image";
                        }
                        Yii::$app->itdidb_procurement_system->createCommand()->insert('attachments', [
                            'file_name' => $file_name,
                            'file_directory' => $file_directory,
                            'pr_id' => $pr_id,
                            'file_extension' => $file_extension,
                        ])->execute();
                    }
                }

                $specIDs = [];
                if (isset($_POST['ItemSpecification']) && is_array($_POST['ItemSpecification'])) {
                    foreach ($_POST['ItemSpecification'] as $i => $specs) {
                        $specIDs = ArrayHelper::merge($specIDs, array_filter(ArrayHelper::getColumn($specs, 'id')));

                        foreach ($specs as $index => $spec) {
                            $data['ItemSpecification'] = $spec;
                            $modelSpecification = (isset($spec['id']) && isset($oldSpecification[$spec['id']])) ? $oldSpecification[$spec['id']] : new ItemSpecification();
                            $modelSpecification->load($data);
                            $modelSpecifications[$i][$index] = $modelSpecification;
                            $valid = $modelSpecification->validate();
                        }
                    }
                }


                $oldSpecIDs = ArrayHelper::getColumn($oldSpecification, 'id');
                $deletedSpecIDs = array_diff($oldSpecIDs, $specIDs);

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedIDs)) {
                                PrItems::deleteAll(['id' => $deletedIDs]);
                            }

                            if (!empty($deletedSpecIDs)) {
                                ItemSpecification::deleteAll(['id' => $deletedSpecIDs]);
                            }

                            foreach ($modeldescription as $i => $modelotherdescription) {
                                if ($flag === false) {
                                    break;
                                }

                                $modelotherdescription->pr_id = $model->id;
                                $modelotherdescription->status = 58;

                                if (!($flag = $modelotherdescription->save(false))) {
                                    break;
                                }

                                if (isset($modelSpecifications[$i]) && is_array($modelSpecifications[$i])) {
                                    foreach ($modelSpecifications[$i] as $index => $modelSpecification) {
                                        $modelSpecification->item_id = $modelotherdescription->id;
                                        if (!($flag = $modelSpecification->save(false))) {
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            $itemHistorylog = new ItemHistoryLogs();
                            $itemHistorylog->item_id = $modelotherdescription->id;
                            $itemHistorylog->action_date = date('Y-m-d h:i');
                            $itemHistorylog->action_status = $modelotherdescription->status;
                            $itemHistorylog->action_by = Yii::$app->user->identity->id;
                            $itemHistorylog->save();

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;
                            $historylog->remarks = 'END USER - Updated PR';
                            $historylog->save();

                            // var_dump($model);die;
                            $transaction->commit();
                            return $this->redirect(['pending-request-index', 'id' => $_GET['id']]);
                        }

                        $transaction->rollBack();
                        Yii::error('Model save failed: ' . json_encode($model->errors), 'error');
                        throw new \Exception('Model save failed.');
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::error('Exception during save: ' . $e->getMessage(), 'error');
                        throw $e;
                    }
                } else {
                    throw new \Exception('Validation failed.');
                }
            }

            return $this->render('pr_update_', [
                'modelFullName' => $modelFullName,
                'model' => $model,
                'modeldescription' => (empty($modeldescription)) ? [new PrItems] : $modeldescription,
                'modelSpecifications' => (empty($modelSpecifications)) ? [new ItemSpecification()] : $modelSpecifications,
                'file_preview' => $file_preview,
                'file_config' => $file_config,
                'inputFiles' => $inputFiles,
                'modelEnduser' => $modelEnduser
            ]);
        } catch (\Exception $e) {
            Yii::error('Exception in actionPurchaserequestUpdate: ' . $e->getMessage(), 'error');
            throw $e;
        }
    }

    public function actionBacAddlrequestUpdate($id)
    {
        try {
            $model = $this->findModel($id);
            $modeldescription = $model->prItems;
            $modelName = Profile::find()->where(['id' => $model->requested_by])->one();
            $modelFullName = ($modelName !== null) ? ($modelName->fname . ' ' . $modelName->lname) : null;
            $modelSpecifications = [];
            $oldSpecification = [];
            $modelEnduser = PrEnduser::find()->where(['pr_id' => $model->id])->all();
            $historylog = new HistoryLog();
            $inputFiles = $model->inputfile;
            $file_preview = [];
            $file_config = [];

            foreach ($inputFiles as $inputfile) {
                $imgUrl = Url::to('@web/') . $inputfile->file_directory;
                $file_preview[] = $imgUrl;
                $file_config[] = array("type" => $inputfile->file_extension, "caption" => $inputfile->file_name, 'key' => $inputfile->id);
            }

            if (!empty($modeldescription)) {
                foreach ($modeldescription as $i => $modelotherdescription) {
                    $specs = $modelotherdescription->itemspecification;
                    $modelSpecifications[$i] = $specs;
                    $oldSpecification = ArrayHelper::merge(ArrayHelper::index($specs, 'id'), $oldSpecification);
                }
            }

            if ($model->load(Yii::$app->request->post())) {
                // if ($model->fund_source_id == 2) {
                //     $model->charge_to = "";
                // }
                // if ($model->charge_to == null) {
                //     $requested_by = $model->requested_by2;
                // } else {
                //     $requested_by = $model->requested_by1;
                // }
                // $model->requested_by = $requested_by;
                // $model->status = 61;

                
                switch ($model->fund_source_id) {
                    case 2:
                        $requested_by = $model->requested_by2;
                        break;
                    case 1:
                        if ($model->indirect_direct_cost === null) {
                            $requested_by = $model->requested_by1;
                        } elseif ($model->indirect_direct_cost == 0) {
                            $requested_by = $model->requested_by1;
                        } else {
                            $requested_by = $model->requested_by3;
                        }
                        break;
                    default:
                        $requested_by = null;
                }

                $approvedby = User::find()->where(['id' => $model->approved_by])->one();
                $division = Division::find()->where(['id' => $approvedby->division_id])->one();
                $model->division = $division->id;
                $model->status = 61;
                $model->save();

                if (!$model->save()) {
                    Yii::error('Model save failed: ' . json_encode($model->errors), 'error');
                    throw new \Exception('Model save failed.');
                }

                $oldIDs = ArrayHelper::map($modeldescription, 'id', 'id');
                $modeldescription = Model::createMultiple(PrItems::classname(), $modeldescription);
                Model::loadMultiple($modeldescription, Yii::$app->request->post());
                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modeldescription, 'id', 'id')));

                $valid = $model->validate();
                $valid = Model::validateMultiple($modeldescription) && $valid;

                $inputFiles = UploadedFile::getInstances($model, 'file');

                foreach ($inputFiles as $inputFile) {
                    $path = 'uploads/pr_files/' . md5($inputFile->baseName . date("m/d/y G:i:s:u")) . '.' . $inputFile->extension;

                    if ($inputFile->saveAs($path)) {
                        $file_name = $inputFile->baseName . '.' . $inputFile->extension;
                        $file_directory = $path;
                        $pr_id = $model->id;
                        $file_extension = $inputFile->extension;

                        if (!empty($deletedIDs_gpf)) {
                            Attachments::deleteAll(['id' => $deletedIDs_gpf]);
                        }
                        switch ($inputFile->extension) {
                            case "pdf":
                                $file_extension = "pdf";
                                break;
                            case "docx":
                                $file_extension = $inputFile->extension;
                                break;
                            case "xlsx":
                                $file_extension = $inputFile->extension;
                                break;
                            default:
                                $file_extension = "image";
                        }
                        Yii::$app->itdidb_procurement_system->createCommand()->insert('attachments', [
                            'file_name' => $file_name,
                            'file_directory' => $file_directory,
                            'pr_id' => $pr_id,
                            'file_extension' => $file_extension,
                        ])->execute();
                    }
                }

                $specIDs = [];
                if (isset($_POST['ItemSpecification']) && is_array($_POST['ItemSpecification'])) {
                    foreach ($_POST['ItemSpecification'] as $i => $specs) {
                        $specIDs = ArrayHelper::merge($specIDs, array_filter(ArrayHelper::getColumn($specs, 'id')));

                        foreach ($specs as $index => $spec) {
                            $data['ItemSpecification'] = $spec;
                            $modelSpecification = (isset($spec['id']) && isset($oldSpecification[$spec['id']])) ? $oldSpecification[$spec['id']] : new ItemSpecification();
                            $modelSpecification->load($data);
                            $modelSpecifications[$i][$index] = $modelSpecification;
                            $valid = $modelSpecification->validate();
                        }
                    }
                }

                $oldSpecIDs = ArrayHelper::getColumn($oldSpecification, 'id');
                $deletedSpecIDs = array_diff($oldSpecIDs, $specIDs);

                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            if (!empty($deletedIDs)) {
                                PrItems::deleteAll(['id' => $deletedIDs]);
                            }

                            if (!empty($deletedSpecIDs)) {
                                ItemSpecification::deleteAll(['id' => $deletedSpecIDs]);
                            }

                            foreach ($modeldescription as $i => $modelotherdescription) {
                                if ($flag === false) {
                                    break;
                                }

                                $modelotherdescription->pr_id = $model->id;
                                $modelotherdescription->status = 61;

                                if (!($flag = $modelotherdescription->save(false))) {
                                    break;
                                }

                                if (isset($modelSpecifications[$i]) && is_array($modelSpecifications[$i])) {
                                    foreach ($modelSpecifications[$i] as $index => $modelSpecification) {
                                        $modelSpecification->item_id = $modelotherdescription->id;
                                        if (!($flag = $modelSpecification->save(false))) {
                                            break;
                                        }
                                    }
                                }
                            }
                            $itemHistorylog = new ItemHistoryLogs();
                            $itemHistorylog->item_id = $modelotherdescription->id;
                            $itemHistorylog->action_date = date('Y-m-d h:i');
                            $itemHistorylog->action_status = $modelotherdescription->status;
                            $itemHistorylog->action_by = Yii::$app->user->identity->id;
                            $itemHistorylog->save();

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;
                            $historylog->remarks = 'END USER - Updated PR';
                            $historylog->save();

                            $transaction->commit();
                            return $this->redirect(['on-process-request-index', 'id' => $_GET['id']]);
                        }

                        $transaction->rollBack();
                        Yii::error('Model save failed: ' . json_encode($model->errors), 'error');
                        throw new \Exception('Model save failed.');
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        Yii::error('Exception during save: ' . $e->getMessage(), 'error');
                        throw $e;
                    }
                } else {
                    throw new \Exception('Validation failed.');
                }
            }

            return $this->render('pr_update_', [
                'modelFullName' => $modelFullName,
                'model' => $model,
                'modeldescription' => (empty($modeldescription)) ? [new PrItems] : $modeldescription,
                'modelSpecifications' => (empty($modelSpecifications)) ? [new ItemSpecification()] : $modelSpecifications,
                'file_preview' => $file_preview,
                'file_config' => $file_config,
                'inputFiles' => $inputFiles,
                'modelEnduser' => $modelEnduser
            ]);
        } catch (\Exception $e) {
            Yii::error('Exception in actionPurchaserequestUpdate: ' . $e->getMessage(), 'error');
            throw $e;
        }
    }

    // attachments delete 
    public function actionPrUpdatedeleteFile()
    {
        $id = $_POST['key'];
        $attach = Attachments::findOne($id);
        $attach->archived = 1;
        $attach->save();
        return $this->asJson(['success' => 'true']);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['pending-request-index']);
    }

    protected function findModel($id)
    {
        if (($model = PurchaseRequest::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //depdrop in create PR
    public function actionSection()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $div_id = $parents[0];
                $out = Section::getSection($div_id);

                return ['output' => $out, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    //depdrop in create PR
    public function actionRequestedby()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $charge_to = $parents[0];

                $list = PbwLibPs::getPsId($charge_to);

                $max = max(array_keys($list));

                for ($i = 0; $max >= $i; $i++) {
                    $out = Profile::find()->where(['id' => $list[$i]['name']])->one();

                    $data = ['id' => $out['id'], 'name' => $out['fname'] . ' ' . $out['lname']];

                    $try[] = $data;
                }

                return ['output' => $try, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    public function actionRequestedbydirectcost()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        $try = [];

        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $charge_to = $parents[0];
                $list = PbwLibPs::getPsId($charge_to);
                $max = max(array_keys($list));

                // Fetch profiles by ID from $list
                $profileIds = array_column($list, 'name');
                $profiles = Profile::find()->where(['id' => $profileIds])->all();

                foreach ($profiles as $profile) {
                    $data = ['id' => $profile->id, 'name' => $profile->fname . ' ' . $profile->lname];
                    $try[] = $data;
                }

                // Ensure profiles with specific user IDs are included
                $userProfiles = Profile::find()->where(['user_id' => [36, 38, 249, 53, 296, 31]])->all();

                foreach ($userProfiles as $userProfile) {
                    $data = ['id' => $userProfile->id, 'name' => $userProfile->fname . ' ' . $userProfile->lname];
                    $try[] = $data;
                }

                return ['output' => $try, 'selected' => ''];
            }
        }

        return ['output' => '', 'selected' => ''];
    }


    // depdrop in create PR
    public function actionProfile()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];
            if ($parents != null) {
                $charge_to = $parents[0];

                $list = PbwLibPs::getPsId($charge_to);

                $max = max(array_keys($list));

                for ($i = 0; $max >= $i; $i++) {
                    $out = Profile::find()->where(['id' => $list[$i]['name']])->one();

                    $data = ['id' => $out['id'], 'name' => $out['fname'] . ' ' . $out['lname']];

                    $try[] = $data;
                }

                return ['output' => $try, 'selected' => ''];
            }
        }
        return ['output' => '', 'selected' => ''];
    }

    //expandrow in index
    public function actionPrItems()
    {
        if (isset($_POST['expandRowKey'])) {
            $model = \app\modules\PurchaseRequest\models\PrItems::findOne($_POST['expandRowKey']);
            return $this->renderPartial('pr_items_expand', ['model' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }

    //expandrow in item index for item specification
    public function actionItemspecsExpand()
    {
        if (isset($_POST['expandRowKey'])) {
            $model = \app\modules\PurchaseRequest\models\ItemSpecification::findOne($_POST['expandRowKey']);
            return $this->renderPartial('pr_itemspecs_expand', ['model' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }

    // generate pdf
    public function actionPurchaserequestPdf()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        // $profile = Profile::find()
        //     ->where(['id' => $model->approved_by])
        //     ->one();

        $description = ArrayHelper::map(
            PrItems::find()
                ->where(['pr_id' => $_GET['id']])
                ->all(),
            'id',
            function ($model) {
                return $model['unit'] . " - " . $model['item_name'];
            }
        );

        $pdf_content = $this->renderPartial('pr_pdf', [
            'model' => $model,
            'description' => $description,

        ]);

        $sanitizedHtml = $this->purifyHtml($pdf_content);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'filename' => 'ITDI-PURCHASE REQUEST FORM',
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $sanitizedHtml,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => [
                'title' => 'ITDI-PURCHASE REQUEST FORM',
            ],
            'methods' => [
                'SetTitle' => 'ITDI-PURCHASE REQUEST FORM',
                'SetFooter' => ['|Page {PAGENO} of {nb}|'],
            ],
        ]);

        return $pdf->render();
    }

    // Helper function for HTML Purification
    protected function purifyHtml($html)
    {
        return HtmlPurifier::process($html);
    }


    public function actionPurchaserequestSubmit()
    {
        $transaction = Yii::$app->db->beginTransaction();

        try {
            $id = Yii::$app->request->get('id');

            $model = PurchaseRequest::findOne($id);

            if ($model === null) {
                throw new NotFoundHttpException('The requested purchase request does not exist.');
            }

            $items = PrItems::find()
                ->where(['pr_id' => $model->id])
                ->all();

            $model->status = 1;

            if ($model->save()) {
                foreach ($items as $item) {
                    $item->status = 1;
                    $item->save();

                    $itemHistorylog = new ItemHistoryLogs();
                    $itemHistorylog->item_id = $item->id;
                    $itemHistorylog->action_date = date('Y-m-d h:i');
                    $itemHistorylog->action_status = $item->status;
                    $itemHistorylog->action_by = Yii::$app->user->id;
                    $itemHistorylog->save();
                }

                $historylog = new HistoryLog();
                $historylog->pr_id = $model->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->action_status = $model->status;
                $historylog->action_user_id = Yii::$app->user->id;
                $historylog->save();

                $transaction->commit();

                Yii::$app->session->setFlash('success', 'Success');
            } else {
                throw new Exception('Failed to update the purchase request status.');
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['pending-request-index']);
    }

    //list of archived 
    public function actionArchive()
    {
        $searchModel = new PurchaseRequestSearch();
        $params = Yii::$app->request->queryParams;

        $params['PurchaseRequestSearch']['status'] = '31';

        $dataProvider = $searchModel->archive($params);

        return $this->render('pr_archive', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }


    public function actionPurchaserequestCancel()
    {
        try {
            $id = Yii::$app->request->post('id');

            $model = PurchaseRequest::find()
                ->where(['id' => $id])
                ->one();

            if ($model === null) {
                throw new \Exception('Error: Unable to Delete the Purchase Request');
            }

            $descriptions = PrItems::find()
                ->where(['pr_id' => $model['id']])
                ->all();

            $historylog = new HistoryLog();
            $itemHistoryLog = new ItemHistoryLogs();

            $model->status = 31;
            if (!$model->save()) {
                throw new \Exception('Failed to update purchase request status.');
            }

            foreach ($descriptions as $description) {
                $description->status = 31;
                if (!$description->save()) {
                    throw new \Exception('Failed to update item status.');
                }

                $itemHistoryLog->item_id = $description->id;
                $itemHistoryLog->action_date = date('Y-m-d h:i');
                $itemHistoryLog->action_status = $description->status;
                $itemHistoryLog->action_by = Yii::$app->user->identity->id;
                if (!$itemHistoryLog->save()) {
                    throw new \Exception('Failed to log item history.');
                }
            }

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->remarks = $_POST['remarks'];
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;

            if (!$historylog->save()) {
                throw new \Exception('Failed to log history.');
            }

            return $this->redirect(['pending-request-index']);
        } catch (\Exception $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
            return $this->redirect(['pending-request-index']);
        }
    }

    // modal for cancel item, 
    public function actionPurchaserequestItemcancel()
    {
        $description = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $itemHistoryLog = new ItemHistoryLogs();

        return $this->renderAjax(
            'pr_modal_remarks',
            [
                'itemHistoryLog' => $itemHistoryLog,
                'description' => $description,
                'model' => $model
            ]
        );
    }

    // cancel item 
    // public function actionPurchaserequestItemcancelsaved($rtn)
    // {
    //     $items = PrItems::find()
    //         ->where(['id' => $rtn])
    //         ->one();

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $items['pr_id']])
    //         ->one();

    //     $bidding = BiddingList::find()
    //         ->where(['item_id' => $items['id']])
    //         ->all();

    //     $itemHistoryLog = new ItemHistoryLogs();
    //     $historylog = new HistoryLog();

    //     $items->status = 18;
    //     $purchaserequest->status = 18;
    //     $items->save();
    //     $purchaserequest->save();

    //     foreach ($bidding as $bid) {
    //         $bid->status = 18;
    //         $bid->save();
    //     }

    //     $test = ($bidding != NULL ? $bid : $items);
    //     $test->save();

    //     if ($test->save()) {

    //         $itemHistoryLog->item_id = $items->id;
    //         $itemHistoryLog->action_date = date('Y-m-d h:i');
    //         $itemHistoryLog->action_status = $items->status;
    //         $itemHistoryLog->action_by = Yii::$app->user->identity->id;
    //         $itemHistoryLog->action_remarks =  $itemHistoryLog->action_remarks;
    //         $itemHistoryLog->save();
    //     }

    //         $historylog->pr_id = $purchaserequest->id;
    //          $historylog->action_date = date('Y-m-d h:i');
    //         $historylog->action_status = $purchaserequest->status;
    //         $historylog->action_user_id = Yii::$app->user->identity->id;
    //         $historylog->remarks = 'CANCELLED ITEM';

    //         $historylog->save();


    //         return $this->redirect(Yii::$app->request->referrer);
    //         // $this->redirect(['index']);

    // }

    public function actionPurchaserequestItemcancelsaved($rtn)
    {
        $items = PrItems::find()
            ->where(['id' => $rtn])
            ->one();


        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $bidding = BiddingList::find()
            ->where(['item_id' => $items['id']])
            ->all();

        $items->status = 18;
        $purchaserequest->status = 18;
        $items->save();
        $purchaserequest->save();

        foreach ($bidding as $bid) {
            $bid->status = 18;
            $bid->save();
        }

        $items->save();
        $transaction = Yii::$app->db->beginTransaction();

        try {
            // Save ItemHistoryLogs
            $itemHistoryLog = new ItemHistoryLogs();
            $itemHistoryLog->item_id = $items->id;
            $itemHistoryLog->action_date = date('Y-m-d h:i');
            $itemHistoryLog->action_status = $items->status;
            $itemHistoryLog->action_by = Yii::$app->user->identity->id;
            $itemHistoryLog->action_remarks = 'CANCELLED ITEM';
            $itemHistoryLog->save();

            // Save HistoryLog
            $historylog = new HistoryLog();
            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'CANCELLED ITEM';
            $historylog->save();

            $transaction->commit();

            return $this->redirect(Yii::$app->request->referrer);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }


    // create item details for bid bulletin (item specs)
    public function actionPrItemsbidbulletinCreate()
    {
        $model = $_GET;
        $itemSpec = ItemSpecification::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $item = PrItems::find()
            ->where(['id' => $itemSpec->item_id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $item['pr_id']])->one();

        $itemHistoryLog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $item->status = 36;
        $purchaserequest->status = 36;

        if ($itemSpec->load(Yii::$app->request->post())) {

            $itemSpec->item_id = $item->id;
            $itemSpec->bidbulletin_status = 1;
            $itemSpec->save();

            if ($itemSpec->save(false)) {

                $itemHistoryLog->item_id = $item->id;
                $itemHistoryLog->action_date = date('Y-m-d h:i');
                $itemHistoryLog->action_status = $item->status;
                $itemHistoryLog->action_by = Yii::$app->user->identity->id;

                $historylog->pr_id = $purchaserequest->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->action_status = $purchaserequest->status;
                $historylog->action_user_id = Yii::$app->user->identity->id;
                $historylog->remarks = 'End-user - Added Item Details for Bid Bulletin';

                $historylog->save();
                $itemHistoryLog->save();
                $purchaserequest->save();
                $item->save();
            }

            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('pr_items_bidbulletin_specs', [
            'model' => $model,
            'itemSpec' => $itemSpec,
            'historylog' => $historylog,
        ]);
    }

    public function actionBidbulletinNotifyEnduser()
    {
        $id = Yii::$app->request->post('id');
        $remarks = Yii::$app->request->post('remarks');

        $model = PurchaseRequest::findOne(['id' => $id]);

        if ($model === null) {
            throw new NotFoundHttpException('Purchase request not found.');
        }

        $model->status = 54;

        $items = PrItems::find()
            ->where(['pr_id' => $model->id])
            ->all();

        $transaction = Yii::$app->db->beginTransaction();

        try {
            foreach ($items as $item) {
                $item->status = 54;
                $itemLogs = new ItemHistoryLogs([
                    'item_id' => $item->id,
                    'action_date' => date('Y-m-d H:i'),
                    'action_remarks' => $remarks,
                    'action_status' => $item->status,
                    'action_by' => Yii::$app->user->identity->id,
                ]);

                if (!$item->save() || !$itemLogs->save()) {
                    throw new Exception('Error saving item or item history log.');
                }
            }

            $historylog = new HistoryLog([
                'pr_id' => $model->id,
                'action_date' => date('Y-m-d H:i'),
                'remarks' => $remarks,
                'action_status' => $model->status,
                'action_user_id' => Yii::$app->user->identity->id,
            ]);

            if ($model->save() && $historylog->save()) {
                $transaction->commit();
                Yii::$app->getSession()->setFlash('success', 'Purchase request status updated.');
            } else {
                $transaction->rollBack();
                Yii::$app->getSession()->setFlash('error', 'Failed to update status.');
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::error($e->getMessage());
            Yii::$app->getSession()->setFlash('error', 'An error occurred while processing your request.');
        }

        return $this->redirect(['purchase-request/bac-request-index']);
    }

    // create BAC bid bulletin (item specs)
    public function actionBacBidbulletinCreate()
    {
        $bidbulletin = new BidBulletin();
        $selectedKeys = Yii::$app->request->get('keys');
        $item = PrItems::find()->where(['id' => $selectedKeys])->one();
        $sample = PrItems::find()
            ->where(['id' => $selectedKeys])->asArray()
            ->all();

        $selectedItems = $sample;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $sample,
        ]);

        if ($bidbulletin->load(Yii::$app->request->post())) {

            $models = Model::createMultiple(BidBulletin::className());
            Model::loadMultiple($models, Yii::$app->request->post());

            $listData = ArrayHelper::map($sample, 'id', function ($model) {
                return $model['id'];
            });
            $itemUpdates = PrItems::find()->select(['id', 'bidbulletin_id'])->where(['id' => $listData])->all();

            // var_dump($bidbulletin);die;
            foreach ($models as $modelbidbulletin) {

                $modelbidbulletin->bidbulletin_no = $bidbulletin->bidbulletin_no;
                $modelbidbulletin->date_posted =  $bidbulletin->date_posted;
                $modelbidbulletin->save();

                foreach ($itemUpdates as $itemUpdate) {
                    $itemSpecs = ItemSpecification::find()->where(['item_id' => $itemUpdate->id])->andWhere(['bidbulletin_status' => 2])->all();

                    $itemUpdate->bidbulletin_id = $modelbidbulletin->id;
                    $itemUpdate->save();

                    foreach ($itemSpecs as $itemSpec) {
                        $itemSpec->bidbulletin_status = 5;
                        $itemSpec->save();
                    }
                }

                return $this->redirect(Yii::$app->request->referrer);
                // return $this->redirect(['bac-bidbulletin-pdf', 'id' => $item['bidbulletin_id']]);
            }
        }

        return $this->renderAjax('pr_items_bidbulletin_create', [
            'item' => $item,
            'dataProvider' => $dataProvider,
            'selectedItems' => $selectedItems,
            'bidbulletin' => $bidbulletin
            // 'prItem' => $prItem
        ]);
    }

    // revised / update bid bulletin (item specs)
    public function actionPrItemsbidbulletinUpdate()
    {
        $itemSpec = ItemSpecification::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $item = PrItems::find()
            ->where(['id' => $itemSpec->item_id])
            ->one();

        if ($itemSpec->load(Yii::$app->request->post())) {

            $itemSpec->bidbulletin_status = 4;
            $itemSpec->item_id = $item->id;
            $itemSpec->save();

            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('pr_items_bidbulletin_specs', [
            'itemSpec' => $itemSpec,
        ]);
    }

    // display bulletin (bac)
    public function actionPrItemsbidbulletinlist()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $model['id']])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
        ]);

        return $this->render('pr_items_bidbulletin_index', [
            'dataProvider' => $dataProvider,
            'items' => $items,
            'model' => $model
        ]);
    }

    // display accepted bulletin (bac)
    public function actionBidbulletinAcceptedlist()
    {
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->acceptedBidbulletin(Yii::$app->request->queryParams);
        $model = new PrItems();

        return $this->render('pr_items_bidbulletin_acceptedlist', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    // accept btn in list of bid bulletin tab (bac)
    public function actionAcceptbulletin()
    {
        $itemSpec = ItemSpecification::find()->where(['id' => $_GET['id']])->one();

        $item = PrItems::find()
            ->where(['id' => $itemSpec->item_id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $item->pr_id])
            ->one();

        $itemHistorylog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $item->status = 38;
        $itemSpec->bidbulletin_status = 2;

        if ($itemSpec->save()) {

            $itemHistorylog->item_id = $item->id;
            $itemHistorylog->action_date = date('Y-m-d h:i');
            $itemHistorylog->action_status = $item->status;
            $itemHistorylog->action_by = Yii::$app->user->identity->id;
            $itemHistorylog->action_remarks = 'Accepted';

            $historylog->pr_id = $item->pr_id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Item Details for Bid Bulletin Accepted';

            $historylog->save();
            $itemHistorylog->save();
            $itemSpec->save();
            $item->save();
            $purchaserequest->save();

            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // decline button in bid builletin tab (bac)
    public function actionDeclinebulletin()
    {
        $id = Yii::$app->request->post('id');

        $itemSpec = ItemSpecification::find()
            ->where(['id' => $id])
            ->one();

        $item = PrItems::find()
            ->where(['id' => $itemSpec->item_id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $item->pr_id])
            ->one();

        $itemHistorylog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $item->status = 38;
        $itemSpec->bidbulletin_status = 3;

        if ($itemSpec->save()) {
            $itemSpec->bidbulletin_remarks = $_POST['remarks'];

            $itemHistorylog->item_id = $item->id;
            $itemHistorylog->action_date =  date('Y-m-d h:i');
            $itemHistorylog->action_status = $item->status;
            $itemHistorylog->action_by = Yii::$app->user->identity->id;
            $itemHistorylog->action_remarks = 'Declined';

            $historylog->pr_id = $item->pr_id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Bid Bulletin Declined';

            $historylog->save();
            $itemHistorylog->save();
            $itemSpec->save();
            $purchaserequest->save();
            $item->save();

            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // pdf bidbulletin form
    public function actionBacBidbulletinPdf()
    {
        $bidbulletin = BidBulletin::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $prItems = PrItems::find()
            ->where(['bidbulletin_id' => $bidbulletin['id']])
            ->all();

        $item = PrItems::find()
            ->where(['bidbulletin_id' => $bidbulletin['id']])
            ->one();

        $itemSpecs = ItemSpecification::find()
            ->where(['item_id' => $item->id])
            ->andWhere(['bidbulletin_status' => '5'])
            ->all();

        $listData = ArrayHelper::map($prItems, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $testPr = PurchaseRequest::find()
            ->select(['id', 'pr_no'])
            ->where(['id' => $listData])
            ->all();

        $listQuote = ArrayHelper::map($testPr, 'id', function ($model) {
            return $model['id'];
        });

        $testQuote = Quotation::find()
            ->where(['pr_id' => $listQuote])
            ->andWhere(['option_id' => '4'])
            ->one();

        // computation of time less 30mins from opening date
        $openingDate = $testQuote->option_date;
        $openingTime = strtotime($openingDate);
        $calculateTime = $openingTime - (30 * 60);
        $submissionTime = date("Y-m-d H:i:s", $calculateTime);

        // computation of time add 1min from opening date
        $openingDate2 = $testQuote->option_date;
        $openingTime2 = strtotime($openingDate2);
        $calculateTime2 = $openingTime2 + (1 * 60);
        $openingTime = date("Y-m-d H:i:s", $calculateTime2);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $prItems,
        ]);

        $pdf_content = $this->renderPartial('pr_items_bidbulletin_pdf', [
            'prItems' => $prItems,
            'item' => $item,
            'dataProvider' => $dataProvider,
            'bidbulletin' => $bidbulletin,
            'testQuote' => $testQuote,
            'submissionTime' => $submissionTime,
            'openingTime' => $openingTime,
            'itemSpecs' => $itemSpecs
        ]);

        $head = '
            <div>
            <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"/>
            </div>
         ';

        $foot = '
            <div style="text-align:center;">
            <br>
                <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
            </div> 
         ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-BAC Bid Bulletin Form',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 40,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-BAC Bid Bulletin Form',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // list of pr (chief)
    public function actionChiefRequestIndex()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->chief(Yii::$app->request->queryParams);

        $model = PurchaseRequest::find()->where(['status' => ['1', '41']])->all();
        $count = PurchaseRequest::find()->select(['id'])->where(['id' => $model])->distinct();
        $countPr = $count->count();

        return $this->render('chief_pr_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'countPr' => $countPr
        ]);
    }

    public function actionChiefMonitoringList()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->chiefMonitoring(Yii::$app->request->queryParams);

        return $this->render('/purchase-request/chief_pr_monitoring_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // list of items tab (chief)
    public function actionChiefRequestItems()
    {
        $model = new PrItems();
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->chiefIndex(Yii::$app->request->queryParams);

        return $this->render('chief_pr_items_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    public function actionChiefPrview()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->all();

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
        ]);

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        return $this->render('chief_pr_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
        ]);
    }

    // approved btn in view page
    public function actionChiefPrapproved($rtn)
    {
        $historylog = new HistoryLog();

        try {
            $model = PurchaseRequest::findOne($rtn);

            if (!$model) {
                throw new \Exception('Purchase request not found.');
            }

            $description = PrItems::findAll(['pr_id' => $model->id]);
            $approvedby = User::findOne($model->approved_by);

            if (!$approvedby) {
                throw new \Exception('User not found.');
            }

            $division = Division::findOne($approvedby->division_id);

            if (!$division) {
                throw new \Exception('Division not found.');
            }

            $sequence = DivisionFormSequence::findOne(['division_code' => $division->division_code]);

            if (!$sequence) {
                throw new \Exception('Division form sequence not found.');
            }

            $isNewPR = $model->pr_no == null;

            if ($isNewPR) {
                $prnumber = $division->division_code . '-' . date('Y') . '-' . date('m') . '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
                $sequence->form_sequence += 1;
                $model->pr_no = $prnumber;
                $model->save();
            }

            $model->status = 2;
            $model->save();

            foreach ($description as $descrip) {
                if ($descrip->status != 18) {
                    $descrip->status = '2';
                    $descrip->save(false);
                }

                $itemHistorylog = new ItemHistoryLogs();
                $itemHistorylog->item_id = $descrip->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $descrip->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->save();

            $sequence->save();

            Yii::$app->getSession()->setFlash('success', 'Approved');
            return $this->redirect(['chief-request-index']);
        } catch (\Exception $e) {
            Yii::$app->getSession()->setFlash('error', $e->getMessage());
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // disapproved btn in view page
    public function actionChiefPrdisapproved()
    {
        $id = Yii::$app->request->post('id');

        $model = PurchaseRequest::find()
            ->where(['id' => $id])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $model['id']])
            ->all();

        $historylog = new HistoryLog();
        $model->status = 3;

        foreach ($items as $item) {
            $item->status = '3';

            $itemHistorylog = new ItemHistoryLogs();
            $itemHistorylog->item_id = $item->id;
            $itemHistorylog->action_date = date('Y-m-d h:i');
            $itemHistorylog->action_status = $item->status;
            $itemHistorylog->action_by = Yii::$app->user->identity->id;

            $item->save(false);
            $itemHistorylog->save();
        }

        if ($model->save()) {

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->remarks = $_POST['remarks'];
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->save();

            Yii::$app->getSession()->setFlash('danger', 'Disapproved Request');
        }
        return $this->redirect(['chief-request-index', 'id' => $model->id]);
    }

    // FMD PROCESS
    // list of pending pr (budget)
    public function actionBudgetPendingRequestIndex()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->budget(Yii::$app->request->queryParams);
        $model = new PurchaseRequest;

        $modelPr = PurchaseRequest::find()->where(['status' => ['2', '51']])->andWhere(['pr_type_id' => 3])->all();
        $count = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPr])->distinct();
        $countPr = $count->count();

        if (Yii::$app->request->post('hasEditable')) {
            $_id = $_POST['editableKey'];
            $model = $this->findModel($_id);

            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['PurchaseRequest']);
            $post['PurchaseRequest'] = $posted;

            $model->pr_type_id = Yii::$app->request->post('pr_type_id');

            if ($model->load($post)); {
                $model->save();
                $output = Yii::$app->request->post('pr_type_id');

                if (isset($posted['pr_type_id'])) {
                    $output =  $model->pr_type_id;
                }
                $out = Json::encode(['output' => $output, 'message' => '']);
            }

            echo $out;

            return $this->redirect(Yii::$app->request->referrer);
            //    return $this->redirect(['/adm-bac/bac_pr_index', 'id' => $model->id]);
        }
        return $this->render('/fmd-budget/budget_pr_pending_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
            'countPr' => $countPr
        ]);
    }

    // Request to End user
    public function actionFmdRequestBtn()
    {
        $id = Yii::$app->request->post('id');
        $model = PurchaseRequest::find()
            ->where(['id' => $id])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $model->id])
            ->all();

        $historylog = new HistoryLog();
        $itemHistoryLog = new ItemHistoryLogs();

        $model->status = 59;
        $model->save();

        foreach ($description as $descrip) {
            $itemHistoryLog = new ItemHistoryLogs();
            $descrip->status = 59;

            $itemHistoryLog->item_id = $descrip->id;
            $itemHistoryLog->action_date = date('Y-m-d h:i');
            $itemHistoryLog->action_status = $descrip->status;
            $itemHistoryLog->action_by = Yii::$app->user->identity->id;

            $descrip->save();
            $itemHistoryLog->save();
        }

        $historylog->pr_id = $model->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $model->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = $_POST['remarks'];

        $historylog->save();


        return $this->redirect(['budget-pending-request-index']);
    }

    // pending request 
    public function actionBudgetMonitoringList()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->budgetMonitoring(Yii::$app->request->queryParams);

        return $this->render('/fmd-budget/budget_pr_monitoring_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    public function actionBudgetPendingRequestItems()
    {
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->fmdIndex(Yii::$app->request->queryParams);

        return $this->render('/fmd-budget/budget_pr_items_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // list of approved or on process pr (budget)
    public function actionBudgetApprovedRequestIndex()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->budgetApproved(Yii::$app->request->queryParams);
        $model = new PurchaseRequest;

        $modelPr = PurchaseRequest::find()->where(['status' => 2])->andWhere(['pr_type_id' => 3])->all();
        $count = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPr])->distinct();
        $countPr = $count->count();

        return $this->render('/fmd-budget/budget_pr_approved_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
            'countPr' => $countPr
        ]);
    }

    public function actionBudgetApprovedRequestItems()
    {
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->fmdapprovedIndex(Yii::$app->request->queryParams);

        return $this->render('/fmd-budget/budget_approved_request_items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // view btn
    public function actionBudgetPrview()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        return $this->render('/fmd-budget/budget_pr_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrBudgetmonitoring($id)
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $description,
        ]);

        return $this->render('/fmd-budget/budget_pr_items_budgetmonitoring', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    // approved btn in view page
    public function actionBudgetPrapproved($rtn)
    {
        $historylog = new HistoryLog();

        $model = PurchaseRequest::find()
            ->where(['id' => $rtn])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $model['id']])
            ->all();

        $model->approved_by = Yii::$app->user->identity->id;
        $model->status = 4;

        if ($model->save()) {

            foreach ($description as $descrip) {
                if ($descrip->status != 18) {
                    $descrip->status = '4';
                    $descrip->save(false);
                }

                $itemHistorylog = new ItemHistoryLogs();
                $itemHistorylog->item_id = $descrip->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $descrip->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                $itemHistorylog->save();
            }

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->save();

            Yii::$app->getSession()->setFlash('success', 'Approved');
        }
        return $this->redirect(['budget-pending-request-index']);
    }

    // disapproved item 
    public function actionBudgetPrdisapproved()
    {
        try {
            $id = Yii::$app->request->post('id');

            $model = PurchaseRequest::find()
                ->where(['id' => $id])
                ->one();

            if (!$model) {
                throw new \Exception('Purchase request not found.');
            }

            $items = PrItems::find()
                ->where(['pr_id' => $model->id])
                ->all();

            $historylog = new HistoryLog();
            $model->status = 42;

            foreach ($items as $item) {
                $item->status = '42';

                $itemHistorylog = new ItemHistoryLogs();
                $itemHistorylog->item_id = $item->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $item->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                if (!$item->save(false)) {
                    throw new \Exception('Failed to save item: ' . json_encode($item->errors));
                }

                if (!$itemHistorylog->save()) {
                    throw new \Exception('Failed to save item history log: ' . json_encode($itemHistorylog->errors));
                }
            }

            if ($model->save()) {
                $historylog->pr_id = $model->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->remarks = Yii::$app->request->post('remarks');
                $historylog->action_status = $model->status;
                $historylog->action_user_id = Yii::$app->user->identity->id;

                if (!$historylog->save()) {
                    throw new \Exception('Failed to save history log: ' . json_encode($historylog->errors));
                }

                Yii::$app->getSession()->setFlash('danger', 'Disapproved Request');
                return $this->redirect(['budget-pending-request-index', 'id' => $model->id]);
            } else {
                throw new \Exception('Failed to save purchase request: ' . json_encode($model->errors));
            }
        } catch (\Exception $e) {
            Yii::error('Exception in actionBudgetPrdisapproved: ' . $e->getMessage(), 'error');
            Yii::$app->getSession()->setFlash('error', 'An error occurred while disapproving the purchase request: ' . $e->getMessage());
            return $this->redirect(['budget-pending-request-index']);
        }
    }


    public function actionAccountingRequestIndex()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->accounting(Yii::$app->request->queryParams);
        $model = new PurchaseRequest;

        return $this->render('/fmd-accounting/accounting_pr_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    public function actionAccountingRequestItems()
    {
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->accountingIndex(Yii::$app->request->queryParams);

        return $this->render('/fmd-accounting/accounting_pr_items_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionAccountingPrview()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->all();

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        return $this->render('/fmd-accounting/accounting_pr_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrAccountingmonitoring()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $description,
        ]);

        return $this->render('/fmd-accounting/accounting_budgetmonitoring', [
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    // approved btn in view page
    public function actionAccountingPrapproved($accntngId)
    {
        $historylog = new HistoryLog();

        $model = PurchaseRequest::find()
            ->where(['id' => $accntngId])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $model['id']])
            ->all();

        $model->approved_by = Yii::$app->user->identity->id;
        $model->status = 5;

        if ($model->save()) {
            foreach ($description as $descrip) {

                $itemHistorylog = new ItemHistoryLogs();
                $descrip->status = '5';

                $itemHistorylog->item_id = $descrip->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $descrip->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                $descrip->save(false);
                $itemHistorylog->save();
            }

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;

            $historylog->save();
            return $this->redirect(['accounting-request-index']);
        }
    }

    // Request to End user
    public function actionBacAddlrequestBtn()
    {
        $id = Yii::$app->request->post('id');
        $model = PurchaseRequest::find()
            ->where(['id' => $id])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $model->id])
            ->all();

        $historylog = new HistoryLog();
        $itemHistoryLog = new ItemHistoryLogs();

        $model->status = 60;
        $model->save();

        foreach ($description as $descrip) {
            $itemHistoryLog = new ItemHistoryLogs();
            $descrip->status = 60;

            $itemHistoryLog->item_id = $descrip->id;
            $itemHistoryLog->action_date = date('Y-m-d h:i');
            $itemHistoryLog->action_status = $descrip->status;
            $itemHistoryLog->action_by = Yii::$app->user->identity->id;

            $descrip->save();
            $itemHistoryLog->save();
        }

        $historylog->pr_id = $model->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $model->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = $_POST['remarks'];

        $historylog->save();


        return $this->redirect(['budget-pending-request-index']);
    }

    // pending request 
    public function actionBacMonitoringList()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->bacMonitoring(Yii::$app->request->queryParams);

        return $this->render('/adm-bac/bac_monitoring_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    //    BAC process
    public function actionBacRequestIndex()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->bac(Yii::$app->request->queryParams);
        $model = new PurchaseRequest;

        $modelPr = PurchaseRequest::find()->where(['status' => ['4', '49']])->all();
        $count = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPr])->distinct();
        $countPr = $count->count();

        if (Yii::$app->request->post('hasEditable')) {
            $_id = $_POST['editableKey'];
            $model = $this->findModel($_id);

            $out = Json::encode(['output' => '', 'message' => '']);
            $post = [];
            $posted = current($_POST['PurchaseRequest']);
            $post['PurchaseRequest'] = $posted;

            $model->mode_pr_id = Yii::$app->request->post('mode_pr_id');

            if ($model->load($post)); {
                $model->save();
                $output = Yii::$app->request->post('mode_pr_id');

                if (isset($posted['mode_pr_id'])) {
                    $output =  $model->mode_pr_id;
                }
                $out = Json::encode(['output' => $output, 'message' => '']);
            }

            echo $out;
            return $this->redirect(Yii::$app->request->referrer);
            //    return $this->redirect(['/adm-bac/bac_pr_index', 'id' => $model->id]);
        }

        return $this->render('/adm-bac/bac_pr_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
            'countPr' => $countPr
        ]);
    }

    public function actionBacRequestItems()
    {
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->bacIndex(Yii::$app->request->queryParams);
        $model = new PrItems();

        return $this->render('/adm-bac/bac_pr_items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model

        ]);
    }

    public function actionBacPrview()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $model['id']])
            ->all();

        $item = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->one();

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
        ]);

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        $modelItemspec = ItemSpecification::find()->where(['item_id' => $item->id])->andWhere(['bidbulletin_status' => 1])->all();
        $count = PrItems::find()->select(['id'])->where(['id' => $modelItemspec])->distinct();
        $countPending = $count->count();

        return $this->render('/adm-bac/bac_pr_view', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'countPending' => $countPending
        ]);
    }

    public function actionBacPrreceived($rec)
    {
        $model = PurchaseRequest::find()->where(['id' => $rec])->one();

        $description = PrItems::find()->where(['pr_id' => $model['id']])->all();

        $historylog = new HistoryLog();

        $model->approved_by = Yii::$app->user->identity->id;
        $model->status = 7;

        if ($model->save()) {

            foreach ($description as $descrip) {

                $itemHistorylog = new ItemHistoryLogs();

                $descrip->status = '9';

                $itemHistorylog->item_id = $descrip->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $descrip->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                $descrip->save(false);
                $itemHistorylog->save();
            }

            $historylog->pr_id = $model->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $model->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Received';

            $historylog->save();
            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // tab-quotation view page
    public function actionBacQuotationindex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->all();

        $quotation = Quotation::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => Quotation::find()
                ->where(['pr_id' => $purchaserequest->id])
                ->orderBy(['time_stamp' => SORT_DESC]),
        ]);

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $items,
        ]);

        return $this->render('/adm-bac/pr_quotation_indexpb', [
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'purchaserequest' => $purchaserequest,
            'quotation' => $quotation,

        ]);
    }

    public function actionBacQuotationcreate()
    {
        $purchaserequestId = Yii::$app->request->get('id');
        $purchaserequest = PurchaseRequest::findOne($purchaserequestId);

        if ($purchaserequest === null) {
            throw new NotFoundHttpException('Purchase request not found.');
        }
        $newQuotation = new Quotation();
        $quotationQuery = Quotation::findOne(['pr_id' => $purchaserequest->id]);
        $quotation = ($quotationQuery !== null ? $quotationQuery : $newQuotation);

        $descriptions = PrItems::find()
            ->where(['pr_id' => $purchaserequestId])
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $descriptions,
        ]);

        $historylog = new HistoryLog();
        $purchaserequest->status = 7;

        if ($quotation->quotation_no === NULL) {

            $fullYear = date('Y');
            $lastTwoDigits = substr($fullYear, -2);

            // solicitation number
            if ($purchaserequest->mode_pr_id == 1) {
                $sequenceGoods = PrCodeFormSequence::find()->where(['pr_code' => 'PB'])->one();
                $pbSeries = 'ITDI-' . $sequenceGoods->pr_code . '-' . date('Y') . '-' .  str_pad($sequenceGoods->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequenceGoods->form_sequence = $sequenceGoods->form_sequence + 1;
                $newQuotation->quotation_no = $pbSeries;
            } else if ($purchaserequest->mode_pr_id == 2) {
                $sequenceInfra = PrCodeFormSequence::find()->where(['pr_code' => 'INFRA'])->one();
                $infraSeries = 'ITDI' . $sequenceInfra->pr_code . '-' . date('Y') . '-' .  str_pad($sequenceInfra->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequenceInfra->form_sequence = $sequenceInfra->form_sequence + 1;
                $newQuotation->quotation_no = $infraSeries;
            } else if ($purchaserequest->mode_pr_id >= 4) {
                $sequenceNonbidding = PrCodeFormSequence::find()->where(['id' => '10'])->one();
                $nbSeries  = $lastTwoDigits .  str_pad($sequenceNonbidding->form_sequence, 4, '0', STR_PAD_LEFT);
                $sequenceNonbidding->form_sequence = $sequenceNonbidding->form_sequence + 1;
                $newQuotation->quotation_no = $nbSeries;
            } else if ($purchaserequest->mode_pr_id == 3) {
                $sequenceCon = PrCodeFormSequence::find()->where(['pr_code' => 'CON'])->one();
                $conSeries = 'ITDI-' . $sequenceCon->pr_code . '-' . date('Y') . '-' .  str_pad($sequenceCon->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequenceCon->form_sequence = $sequenceCon->form_sequence + 1;
                $newQuotation->quotation_no = $conSeries;
            }

            if ($newQuotation->load(Yii::$app->request->post())) {

                if ($purchaserequest->mode_pr_id == 1) {
                    $sequenceGoods->save();
                } else if ($purchaserequest->mode_pr_id == 2) {
                    $sequenceInfra->save();
                } else if ($purchaserequest->mode_pr_id >= 4) {
                    $sequenceNonbidding->save();
                } else if ($purchaserequest->mode_pr_id == 3) {
                    $sequenceCon->save();
                }

                $newQuotation->option_date = $newQuotation->date . ' ' . $newQuotation->time;
                $newQuotation->time_stamp = date('Y-m-d h:i:s');
                $newQuotation->status = 3;
                $newQuotation->save();

                foreach ($descriptions as $descrip) {

                    $itemHistorylog = new ItemHistoryLogs();

                    $descrip->status = '10';

                    $itemHistorylog->item_id = $descrip->id;
                    $itemHistorylog->action_date = date('Y-m-d h:i');
                    $itemHistorylog->action_status = $descrip->status;
                    $itemHistorylog->action_by = Yii::$app->user->identity->id;
                    $itemHistorylog->action_remarks = 'Solicitation No: ' . $newQuotation->quotation_no;

                    $descrip->save(false);
                    $itemHistorylog->save();
                }
                if ($newQuotation->save()) {

                    $historylog->pr_id = $purchaserequest->id;
                    $historylog->action_date = date('Y-m-d h:i');
                    $historylog->action_status = $purchaserequest->status;
                    $historylog->action_user_id = Yii::$app->user->identity->id;
                    $historylog->remarks = 'BAC- Created Solicitation #';

                    $historylog->save();
                    $purchaserequest->save();

                    Yii::$app->getSession()->setFlash('success', 'Success');
                    return $this->redirect(Yii::$app->request->referrer);
                }
            }

            return $this->renderAjax('/adm-bac/pr_quotation_create', [
                'newQuotation' => $newQuotation,
                'quotation' => $quotation,
                'purchaserequest' => $purchaserequest,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            if ($newQuotation->load(Yii::$app->request->post()) && $newQuotation->validate()) {

                $newQuotation->option_date = $newQuotation->date . ' ' . $newQuotation->time;
                $newQuotation->time_stamp = date('Y-m-d h:i:s');
                $newQuotation->save();

                Yii::$app->getSession()->setFlash('success', 'Success');
                return $this->redirect(Yii::$app->request->referrer);
            }

            return $this->renderAjax('/adm-bac/pr_quotation_create', [
                'newQuotation' => $newQuotation,
                'quotation' => $quotation,
                'purchaserequest' => $purchaserequest,
                'dataProvider' => $dataProvider,
            ]);
        }
    }

    public function actionGetDuration($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $info = Info::findOne($id);

        if ($info !== null) {
            return [
                'success' => true,
                'daterange_from' => $info->daterange_from,
                'daterange_to' => $info->daterange_to,
            ];
        }

        return ['success' => false];
    }

    // add scheduling details for reposting
    // public function actionBacRepostingCreate()
    // {
    //     $prItem = PrItems::find()
    //         ->where(['id' => $_GET['id']])
    //         ->one();

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $prItem->pr_id])
    //         ->one();

    //     $quote = Quotation::find()
    //         ->where(['pr_id' => $prItem->pr_id])
    //         ->one();

    //     $quotationReposting = new Quotation();
    //     $quotation = ($quote != NULL ? $quote : $quotationReposting);

    //     $itemHistorylog = new ItemHistoryLogs();
    //     $historylog = new HistoryLog();
    //     $purchaserequest->status = 7;

    //     if ($quotationReposting->load(Yii::$app->request->post())) {
    //         if ($quotationReposting->option_id == 7) {
    //             $quotationReposting->status = 3; // for reposting
    //             $quotationReposting->save();
    //             $prItem->status = '47';
    //             $prItem->save();
    //         } else {
    //             $quotationReposting->status = 4; // for canvass
    //             $quotationReposting->save();
    //             $prItem->status = '57';
    //             $prItem->save();
    //         }

    //         if ($quotationReposting->save()) {

    //             if ($quotationReposting->option_id == 7) {
    //                 $itemHistorylog->item_id = $prItem->id;
    //                 $itemHistorylog->action_date = date('Y-m-d h:i');
    //                 $itemHistorylog->action_status = $prItem->status;
    //                 $itemHistorylog->action_by = Yii::$app->user->identity->id;
    //                 $itemHistorylog->action_remarks = $quotationReposting->remarks;


    //                 $itemHistorylog->save();


    //                 $historylog->pr_id = $purchaserequest->id;
    //                 $historylog->action_date = date('Y-m-d h:i');
    //                 $historylog->action_status = $purchaserequest->status;
    //                 $historylog->action_user_id = Yii::$app->user->identity->id;
    //                 $historylog->remarks = 'BAC- Reposting';

    //                 $historylog->save();
    //                 $purchaserequest->save();

    //                 return $this->redirect(Yii::$app->request->referrer);
    //             } else {

    //                 $itemHistorylog->item_id = $prItem->id;
    //                 $itemHistorylog->action_date = date('Y-m-d h:i');
    //                 $itemHistorylog->action_status = $prItem->status;
    //                 $itemHistorylog->action_by = Yii::$app->user->identity->id;
    //                 $itemHistorylog->action_remarks = $quotationReposting->remarks;


    //                 $itemHistorylog->save();


    //                 $historylog->pr_id = $purchaserequest->id;
    //                 $historylog->action_date = date('Y-m-d h:i');
    //                 $historylog->action_status = $purchaserequest->status;
    //                 $historylog->action_user_id = Yii::$app->user->identity->id;
    //                 $historylog->remarks = 'BAC- For Canvass';

    //                 $historylog->save();
    //                 $purchaserequest->save();
    //             }

    //             return $this->redirect(Yii::$app->request->referrer);
    //         }
    //     }

    //     return $this->renderAjax('/adm-bac/pr_reposting_create', [
    //         'prItem' => $prItem,
    //         'quotationReposting' => $quotationReposting,
    //         'purchaserequest' => $purchaserequest,
    //         'quotation' => $quotation
    //     ]);
    // }

    public function actionBacRepostingCreate()
    {
        $prItemId = Yii::$app->request->get('id');
        $prItem = PrItems::findOne($prItemId);

        if (!$prItem) {
            throw new \yii\web\NotFoundHttpException('The requested PR item does not exist.');
        }

        $purchaseRequest = PurchaseRequest::find()
            ->where(['id' => $prItem->pr_id])
            ->one();
        $quotation = Quotation::find()->where(['pr_id' => $prItem->pr_id])->one();
        $quote = Quotation::find()->where(['pr_id' => $prItem->pr_id])->andWhere(['option_id' => '6'])->one();
        $quotationReposting = $quote ?? new Quotation();

        $itemHistorylog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        if ($quotationReposting->load(Yii::$app->request->post())) {
            if ($quotationReposting->option_id == 6) {
                $quotationReposting->status = 3; // for reposting
                $quotationReposting->option_date = $quotationReposting->date . ' ' . $quotationReposting->time;
                $quotationReposting->time_stamp = date('Y-m-d h:i');
                $prItem->status = '47';
            } elseif ($quotationReposting->option_id == 8) {
                $quotationReposting->status = 4; // for canvass
                $quotationReposting->time_stamp = date('Y-m-d h:i');
                $quotationReposting->option_date = $quotationReposting->date . ' ' . $quotationReposting->time;
                $prItem->status = '57';
            }

            if ($quotationReposting->save()) {
                $prItem->save();

                $itemHistorylog->item_id = $prItem->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $prItem->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->action_remarks = $quotationReposting->remarks;
                $itemHistorylog->save();

                $historylog->pr_id = $purchaseRequest->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->action_status = $purchaseRequest->status;
                $historylog->action_user_id = Yii::$app->user->identity->id;

                if ($quotationReposting->option_id == 6) {
                    $historylog->remarks = 'BAC- Reposting';
                } elseif ($quotationReposting->option_id == 8) {
                    $historylog->remarks = 'BAC- For Canvass';
                }

                $historylog->save();
                $purchaseRequest->save();

                Yii::$app->getSession()->setFlash('success', 'Success');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('/adm-bac/pr_reposting_create', [
            'prItem' => $prItem,
            'quotationReposting' => $quotationReposting,
            'purchaseRequest' => $purchaseRequest,
            'quotation' => $quotation,
            'quote' => $quote
        ]);
    }

    // Request for quotation
    public function actionBacQuotationRfqcreate()
    {
        $quotation = Quotation::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $quotation->pr_id])
            ->one();

        // $quotation = Quotation::find()
        //     ->where(['pr_id' => $purchaserequest['id']])
        //     ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['not', ['status' => '18']])->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $description,
        ]);

        $historylog = new HistoryLog();
        $quotation->status = 1;
        $purchaserequest->status = 7;

        if ($quotation->save()) {
            foreach ($description as $descrip) {

                $descrip->status = 11;
                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $descrip->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $descrip->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                $descrip->save();
                $itemHistorylog->save();
            }
            if ($purchaserequest->save()) {
                $historylog->pr_id = $purchaserequest->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->action_status = $purchaserequest->status;
                $historylog->action_user_id = Yii::$app->user->identity->id;
                $historylog->remarks = 'BAC-Canvas Form';

                $historylog->save();
            }
        }

        return $this->renderAjax('/adm-bac/pr_quotation_rfq_create', [
            'description' => $description,
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'quotation' => $quotation,
        ]);
    }

    public function actionBacQuotationRfqPdf()
    {
        $quotation = Quotation::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $quotation->pr_id])
            ->one();

        $descriptiontest = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->all();

        $purchaserequest->status = 7;
        $quotation->status = 1;
        $item_no = 1;
        $quotation->save();

        foreach ($descriptiontest as $descrip) {

            $itemHistorylog = new ItemHistoryLogs();

            $itemHistorylog->item_id = $descrip->id;
            $itemHistorylog->action_date = date('Y-m-d h:i');
            $itemHistorylog->action_status = $descrip->status;
            $itemHistorylog->action_by = Yii::$app->user->identity->id;

            $descrip->save(false);
            $itemHistorylog->save();
        }

        $pdf_content = $this->renderPartial('/adm-bac/pr_quotation_rfq_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'quotation' => $quotation,
            'item_no' => $item_no,
        ]);

        $foot = '
        <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
        Page {PAGENO} of {nb} <br>
       
     ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'filename' => 'ITDI- REQUEST FOR QUOTATION FORM',
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'marginLeft' => 5,
            'marginRight' => 5,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI- REQUEST FOR QUOTATION FORM',
                'SetFooter' => [$foot],
            ]
        ]);
        $mpdf = $pdf->api;
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;

        return $pdf->render();
    }

    public function actionBacQuotationRfqRepostPdf()
    {
        $quotation = Quotation::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $quotation->pr_id])
            ->one();

        $descriptiontest = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => '47'])
            ->all();

        $purchaserequest->status = 7;
        $quotation->status = 1;
        $item_no = 1;
        $quotation->save();

        foreach ($descriptiontest as $descrip) {

            $itemHistorylog = new ItemHistoryLogs();

            $itemHistorylog->item_id = $descrip->id;
            $itemHistorylog->action_date = date('Y-m-d h:i');
            $itemHistorylog->action_status = $descrip->status;
            $itemHistorylog->action_by = Yii::$app->user->identity->id;

            $descrip->save(false);
            $itemHistorylog->save();
        }

        $pdf_content = $this->renderPartial('/adm-bac/pr_quotation_rfq_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'quotation' => $quotation,
            'item_no' => $item_no,
        ]);

        $foot = '
        <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
        Page {PAGENO} of {nb} <br>
       
     ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'filename' => 'ITDI- REQUEST FOR QUOTATION FORM',
            // 'mode' => Pdf::MODE_BLANK,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'marginLeft' => 5,
            'marginRight' => 5,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI- REQUEST FOR QUOTATION FORM',
                'SetFooter' => [$foot],
            ]
        ]);
        // $content = $this->renderPartial('/adm-bac/pr_quotation_rfq_pdf');
        $mpdf = $pdf->api;
        // $content = '<div class="page-break-before">' . $content . '</div>';
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;

        return $pdf->render();
        // return $this->redirect(['index']);
    }

    // Philgeps 
    public function actionBacQuotationPhilgepscreate()
    {
        $quotation = Quotation::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $quotation->pr_id])
            ->one();

        $descriptiontest = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $descriptiontest,
        ]);

        $historylog = new HistoryLog();

        if ($quotation->load(Yii::$app->request->post())) {

            foreach ($descriptiontest as $descrip) {

                $descrip->status = '12';

                // $itemStatus = ItemHistoryLogs::itemStatus($descrip->id, $descrip->status);
                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $descrip->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $descrip->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->action_remarks = 'Reference #: ' . $quotation->reference_no;

                $itemHistorylog->save();
                $descrip->save(false);
            }

            if ($quotation->save()) {
                $purchaserequestPrNo = PurchaseRequest::find()->where(['id' => $quotation->pr_id])->one();

                $purchaserequestPrNo->status = 7;
                $purchaserequestPrNo->save();

                $historylog->pr_id = $purchaserequestPrNo->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->action_status = $purchaserequestPrNo->status;
                $historylog->action_user_id = Yii::$app->user->identity->id;

                $historylog->remarks = 'BAC- Posted';
                $historylog->save();

                Yii::$app->getSession()->setFlash('success', 'Success');
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('/adm-bac/pr_quotation_philgeps_create', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'quotation' => $quotation,
            'descriptiontest' => $descriptiontest,
        ]);
    }

    // Remarks update 
    public function actionBacQuotationRemarksUpdate()
    {
        $quotation = Quotation::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $quotation->pr_id])
            ->one();

        $descriptiontest = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->all();

        $dataProvider = new ActiveDataProvider([
            'query' => $descriptiontest,
        ]);

        if ($quotation->load(Yii::$app->request->post())) {

            $quotation->save();

            Yii::$app->getSession()->setFlash('success', 'Success');
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-bac/pr_quotation_remarks_update', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'quotation' => $quotation,
            'descriptiontest' => $descriptiontest,
        ]);
    }

    // Philgeps repost reference number 
    public function actionBacRepostrefnumcreate()
    {
        $id = Yii::$app->request->post('id');
        $quotation = Quotation::find()
            ->where(['id' => $id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $quotation->pr_id])
            ->one();

        $prItem = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => '47'])
            ->one();

        // var_dump($prItem);die;
        $itemHistorylog = new ItemHistoryLogs();

        $quotation->reference_no = $_POST['remarks'];
        $quotation->save();

        if ($quotation->save()) {
            $purchaserequestPrNo = PurchaseRequest::find()->where(['id' => $quotation->pr_id])->one();

            $purchaserequestPrNo->status = 7;
            $purchaserequestPrNo->save();

            $itemHistorylog->item_id = $prItem->id;
            $itemHistorylog->action_date = date('Y-m-d h:i');
            $itemHistorylog->action_status = 'Added Reference #';
            $itemHistorylog->action_by = Yii::$app->user->identity->id;
            $itemHistorylog->action_remarks = 'Reference #: ' . $_POST['remarks'];;

            $itemHistorylog->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-bac/pr_quotation_philgeps_create', [
            'purchaserequest' => $purchaserequest,
            'quotation' => $quotation,
            'prItem' => $prItem,
        ]);
    }

    // saved btn for revision of PR in quotation tab for PB
    public function actionBacRevisionrequest()
    {
        $id = Yii::$app->request->post('id');
        $model = PurchaseRequest::find()
            ->where(['id' => $id])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $model->id])
            ->all();

        $historylog = new HistoryLog();
        $itemHistoryLog = new ItemHistoryLogs();

        $model->status = 39;
        $model->save();

        foreach ($description as $descrip) {
            $itemHistoryLog = new ItemHistoryLogs();
            $descrip->status = 40;

            $itemHistoryLog->item_id = $descrip->id;
            $itemHistoryLog->action_date = date('Y-m-d h:i');
            $itemHistoryLog->action_status = $descrip->status;
            $itemHistoryLog->action_by = Yii::$app->user->identity->id;

            $descrip->save();
            $itemHistoryLog->save();
        }

        $historylog->pr_id = $model->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $model->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = $_POST['remarks'];

        $historylog->save();


        return $this->redirect(['bac-request-index']);
    }

    // PPMS process
    public function actionProcurementRequestIndex()
    {
        $model = new PurchaseRequest;
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->ppms(Yii::$app->request->queryParams);

        if (Yii::$app->request->post('hasEditable')) {

            $id = (Yii::$app->request->post('editableKey'));
            $model = $this->findModel($id);
            $out = Json::encode(['output' => '', 'message' => 'success']);
            // $post = [];
            $posted = current($_POST['PurchaseRequest']);
            $post['PurchaseRequest'] = $posted;

            $model->budget_clustering_id = Yii::$app->request->post('budget_clustering_id');
            if ($model->load($post)); {
                $model->save();
                $output = Yii::$app->request->post('budget_clustering_id');

                if (isset($posted['budget_clustering_id'])) {
                    $output =  $model->budget_clustering_id;
                }
                $out = Json::encode(['output' => $output, 'message' => 'success']);
            }
            echo $out;
            return $this->redirect(['procurement-request-index', 'id' => $model->id]);
        }

        return $this->render('/adm-procurement/ppms_pr_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]);
    }

    public function actionProcurementRequestItems()
    {
        $searchModel = new PrItemSearch();
        $dataProvider = $searchModel->procurementIndex(Yii::$app->request->queryParams);

        return $this->render('/adm-procurement/ppms_pr_items', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionProcurementPrview()
    {
        $model = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->andWhere(['status' => TrackStatus::PPMS_VIEW])
            ->all();

        $historylog = HistoryLog::find()
            ->where(['pr_id' => $_GET['id']])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $files = Attachments::find()
            ->where(['pr_id' => $model->id])
            ->all();

        $attachments = Attachments::find()
            ->where(['pr_id' => $model['id']])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $attachments,
        ]);

        $dataProvider = new ArrayDataProvider([
            'allModels' => $description,
        ]);

        return $this->render('/adm-procurement/ppms_pr_view', [
            'model' => $model,
            'description' => $description,
            'historylog' => $historylog,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'files' => $files
        ]);
    }

    // request for additional attachments
    public function actionPrAttachmentrequest()
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

            return $this->redirect(['bac-request-index', 'id' => $model->id]);
        }
    }

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

                            return $this->redirect(['purchaserequest-view', 'id' => $model->id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('pr_attachments_create', [
            'model' => $model,
            'modelAttachments' => $modelAttachments,
            'modelAttach' => (empty($modelAttach)) ? [new Attachments] : $modelAttach,
            'historylog' => $historylog
        ]);
    }
}
