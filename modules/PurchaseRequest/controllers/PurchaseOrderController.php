<?php

namespace app\modules\PurchaseRequest\controllers;

use app\modules\PurchaseRequest\models\AdditionalServices;
use Yii;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ConformeAttachments;
use app\modules\PurchaseRequest\models\Delivery;
use app\modules\PurchaseRequest\models\DeliveryAttachments;
use app\modules\PurchaseRequest\models\DivisionFormSequence;
use kartik\mpdf\Pdf;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\ItemHistoryLogs;
use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\LessDeductions;
use app\modules\PurchaseRequest\models\Model;
use app\modules\PurchaseRequest\models\PurchaseOrder;
use app\modules\PurchaseRequest\models\PurchaseOrderItems;
use app\modules\PurchaseRequest\models\PurchaseOrderSearch;
use app\modules\PurchaseRequest\models\Supplier;
use app\modules\PurchaseRequest\models\InspectionAcceptanceReport;
use yii\web\UploadedFile;

/**
 * BiddingController implements the CRUD actions for BiddingList model.
 */
class PurchaseOrderController extends Controller
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

    //expandrow for conforme
    public function actionItemspecsExpand()
    {
        if (isset($_POST['expandRowKey'])) {
            $model = \app\modules\PurchaseRequest\models\ConformeAttachments::findOne($_POST['expandRowKey']);
            return $this->renderPartial('/adm-procurement/ppms_conforme_list_expandrow', ['model' => $model]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }
    }

    // Purchase Order Process
    public function actionPpmsPurchaseorderCreate()
    {
        $purchaseOrder = new PurchaseOrder();
        $modelItems = [new PurchaseOrderItems()];
        $modelAdditions = [new AdditionalServices()];

        $selectedKeys = Yii::$app->request->get('keys');
        $sample = BiddingList::find()->where(['id' => $selectedKeys])->all();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $sample,
        ]);

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $bidding = BiddingList::find()
            ->where(['id' => $sample])->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $items = PrItems::find()
            ->where(['id' => $bidding->item_id])->one();

        $dataProvider = new ActiveDataProvider([
            'query' => $items
        ]);

        if (in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19'])) {

            $sequence = DivisionFormSequence::find()->where(['division_code' => 'WO'])->one();
            $wonumber  = $sequence->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
            $sequence->form_sequence = $sequence->form_sequence + 1;
            $purchaseOrder->po_no = $wonumber;
        } else {
            $sequence = DivisionFormSequence::find()->where(['division_code' => 'PO'])->one();
            $ponumber  = $sequence->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
            $sequence->form_sequence = $sequence->form_sequence + 1;
            $purchaseOrder->po_no = $ponumber;
        }

        $historylog = new HistoryLog();
        $valid = [];

        if ($purchaseOrder->load(Yii::$app->request->post())) {
            // var_dump($purchaseOrder);die;
            $purchaserequest->status = 7;

            $purchaseOrder->po_status = 1;
            $purchaseOrder->created_by = Yii::$app->user->identity->id;
            $purchaseOrder->pr_id = $purchaserequest->id;
            $purchaseOrder->supplier_id = $bidding->supplier_id;
            $purchaseOrder->po_date_created = date('Y-m-d h:i');
            $purchaseOrder->date_delivery = date('d-M-yyyy');

            $modelItems = Model::createMultiple(PurchaseOrderItems::classname());
            Model::loadMultiple($modelItems, Yii::$app->request->post());
            $modelAdditions = Model::createMultiple(AdditionalServices::classname());
            Model::loadMultiple($modelAdditions, Yii::$app->request->post());
            $valid = $purchaseOrder->validate();
            $valid = Model::validateMultiple($modelItems) && $valid;
            $valid = Model::validateMultiple($modelAdditions) && $valid;

            $sequence->save();
            $purchaserequest->save();

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $purchaseOrder->save()) {
                            foreach ($modelAdditions as $modelAddservices) {

                                $modelAddservices->po_id = $purchaseOrder->id;
                                if (!($flag = $modelAddservices->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }

                            foreach ($modelItems as $modelItem) {

                                $modelBidItem = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                                $itemId = PrItems::find()->where(['id' => $modelBidItem->item_id])->one();

                                $modelItem->po_id = $purchaseOrder->id;
                                $modelItem->bid_id = $modelBidItem->id;
                                $modelBidItem->po_id = $purchaseOrder->id;

                                $modelBidItem->status = 21;

                                $itemId->status = 21;
                                $itemHistorylog = new ItemHistoryLogs();

                                $modelBidItem->save();
                                $itemId->save();
                                $modelItem->save();
                                $valid[] = $modelItem->save();

                                $itemHistorylog->item_id = $itemId->id;
                                $itemHistorylog->action_date = date('Y-m-d h:i');
                                $itemHistorylog->action_status = $modelBidItem->status;
                                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                                $itemHistorylog->save();
                            }

                            $historylog->pr_id = $purchaserequest->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $purchaserequest->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;
                            $historylog->remarks = 'PPMS- Purchase Order';


                            $historylog->save();

                            return $this->redirect(Yii::$app->request->referrer);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/adm-procurement/ppms_po_create', [
            'purchaserequest' => $purchaserequest,
            'items' => $items,
            'bidding' => $bidding,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'dataProvider2' => $dataProvider2,
            'modelItems' => $modelItems,
            'modelItems' => (empty($modelItems)) ? [new PurchaseOrderItems()] : $modelItems,
            'modelAdditions' => (empty($modelAdditions)) ? [new AdditionalServices()] : $modelAdditions,
            'sample' => $sample
        ]);
    }

    // Work Order Process
    // public function actionPpmsWorkorderCreate()
    // {
    //     $workOrder = new WorkOrder();
    //     $modelItems = new WorkOrderItems();
    //     $modelAdditions = [new AdditionalServices()];
    //     $model_additions = AdditionalServices::find()->where(['add_service_id' => $workOrder->id])->all();

    //     $selectedKeys = Yii::$app->request->get('keys');
    //     $sample = BiddingList::find()->where(['id' => $selectedKeys])->all();

    //     $dataProvider2 = new ArrayDataProvider  ([
    //         'allModels' => $sample,
    //     ]);

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $_GET['id']])
    //         ->one();

    //     $bidding = BiddingList::find()
    //         ->where(['id' => $sample])->one();

    //     $supplier = Supplier::find()
    //         ->where(['id' => $bidding->supplier_id])
    //         ->one();

    //     $items = PrItems::find()
    //         ->where(['id' => $bidding->item_id])->one();

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $items
    //     ]);

    //     if (in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19'])) {

    //         $sequence = DivisionFormSequence::find()->where(['division_code' => 'WO'])->one();
    //         $wonumber  = $sequence->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
    //         $sequence->form_sequence = $sequence->form_sequence + 1;
    //         $workOrder->po_no = $wonumber;
    //     } else {
    //         $sequence = DivisionFormSequence::find()->where(['division_code' => 'PO'])->one();
    //         $ponumber  = $sequence->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
    //         $sequence->form_sequence = $sequence->form_sequence + 1;
    //         $workOrder->po_no = $ponumber;
    //     }

    //     // $orsburs = DivisionFormSequence::find()->where(['division_code' => 'TF'])->one();
    //     // $orsbursnumber  = $orsburs->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($orsburs->form_sequence, 4, '0', STR_PAD_LEFT);
    //     // $orsburs->form_sequence = $sequence->form_sequence + 1;
    //     // $purchaseOrder->ors_burs_num = $orsbursnumber;

    //     $historylog = new HistoryLog();
    //     $biddingLists = Yii::$app->request->post('BiddingList');

    //     if ($workOrder->load(Yii::$app->request->post())) {


    //         $purchaserequest->status = 7;

    //         $workOrder->po_status = 1;
    //         $workOrder->created_by = Yii::$app->user->identity->id;
    //         $purchaseOrder->pr_id = $purchaserequest->id;
    //         $purchaseOrder->supplier_id = $bidding->supplier_id;
    //         $purchaseOrder->po_date_created = date('Y-m-d h:i');
    //         // $purchaseOrder->date_delivery = date('Y-m-d h:i');
    //         // $purchaseOrder->bid_id = $bidding->id;

    //         $modelDeductions = Model::createMultiple(LessDeductions::classname());
    //         Model::loadMultiple($modelDeductions, Yii::$app->request->post());

    //         $valid = $purchaseOrder->validate();
    //         $valid = Model::validateMultiple($modelDeductions) && $valid;

    //         $modelAdditions = Model::createMultiple(AdditionalServices::classname());
    //         Model::loadMultiple($modelAdditions, Yii::$app->request->post());

    //         $valid = $purchaseOrder->validate();
    //         $valid = Model::validateMultiple($modelAdditions) && $valid;

    //         $sequence->save();
    //         // $orsburs->save();
    //         $purchaseOrder->save();
    //         $purchaserequest->save();


    //         if ($valid) {
    //             $transaction = \Yii::$app->db->beginTransaction();
    //             try {
    //                 if ($flag = $purchaseOrder->save()) {
    //                     foreach ($modelDeductions as $modelLessDeductions) {
    //                         $modelLessDeductions->po_id = $purchaseOrder->id;
    //                         if (!($flag = $modelLessDeductions->save(false))) {
    //                             $transaction->rollBack();
    //                             break;
    //                         }
    //                     }
    //                     foreach ($modelAdditions as $modelAddservices) {
    //                         $modelAddservices->po_id = $purchaseOrder->id;
    //                         if (!($flag = $modelAddservices->save(false))) {
    //                             $transaction->rollBack();
    //                             break;
    //                         }
    //                     }

    //                     foreach ($sample as $modelBidding) {
    //                         // var_dump($modelBidding);die;
    //                         $description = PrItems::find()->where(['id' => $modelBidding->item_id])->one();

    //                         $modelBidding->item_remarks = $modelBidding->item_remarks;
    //                         $modelBidding->status = 21;

    //                         $description->status = 21;
    //                         $modelItems = new PurchaseOrderItems();
    //                         $itemHistorylog = new ItemHistoryLogs();

    //                         $modelItems->po_id = $purchaseOrder->id;
    //                         $modelItems->bid_id = $modelBidding['id'];
    //                         $modelItems->isNewRecord = true;
    //                         $modelItems->save();
    //                         $modelBidding->save();
    //                         $description->save();

    //                         $itemHistorylog->item_id = $description->id;
    //                         $itemHistorylog->action_date = date('Y-m-d h:i');
    //                         $itemHistorylog->action_status = $modelBidding->status;
    //                         $itemHistorylog->action_by = Yii::$app->user->identity->id;

    //                         $itemHistorylog->save();
    //                     }

    //                     $historylog->pr_id = $purchaserequest->id;
    //                     $historylog->action_date = date('Y-m-d h:i');
    //                     $historylog->action_status = $purchaserequest->status;
    //                     $historylog->action_user_id = Yii::$app->user->identity->id;
    //                     $historylog->remarks = 'PPMS- Purchase Order';

    //                     $historylog->save();
    //                     return $this->redirect(Yii::$app->request->referrer);
    //                 }
    //             } catch (\Exception $e) {
    //                 $transaction->rollBack();
    //             }
    //         }
    //     }

    //     return $this->renderAjax('/adm-procurement/ppms_wo_create', [
    //         'purchaserequest' => $purchaserequest,
    //         'items' => $items,
    //         'bidding' => $bidding,
    //         'purchaseOrder' => $purchaseOrder,
    //         'dataProvider' => $dataProvider,
    //         'supplier' => $supplier,
    //         'dataProvider2' => $dataProvider2,
    //         'modelItems' => $modelItems,
    //         'modelDeductions' => (empty($modelDeductions)) ? [new LessDeductions] : $modelDeductions,
    //         'model_deductions' => $model_deductions,
    //         'modelAdditions' => (empty($modelAdditions)) ? [new AdditionalServices()] : $modelAdditions,
    //         'model_additions' => $model_additions,
    //     ]);
    // }

    // DV Process
    public function actionPpmsDvCreate()
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $_GET['id']])->one();

        $modelorderItems = PurchaseOrderItems::find()->where(['po_id' => $purchaseOrder->id])->all();
        $model_deductions = LessDeductions::find()->where(['po_id' => $purchaseOrder->id])->all();
        // var_dump($modelorderItems);die;
        $model_additions = AdditionalServices::find()->where(['po_id' => $purchaseOrder->id])->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $modelorderItems,
        ]);

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $modelorderItems,
        ]);

        $dataProvider3 = new ArrayDataProvider([
            'allModels' => $model_additions,
        ]);

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $supplier = Supplier::find()
            ->where(['id' => $purchaseOrder->supplier_id])
            ->one();

        $historylog = new HistoryLog();

        if ($purchaseOrder->load(Yii::$app->request->post())) {

            foreach ($modelorderItems as $modelItem) {
                $modelBidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $modelBidding->item_id])->one();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $modelBidding->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;

                $itemHistorylog->save();
            }

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'PPMS- Purchase Order';

            $historylog->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-procurement/ppms_dv_create', [
            'purchaserequest' => $purchaserequest,
            'purchaseOrder' => $purchaseOrder,
            'supplier' => $supplier,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'dataProvider3' => $dataProvider3,
            'modelDeductions' => (empty($modelDeductions)) ? [new LessDeductions] : $modelDeductions,
            'model_deductions' => $model_deductions,
            'modelAdditions' => (empty($modelAdditions)) ? [new AdditionalServices()] : $modelAdditions,
            'model_additions' => $model_additions,
        ]);
    }

    public function actionPurchaseorderIndex()
    {
        $purchaseOrder = PurchaseOrder::find()->all();

        $dataProvider = new ArrayDataProvider([
            'purchaseOrder' => $purchaseOrder,
        ]);
        return $this->renderAjax('/adm-procurement/ppms_po_index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    // all po/wo
    public function actionPurchaseorderList()
    {

        $model = new PurchaseOrder();
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->searchAll(Yii::$app->request->queryParams);

        return $this->render('/adm-procurement/ppms_list_po_wo', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    public function actionPurchaseorderView()
    {
        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        return $this->renderAjax('/adm-procurement/ppms_po_view', [
            'modelPurchaseorder' => $modelPurchaseorder,
            'modelPurchaserequest' => $modelPurchaserequest,

        ]);
    }

    // Item Specs in PO
    public function actionPurchaseorderItemsView()
    {
        $modelBidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $modelItemspecs = ItemSpecification::find()
            ->where(['item_id' => $modelBidding['item_id']])
            ->all();

        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $modelBidding->pr_id])
            ->one();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $modelItemspecs,
        ]);

        return $this->renderAjax('/adm-procurement/ppms_item_specs_view', [
            'modelBidding' => $modelBidding,
            'modelPurchaserequest' => $modelPurchaserequest,
            'dataProvider' => $dataProvider
        ]);
    }

    // all po/wo cancelled
    public function actionPurchaseorderCancelled()
    {

        $model = new PurchaseOrder();
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->searchAllcancelled(Yii::$app->request->queryParams);

        return $this->renderAjax('/adm-procurement/ppms_cancelled_po_wo', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // list of po/wo for end user
    public function actionPoIndexEnduser()
    {
        $model = new PurchaseOrder();
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->euserPowolist(Yii::$app->request->queryParams);

        return $this->render('/adm-procurement/euser_list_po_wo', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // list of po/wo for budget
    public function actionPoIndexBudget()
    {
        $model = new PurchaseOrder();
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->budgetPowolist(Yii::$app->request->queryParams);

        return $this->render('/fmd-budget/budget_list_po_wo', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
        ]);
    }

    // list of po/wo for budget monitoring
    public function actionPowoBudgetview()
    {
        $model = new PurchaseOrder();
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->fmd(Yii::$app->request->queryParams, $_GET['id']);

        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        return $this->render('/fmd-budget/budget_po_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
            'modelPurchaserequest' => $modelPurchaserequest
        ]);
    }

    public function actionOrsbursGeneratenum()
    {
        $model = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $modelpr = PurchaseRequest::find()
            ->where(['id' => $model['pr_id']])
            ->one();

        $modelpr->status = 7;
        $modelpr->save();

        $orderItems = PurchaseOrderItems::find()->where(['po_id' => $model['id']])->all();

        if ($model->load(Yii::$app->request->post())) {
            $model->date_ors_burs = date('Y-m-d h:i');
            $model->save();

            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('/fmd-budget/pr_modal_orsburs_generatenum', [
            'model' => $model,
            'modelpr' => $modelpr

        ]);
    }

    public function actionPpmsPurchaseorderIndex()
    {
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, [$_GET['id']]);

        return $this->renderAjax('/adm-procurement/ppms_po_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionPpmsPurchaseorderAccept($acpt)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $acpt])->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 2;
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 32;
                $description->status = 32;
                $bidding->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'PPMS- Submitted PO to Validate';

        $historylog->save();

        return $this->redirect(['purchase-request/procurement-request-index']);
    }

    // cancel PO in index
    public function actionPpmsPurchaseorderCancel()
    {
        $id = Yii::$app->request->post('cancelid');

        $purchaseOrder = PurchaseOrder::find()->where(['id' => $id])->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 46;
        $purchaseOrder->po_status = 3;
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 46;
                $description->status = 46;
                $bidding->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_remarks = $_POST['remarks'];
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = $_POST['remarks'];

        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    // Budget-purchase order process
    public function actionBudgetPoIndex()
    {
        $model = new PurchaseOrder();
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->fmd(Yii::$app->request->queryParams, $_GET['id']);

        return $this->renderAjax('/fmd-budget/budget_po_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model,

        ]);
    }

    public function actionBudgetPoObligate($fmd)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $fmd])->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $bidding = BiddingList::find()
            ->where(['id' => $itemData])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding['item_id']])
            ->one();

        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 4; //obligate status
        // $bidding->status = 22;
        $purchaserequest->save();
        $purchaseOrder->save();


        if ($purchaseOrder->save()) {

            foreach ($biddingLists as $modelItem) {

                // $bidding = BiddingList::find()->where(['id' => $modelItem['bid_id']])->one();
                $description = PrItems::find()->where(['id' => $modelItem['item_id']])->one();
                $modelItem->status = 22;
                $description->status = 22;

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
                $modelItem->save();
                $description->save();
            }

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'FMD(BUDGET)- P.O. Obligated';


            $historylog->save();

            return $this->redirect(['purchase-request/budget-approved-request-index']);
        }
    }

    // Accounting-purchase order process
    public function actionAccountingPoIndex()
    {
        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->accounting(Yii::$app->request->queryParams, $_GET['id']);

        return $this->render('/fmd-accounting/accounting_po_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'modelPurchaserequest' => $modelPurchaserequest

        ]);
    }

    public function actionAccountingPoValidate($val)
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $val])
            ->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])
            ->all();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder['id']])
            ->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $bidding = BiddingList::find()
            ->where(['id' => $itemData])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding['item_id']])
            ->one();

        $historylog = new HistoryLog();

        // $description->status = 23;
        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 5; //validate status
        // $bidding->status = 23;
        $purchaserequest->save();
        $purchaseOrder->save();

        if ($purchaseOrder->save()) {

            foreach ($biddingLists as $modelItem) {

                $description = PrItems::find()->where(['id' => $modelItem['item_id']])->one();
                $modelItem->status = 23;
                $description->status = 23;

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
                $modelItem->save();
                $description->save();
            }

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'FMD(Accounting)- P.O. Validated';
            $historylog->save();

            return $this->redirect(['purchase-request/accounting-request-index']);
        }
    }

    // Conforme process in PPMS
    public function actionPpmsPoSubmitforconforme($con)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $con])->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $purchaseOrder->conforme_status = 1;
        $purchaseOrder->po_status = 6;
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($biddingLists as $modelItem) {
                // $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $modelItem->item_id])->one();
                $modelItem->status = 24;
                $description->status = 24;
                $modelItem->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'PPMS- Suppliers Conforme';
        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPpmsSuppliersconformeIndex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->conforme(Yii::$app->request->queryParams, $_GET['id']);

        return $this->render('/adm-procurement/ppms_suppliers_conforme_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'purchaserequest' => $purchaserequest
        ]);
    }

    // left side view page of conforme
    public function actionPpmsConformeLists()
    {
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->conformeLeft(Yii::$app->request->queryParams);

        return $this->render('/adm-procurement/ppms_suppliers_conforme_list_all', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    // PPMS
    public function actionPpmsSuppliersconformeCreate()
    {
        $request = Yii::$app->request;
        $model = PurchaseOrder::find()->where(['id' => $_GET['id']])->one();
        $modelConforme = new ConformeAttachments();
        $modelConform = [new ConformeAttachments];
        $valid = [];

        if ($model->load(Yii::$app->request->post())) {

            $modelConform = Model::createMultiple(ConformeAttachments::classname());
            Model::loadMultiple($modelConform, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelConform) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {

                            foreach ($modelConform as $i => $conf) {

                                $file[$i] = UploadedFile::getInstanceByName("ConformeAttachments[" . $i . "][file_name]");
                                if ($file[$i] != NULL) {

                                    $path = 'uploads/pr_conforme_files/' . md5($file[$i]->baseName . date("m/d/y G:i:s:u")) . '.' . $file[$i]->extension;

                                    if ($file[$i]->saveAs($path)) {
                                        $conf->po_id = $model->id;
                                        $conf->file_directory = $path;
                                        $conf->file_name = $file[$i]->baseName . '.' . $file[$i]->extension;
                                        $conf->file_extension = $file[$i]->extension;
                                        $valid[] = $conf->save();
                                    }
                                } else {
                                    continue;
                                }
                            }
                            $model->date_conforme = $conf->time_stamp;
                            $model->save();

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            // return $this->redirect(['purchase-request/procurement-prview', 'id' => $model->pr_id]);
                            return $this->redirect(Yii::$app->request->referrer);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/adm-procurement/pr_conforme_attachments_create', [
            'model' => $model,
            'modelConforme' => $modelConforme,
            'modelConform' => (empty($modelConform)) ? [new ConformeAttachments()] : $modelConform,
            // 'historylog' => $historylog
        ]);
    }

    public function actionDeliveryattachmentsCreate()
    {
        $request = Yii::$app->request;
        $model = Delivery::find()->where(['id' => $_GET['id']])->one();
        $modelPo = PurchaseOrder::find()->where(['id' => $model->po_id])->one();
        $modelPr = PurchaseRequest::find()->where(['id' => $modelPo->pr_id])->one();
        $modelDelattach = new DeliveryAttachments();
        $modelDelattachment = [new DeliveryAttachments()];
        // $historylog = new HistoryLog();
        $valid = [];

        if ($model->load(Yii::$app->request->post())) {

            $modelDelattachment = Model::createMultiple(DeliveryAttachments::classname());
            Model::loadMultiple($modelDelattachment, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelDelattachment) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {

                            foreach ($modelDelattachment as $i => $del) {

                                $file[$i] = UploadedFile::getInstanceByName("DeliveryAttachments[" . $i . "][file_name]");
                                if ($file[$i] != NULL) {

                                    $path = 'uploads/pr_delivery_files/' . md5($file[$i]->baseName . date("m/d/y G:i:s:u")) . '.' . $file[$i]->extension;

                                    if ($file[$i]->saveAs($path)) {
                                        $del->delivery_id = $model->id;
                                        $del->file_directory = $path;
                                        $del->file_name = $file[$i]->baseName . '.' . $file[$i]->extension;
                                        $del->file_extension = $file[$i]->extension;
                                        $valid[] = $del->save();
                                    }
                                } else {
                                    continue;
                                }
                            }

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            // return $this->redirect(['purchase-request/procurement-prview', 'id' => $modelPr->id]);
                            return $this->redirect(Yii::$app->request->referrer);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/adm-procurement/pr_delivery_attachments_create', [
            'model' => $model,
            'modelDelattach' => $modelDelattach,
            'modelDelattachment' => (empty($modelDelattachment)) ? [new DeliveryAttachments()] : $modelDelattachment,
        ]);
    }

    public function actionPpmsSuppliersconformeAccept($conId)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $conId])->one();
        // var_dump($purchaseOrder);die;

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 7;
        $purchaseOrder->conforme_status = 2;
        $purchaseOrder->date_conforme = date('Y-m-d h:i');
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 34;
                $description->status = 34;
                $bidding->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'PPMS- Accepted Conforme of Supplier';

        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPpmsSuppliersconformeDecline($dec)
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $dec])
            ->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 35;
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 35;
                $description->status = 35;
                $bidding->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'PPMS- Declined Conforme of Supplier';


        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    // Delivery process
    public function actionPpmsDeliveryIndex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->delivery(Yii::$app->request->queryParams, $_GET['id']);

        return $this->render('/adm-procurement/ppms_delivery_index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'purchaserequest' => $purchaserequest
        ]);
    }

    // DV process
    public function actionPpmsDvIndex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaseOrder = PurchaseOrder::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->all();

        $biddingQuery = BiddingList::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->andWhere(['status' => ['24', '52', '25']]);

        $searchModel = new PurchaseOrderSearch();
        $dataProvider2 = $searchModel->ppms(Yii::$app->request->queryParams, $_GET['id']);

        $dataProvider = new ActiveDataProvider([
            'query' => $biddingQuery,
        ]);

        return $this->render('/adm-procurement/ppms_dv_index', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'bidding' => $biddingQuery->all(),
        ]);
    }

    // Delivery process
    public function actionPpmsDeliveryLists()
    {
        $searchModel = new PurchaseOrderSearch();
        $dataProvider = $searchModel->deliveryLists(Yii::$app->request->queryParams);

        return $this->render('/adm-procurement/ppms_delivery_list_all', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionPpmsDeliveryCreate()
    {

        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaseRequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder->id])
            ->all();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])
            ->all();

        $historylog = new HistoryLog();
        $purchaseRequest->status = 7;

        if ($purchaseOrder->actual_date_delivery == NULL) {
            $model_delivery = [new Delivery];
        } else {
            $model_delivery = $purchaseOrder->delivery;
        }
        if ($purchaseOrder->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($model_delivery, 'id', 'id');
            $model_delivery = Model::createMultiple(Delivery::className(), $model_delivery);
            Model::loadMultiple($model_delivery, Yii::$app->request->post());

            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($model_delivery, 'id', 'id')));

            $valid = $purchaseOrder->validate();
            $valid = Model::validateMultiple($model_delivery) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $purchaseOrder->save(false)) {
                        foreach ($model_delivery as $modeldelivery) {

                            $modeldelivery->po_id = $purchaseOrder->id;

                            if (!($flag = $modeldelivery->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        foreach ($biddingLists as $modelItem) {
                            // $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                            $description = PrItems::find()->where(['id' => $modelItem->item_id])->one();
                            $modelItem->status = 25;
                            $description->status = 25;
                            $modelItem->save();
                            $description->save();

                            $itemHistorylog = new ItemHistoryLogs();

                            $itemHistorylog->item_id = $description->id;
                            $itemHistorylog->action_date = date('Y-m-d h:i');
                            $itemHistorylog->action_status = $description->status;
                            $itemHistorylog->action_by = Yii::$app->user->identity->id;
                            $itemHistorylog->save();
                        }
                    }

                    if ($flag) {
                        $transaction->commit();

                        $purchaseOrder->po_status = 7;
                        $purchaseOrder->conforme_status = 3;
                        $purchaseOrder->delivery_status = $modeldelivery->type_delivery;

                        $historylog->pr_id = $purchaseRequest->id;
                        $historylog->action_date = date('Y-m-d h:i');
                        $historylog->action_status = $purchaseRequest->status;
                        $historylog->action_user_id = Yii::$app->user->identity->id;
                        $historylog->remarks = 'PPMS- Delivery';

                        $purchaseOrder->save();
                        $historylog->save();
                        $purchaseRequest->save();

                        Yii::$app->getSession()->setFlash('success', 'Success');
                        // return $this->redirect(['purchase-request/procurement-prview', 'id' => $_GET['id']]);
                        return $this->redirect(Yii::$app->request->referrer);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
        return $this->renderAjax('/adm-procurement/ppms_delivery_create', [
            'purchaseOrder' => $purchaseOrder,
            'model_delivery' => (empty($model_delivery)) ? [new Delivery()] : $model_delivery,
            // 'delAttachments' => $delAttachments
        ]);
    }

    // Validate Delivery
    public function actionPpmsDeliveryValidated($valId)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $valId])->one();
        // var_dump($purchaseOrder);die;

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 8;
        $purchaseOrder->delivery_status = 'd';

        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {

                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 52;
                $description->status = 52;
                $bidding->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'PPMS- Delivery Validated';

        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    // IAR ICS PAR Index
    public function actionPpmsIarIcsParIndex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $biddingQuery = BiddingList::find()
            ->where(['pr_id' => $_GET['id']])->andWhere(['status' => ['17']]);

        $searchModel = new PurchaseOrderSearch();
        $dataProvider2 = $searchModel->ppms(Yii::$app->request->queryParams, $_GET['id']);

        $dataProvider = new ActiveDataProvider([
            'query' => $biddingQuery,
        ]);

        return $this->renderAjax('/adm-procurement/iar_ics_par_index', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'bidding' => $biddingQuery->all(),
        ]);
    }

    // cancel PO items in index
    public function actionPpmsIarPoitemsCancel()
    {
        $id = Yii::$app->request->post('cancelid');

        $purchaseOrder = PurchaseOrder::find()->where(['id' => $id])->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $purchaseOrder->id])->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder->pr_id])
            ->one();

        $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
            return $model['bid_id'];
        });

        $historylog = new HistoryLog();

        $purchaserequest->status = 46;
        $purchaseOrder->po_status = 3;
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 46;
                $description->status = 46;
                $bidding->save();
                $description->save();

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_remarks = $_POST['remarks'];
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = $_POST['remarks'];

        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    // pdf purchase order
    public function actionPpmsPurchaseorderPdf()
    {
        $model = new PurchaseOrder();
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $orderItems = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->all();

        $orderItem1 = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->one();

        $itemVats = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])
            ->andWhere(['deduction_id' => '1'])
            ->all();

        $itemEwts = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])
            ->andWhere(['deduction_id' => '2'])
            ->all();

        $addServices = AdditionalServices::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $bidding = BiddingList::find()
            ->where(['id' => $orderItem1['bid_id']])->one();

        $descriptiontest = PrItems::find()
            ->where(['id' => $bidding['item_id']])->one();

        $itemSpecs = ItemSpecification::find()
            ->where(['item_id' => $descriptiontest['id']])
            ->all();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_po_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'model' => $model,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItems' => $orderItems,
            'orderItem1' => $orderItem1,
            'addServices' => $addServices,
            'biddingLists' => $biddingLists,
            'itemVats' => $itemVats,
            'itemSpecs' => $itemSpecs,
            'itemEwts' => $itemEwts,

        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'filename' => 'ITDI-PURCHASE ORDER FORM',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-PURCHASE ORDER FORM',
            ]
        ]);

        return $pdf->render();
    }

    // pdf work order
    public function actionPpmsWorkorderPdf()
    {
        $model = new PurchaseOrder();

        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $orderItems = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->all();

        $orderItem1 = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->one();

        $itemVats = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])
            ->andWhere(['deduction_id' => '1'])
            ->all();

        $itemEwts = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])
            ->andWhere(['deduction_id' => '2'])
            ->all();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $addServices = AdditionalServices::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $bidding = BiddingList::find()
            ->where(['id' => $orderItem1['bid_id']])->one();

        $descriptiontest = PrItems::find()
            ->where(['id' => $bidding['item_id']])->one();

        $itemSpecs = ItemSpecification::find()
            ->where(['item_id' => $descriptiontest['id']])
            ->all();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_wo_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'model' => $model,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItems' => $orderItems,
            'orderItem1' => $orderItem1,
            'itemVats' => $itemVats,
            'itemEwts' => $itemEwts,
            'addServices' => $addServices,
            'biddingLists' => $biddingLists,
            'itemSpecs' => $itemSpecs
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-WORK ORDER FORM',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-WORK ORDER FORM',
            ]
        ]);

        return $pdf->render();
    }

    // ORS
    public function actionPpmsOrsPdf()
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->all();

        $orderItem1 = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $lessDeduction = LessDeductions::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $bidding = BiddingList::find()
            ->where(['id' => $orderItem1['bid_id']])->one();

        $descriptiontest = PrItems::find()
            ->where(['id' => $bidding['item_id']])->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_ors_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItem' => $orderItem,
            'orderItem1' => $orderItem1,
            'lessDeduction' => $lessDeduction,
            'biddingLists' => $biddingLists
        ]);


        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-OBLIGATION REQUEST AND STATUS ',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-OBLIGATION REQUEST AND STATUS ',
            ]
        ]);

        return $pdf->render();
    }

    // BURS
    public function actionPpmsBursPdf()
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->all();

        $orderItem1 = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->one();

        $lessDeduction = LessDeductions::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $bidding = BiddingList::find()
            ->where(['id' => $orderItem1['bid_id']])->one();

        $descriptiontest = PrItems::find()
            ->where(['id' => $bidding['item_id']])->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_burs_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItem' => $orderItem,
            'orderItem1' => $orderItem1,
            'lessDeduction' => $lessDeduction,
            'biddingLists' => $biddingLists
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI- BUDGET UTILIZATION REQUEST AND STATUS',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI- BUDGET UTILIZATION REQUEST AND STATUS',
            ]
        ]);

        return $pdf->render();
    }

    // NTP
    // public function actionPpmsNtpPdf()
    // {
    //     $purchaseOrder = PurchaseOrder::find()
    //         ->where(['id' => $_GET['id']])
    //         ->one();

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $purchaseOrder['pr_id']])
    //         ->one();

    //     $orderItem = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])
    //         ->all();

    //     $orderItem1 = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])
    //         ->one();

    //     $lessDeduction = LessDeductions::find()
    //         ->where(['po_id' => $purchaseOrder['id']])
    //         ->all();

    //     $bidding = BiddingList::find()
    //         ->where(['id' => $orderItem1['bid_id']])
    //         ->one();

    //     $description = PrItems::find()
    //         ->where(['id' => $bidding['item_id']])
    //         ->one();

    //     $supplier = Supplier::find()
    //         ->where(['id' => $bidding->supplier_id])
    //         ->one();

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => PrItems::find()
    //             ->where(['id' => $bidding->item_id])
    //     ]);

    //     $pdf_content = $this->renderPartial('/adm-procurement/pr_ntp_pdf', [
    //         'purchaserequest' => $purchaserequest,
    //         'description' => $description,
    //         'bidding' => $bidding,
    //         'purchaseOrder' => $purchaseOrder,
    //         'dataProvider' => $dataProvider,
    //         'supplier' => $supplier,
    //         'orderItem' => $orderItem,
    //         'orderItem1' => $orderItem1,
    //         'lessDeduction' => $lessDeduction
    //     ]);

    //     $head = '
    //     <div>
    //     <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"/>
    //     </div>
    //  ';

    //     $foot = '
    //     <div style="text-align:center;">
    //    <br>
    //         <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
    //  ';

    //     $pdf = new Pdf([
    //         'mode' => Pdf::MODE_CORE,
    //         'filename' => 'ITDI- NOTICE TO PROCEED',
    //         'format' => Pdf::FORMAT_A4,
    //         'marginTop' => 40,
    //         'marginBottom' => 0,
    //         'orientation' => Pdf::ORIENT_PORTRAIT,
    //         'destination' => Pdf::DEST_BROWSER,
    //         'content' => $pdf_content,
    //         'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
    //         'cssInline' => '.kv-heading-1{font-size:18px}',
    //         'options' => ['title' => 'Krajee Report Title'],
    //         'methods' => [
    //             'SetTitle' => 'ITDI- NOTICE TO PROCEED',
    //             'SetHeader' => [$head],
    //             'SetFooter' => [$foot],
    //         ]
    //     ]);

    //     $mpdf = $pdf->api;
    //     $mpdf->setHeader($head);
    //     $mpdf->SetFooter($foot);
    //     $mpdf->defaultfooterline = 0;
    //     $mpdf->defaultheaderline = 0;
    //     return $pdf->render();
    // }

    // NOA
    // public function actionPpmsNoaPdf()
    // {
    //     $purchaseOrder = PurchaseOrder::find()
    //         ->where(['id' => $_GET['id']])
    //         ->one();

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $purchaseOrder['pr_id']])
    //         ->one();

    //     $orderItem = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])->all();

    //     $orderItem1 = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])->one();

    //     $lessDeduction = LessDeductions::find()
    //         ->where(['po_id' => $purchaseOrder['id']])->all();

    //     $bidding = BiddingList::find()
    //         ->where(['id' => $orderItem1['bid_id']])->one();

    //     $description = PrItems::find()
    //         ->where(['id' => $bidding['item_id']])->one();

    //     $supplier = Supplier::find()
    //         ->where(['id' => $bidding->supplier_id])
    //         ->one();

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => PrItems::find()->where(['id' => $bidding->item_id])
    //     ]);

    //     $pdf_content = $this->renderPartial('/adm-procurement/pr_noa_pdf', [
    //         'purchaserequest' => $purchaserequest,
    //         'description' => $description,
    //         'bidding' => $bidding,
    //         'purchaseOrder' => $purchaseOrder,
    //         'dataProvider' => $dataProvider,
    //         'supplier' => $supplier,
    //         'orderItem' => $orderItem,
    //         'orderItem1' => $orderItem1,
    //         'lessDeduction' => $lessDeduction
    //     ]);

    //     $head = '
    //      <div>
    //      <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"/>
    //      </div>
    //   ';

    //     $foot = '
    //      <div style="text-align:center;">
    //     <br>
    //          <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/></div>
    //   ';

    //     $pdf = new Pdf([
    //         'mode' => Pdf::MODE_CORE,
    //         'filename' => 'ITDI- NOTICE OF AWARD',
    //         'format' => Pdf::FORMAT_A4,
    //         'marginTop' => 40,
    //         'marginLeft' => 20,
    //         'marginRight' => 20,
    //         'marginBottom' => 0,
    //         'orientation' => Pdf::ORIENT_PORTRAIT,
    //         'destination' => Pdf::DEST_BROWSER,
    //         'content' => $pdf_content,
    //         'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
    //         'cssInline' => '
    //             .kv-heading-1{font-size:18px}
    //             @media all{
    //                 .font_next{font-family:DoodlePen}table{border-collapse:collapse;width:100%}td{border:1px solid #000}.page-break {display: none;}
    //             }
    //             @media print{
    //                 .page-break{display: block;page-break-before: always;}
    //             }
    //         ',
    //         'options' => ['title' => 'Krajee Report Title'],
    //         'methods' => [
    //             'SetTitle' => 'ITDI- NOTICE OF AWARD',
    //             'SetHeader' => [$head],
    //             'SetFooter' => [$foot],
    //         ]
    //     ]);

    //     $mpdf = $pdf->api;
    //     $mpdf->setHeader($head);
    //     $mpdf->SetFooter($foot);
    //     $mpdf->defaultfooterline = 0;
    //     $mpdf->defaultheaderline = 0;
    //     return $pdf->render();
    // }

    // DV
    public function actionPpmsDvPdf()
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $iarModel = InspectionAcceptanceReport::find()
            ->where(['po_id' => $purchaseOrder->id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->all();

        $orderItem1 = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->one();

        $lessDeduction = LessDeductions::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $bidding = BiddingList::find()
            ->where(['id' => $orderItem1['bid_id']])->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();


        $descriptiontest = PrItems::find()
            ->where(['id' => $bidding['item_id']])->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_dv_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItem' => $orderItem,
            'orderItem1' => $orderItem1,
            'lessDeduction' => $lessDeduction,
            'biddingLists' => $biddingLists,
            'iarModel' => $iarModel
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-DISBURSEMENT VOUCHER ',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-DISBURSEMENT VOUCHER ',
            ]
        ]);

        return $pdf->render();
    }

    // public function actionPpmsDvPdf()
    // {
    //     $model = new PurchaseOrder();

    //     $purchaseOrder = PurchaseOrder::find()
    //         ->where(['id' => $_GET['id']])
    //         ->one();

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $purchaseOrder['pr_id']])
    //         ->one();

    //     $biddingLists = BiddingList::find()
    //         ->where(['po_id' => $purchaseOrder['id']])->all();

    //     $orderItems = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])->all();

    //     $orderItem1 = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])->one();

    //     $itemVats = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])
    //         ->andWhere(['deduction_id' => '1'])
    //         ->all();

    //     $itemEwts = PurchaseOrderItems::find()
    //         ->where(['po_id' => $_GET['id']])
    //         ->andWhere(['deduction_id' => '2'])
    //         ->all();

    //     $addServices = AdditionalServices::find()
    //         ->where(['po_id' => $purchaseOrder['id']])->all();

    //     $bidding = BiddingList::find()
    //         ->where(['id' => $orderItem1['bid_id']])->one();

    //     $descriptiontest = PrItems::find()
    //         ->where(['id' => $bidding['item_id']])->one();

    //     $itemSpecs = ItemSpecification::find()
    //         ->where(['item_id' => $descriptiontest['id']])
    //         ->all();

    //     $supplier = Supplier::find()
    //         ->where(['id' => $bidding->supplier_id])
    //         ->one();

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => PrItems::find()->where(['id' => $bidding->item_id])
    //     ]);

    //     $pdf_content = $this->renderPartial('/adm-procurement/ppms_dv_pdf', [
    //         'purchaserequest' => $purchaserequest,
    //         'descriptiontest' => $descriptiontest,
    //         'bidding' => $bidding,
    //         'model' => $model,
    //         'purchaseOrder' => $purchaseOrder,
    //         'dataProvider' => $dataProvider,
    //         'supplier' => $supplier,
    //         'orderItems' => $orderItems,
    //         'orderItem1' => $orderItem1,
    //         'addServices' => $addServices,
    //         'biddingLists' => $biddingLists,
    //         'itemVats' => $itemVats,
    //         'itemSpecs' => $itemSpecs,
    //         'itemEwts' => $itemEwts
    //     ]);

    //     $pdf = new Pdf([
    //         'mode' => Pdf::MODE_CORE,
    //         'filename' => 'ITDI-DISBURSEMENT VOUCHER ',
    //         'format' => Pdf::FORMAT_A4,
    //         'marginTop' => 40,
    //         'marginBottom' => 0,
    //         'orientation' => Pdf::ORIENT_PORTRAIT,
    //         'destination' => Pdf::DEST_BROWSER,
    //         'content' => $pdf_content,
    //         'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
    //         'cssInline' => '.kv-heading-1{font-size:18px}',
    //         'options' => ['title' => 'Krajee Report Title'],
    //         'methods' => [
    //             'SetTitle' => 'ITDI-DISBURSEMENT VOUCHER ',
    //         ]
    //     ]);

    //     return $pdf->render();
    // }
}
