<?php

namespace app\modules\PurchaseRequest\controllers;

use app\models\User;
use app\modules\PurchaseRequest\models\AdditionalServices;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\InspectionAcceptanceReport;
use app\modules\PurchaseRequest\models\InventoryCustodianSlip;
use app\modules\PurchaseRequest\models\ItemHistoryLogs;
use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseOrder;
use app\modules\PurchaseRequest\models\PurchaseOrderItems;
use app\modules\PurchaseRequest\models\PurchaseOrderSearch;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\RequestModel;
use app\modules\PurchaseRequest\models\Supplier;
use app\modules\PurchaseRequest\models\PropertyAcknowledgement;
use kartik\mpdf\Pdf;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class InspectionAcceptanceReportController extends Controller
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

    // IAR Index
    public function actionPpmsIarIndex()
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

        return $this->render('/adm-procurement/ppms_iar_index', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'bidding' => $biddingQuery->all(),
        ]);
    }

    // ICS Index
    public function actionPpmsIcsIndex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaseOrder = PurchaseOrder::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->all();

        $biddingQuery = BiddingList::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->andWhere(['status' => ['24', '52']]);

        $searchModel = new PurchaseOrderSearch();
        $dataProvider2 = $searchModel->ppms(Yii::$app->request->queryParams, $_GET['id']);

        $dataProvider = new ActiveDataProvider([
            'query' => $biddingQuery,
        ]);

        return $this->render('/adm-procurement/ppms_ics_index', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'bidding' => $biddingQuery->all(),
        ]);
    }

    public function actionIarView()
    {
        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        $modelIar = InspectionAcceptanceReport::find()
            ->where(['po_id' => $modelPurchaseorder->id])
            ->one();

        $modelBidding = BiddingList::find()
            ->where(['pr_id' => $modelPurchaserequest['id']])
            ->andWhere(['status' => ['24', '52']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $modelBidding,
        ]);


        return $this->renderAjax('/adm-procurement/ppms_iar_view', [
            'modelPurchaseorder' => $modelPurchaseorder,
            'modelPurchaserequest' => $modelPurchaserequest,
            'modelIar' => $modelIar,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPpmsIcsView()
    {
        $modelBidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $modelBidding->po_id])
            ->one();

        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        $modelIar = InspectionAcceptanceReport::find()
            ->where(['po_id' => $modelPurchaseorder->id])
            ->one();

        $modelIcs = InventoryCustodianSlip::find()
            ->where(['iar_id' => $modelIar->id])
            ->one();

        $modelBidding = BiddingList::find()
            ->where(['pr_id' => $modelPurchaserequest['id']])
            ->andWhere(['status' => ['24', '52']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $modelBidding,
        ]);


        return $this->renderAjax('/adm-procurement/ppms_ics_view', [
            'modelPurchaseorder' => $modelPurchaseorder,
            'modelPurchaserequest' => $modelPurchaserequest,
            'modelIar' => $modelIar,
            'dataProvider' => $dataProvider,
            'modelIcs' => $modelIcs
        ]);
    }

    // Item Specs in ICS
    public function actionIcsItemsView()
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

        return $this->renderAjax('/adm-procurement/ppms_ics_item_specs_expand_view', [
            'modelBidding' => $modelBidding,
            'modelPurchaserequest' => $modelPurchaserequest,
            'dataProvider' => $dataProvider
        ]);
    }

    // Item Specs in IAR
    public function actionIarItemsView()
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

        return $this->renderAjax('/adm-procurement/ppms_iar_item_specs_expand_view', [
            'modelBidding' => $modelBidding,
            'modelPurchaserequest' => $modelPurchaserequest,
            'dataProvider' => $dataProvider
        ]);
    }

    // PAR 
    public function actionPpmsParView()
    {
        $modelBidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $modelBidding->po_id])
            ->one();

        $modelPurchaserequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        $modelIar = InspectionAcceptanceReport::find()
            ->where(['po_id' => $modelPurchaseorder->id])
            ->one();

        $modelPar = PropertyAcknowledgement::find()
            ->where(['iar_id' => $modelIar->id])
            ->one();

        $modelBidding = BiddingList::find()
            ->where(['pr_id' => $modelPurchaserequest['id']])
            ->andWhere(['status' => ['24', '52']]);

        $dataProvider = new ActiveDataProvider([
            'query' => $modelBidding,
        ]);

        return $this->renderAjax('/adm-procurement/ppms_par_view', [
            'modelPurchaseorder' => $modelPurchaseorder,
            'modelPurchaserequest' => $modelPurchaserequest,
            'modelIar' => $modelIar,
            'dataProvider' => $dataProvider,
            'modelPar' => $modelPar
        ]);
    }

    public function actionRequestchanges()
    {
        $model = $_GET['id'];
        // var_dump($model);die;
        $itemSpec = ItemSpecification::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $item = PrItems::find()
            ->where(['id' => $itemSpec->item_id])
            ->one();

        $requestChanges = new RequestModel();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $item['pr_id']])->one();

        if ($itemSpec->load(Yii::$app->request->post())) {
            $itemSpec->item_id = $item->id;
            $itemSpec->save();

            if ($itemSpec->save(false)) {
                $requestChanges->item_specs_id = $itemSpec->id;
                $requestChanges->request_changes = $itemSpec->request_changes;
                $requestChanges->request_status = 1;
                $requestChanges->save();
            }

            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('/adm-procurement/ppms_iar_itemspecs_changes', [
            'model' => $model,
            'itemSpec' => $itemSpec,

        ]);
    }

    // Purchase Order Process
    public function actionPpmsIarCreate($id)
    {
        $modelID = $_GET['id'];

        $iarModel = new InspectionAcceptanceReport();

        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaseRequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        $historylog = new HistoryLog();

        if ($iarModel->load(Yii::$app->request->post())) {

            $iarModel->po_id = $modelPurchaseorder->id;
            $iarModel->pr_id = $modelPurchaseorder->pr_id;
            $iarModel->iar_status = 1;
            $iarModel->save();

            if ($iarModel->save()) {

                $modelPurchaseorder->iar_id = $iarModel->id;
                $modelPurchaseorder->po_status = 9; //status for IAR Changes
                $modelPurchaseorder->save();

                $historylog->pr_id = $purchaseRequest->id;
                $historylog->action_date = date('Y-m-d h:i');
                $historylog->action_status = $purchaseRequest->status;
                $historylog->action_user_id = Yii::$app->user->identity->id;
                $historylog->remarks = 'PPMS- Created IAR';

                $historylog->save();
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
        return $this->renderAjax('/adm-procurement/ppms_iar_create', [
            'purchaseRequest' => $purchaseRequest,
            'modelPurchaseorder' => $modelPurchaseorder,
            'iarModel' => $iarModel
        ]);
    }

    // Purchase Order Process
    public function actionPpmsIcsCreate()
    {
        $modelBidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $icsModel = new InventoryCustodianSlip();

        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $modelBidding->po_id])
            ->one();

        $iarModel = InspectionAcceptanceReport::find()
            ->where(['id' => $modelPurchaseorder->iar_id])
            ->one();

        $purchaseRequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        if ($icsModel->load(Yii::$app->request->post())) {
            $icsModel->item_id = $modelBidding->item_id;
            $icsModel->bid_id = $modelBidding->id;
            $icsModel->iar_id = $iarModel->id;
            $icsModel->ics_status = 1;
            $icsModel->save();
            // var_dump($icsModel);die;

            if ($icsModel->save()) {
                // $modelPurchaseorder->iar_id = $icsModel->id;
                $modelPurchaseorder->po_status = 10; //status for IAR Changes
                $modelPurchaseorder->save();
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-procurement/ppms_ics_create', [
            'purchaseRequest' => $purchaseRequest,
            'modelPurchaseorder' => $modelPurchaseorder,
            'icsModel' => $icsModel,
            'iarModel' => $iarModel
        ]);
    }

    public function actionPpmsParCreate()
    {
        $modelBidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $parModel = new PropertyAcknowledgement();

        $modelPurchaseorder = PurchaseOrder::find()
            ->where(['id' => $modelBidding->po_id])
            ->one();

        $iarModel = InspectionAcceptanceReport::find()
            ->where(['id' => $modelPurchaseorder->iar_id])
            ->one();

        $purchaseRequest = PurchaseRequest::find()
            ->where(['id' => $modelPurchaseorder->pr_id])
            ->one();

        if ($parModel->load(Yii::$app->request->post())) {
            $parModel->item_id = $modelBidding->item_id;
            $parModel->bid_id = $modelBidding->id;
            $parModel->iar_id = $iarModel->id;
            $parModel->par_status = 1;
            $parModel->save();
            // var_dump($icsModel);die;

            if ($parModel->save()) {
                // $modelPurchaseorder->iar_id = $icsModel->id;
                $modelPurchaseorder->po_status = 11; //status for PAR Changes
                $modelPurchaseorder->save();
            }
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-procurement/ppms_par_create', [
            'purchaseRequest' => $purchaseRequest,
            'modelPurchaseorder' => $modelPurchaseorder,
            'parModel' => $parModel,
            'iarModel' => $iarModel
        ]);
    }
    // pdf purchase order
    public function actionPpmsIarPdf()
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $iarModel = InspectionAcceptanceReport::find()
            ->where(['po_id' => $purchaseOrder->id])
            ->one();

        $inspectorId = User::find()
            ->where(['id' => $iarModel->inspector_id])
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

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_iar_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'iarModel' => $iarModel,
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
            'inspectorId' => $inspectorId
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-INSPECTION AND ACCEPTANCE FORM',
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
                'SetTitle' => 'ITDI-INSPECTION AND ACCEPTANCE FORM',
            ]
        ]);

        return $pdf->render();
    }

    // pdf purchase order
    public function actionPpmsIcsPdf()
    {
        $bidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $bidding->po_id])
            ->one();
   
        $icsModel = InventoryCustodianSlip::find()
            ->where(['bid_id' => $bidding->id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $purchaseOrder['pr_id']])
            ->one();

        $biddingLists = BiddingList::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        // $orderItems = PurchaseOrderItems::find()
        //     ->where(['po_id' => $_GET['id']])->all();

        // $orderItem1 = PurchaseOrderItems::find()
        //     ->where(['po_id' => $_GET['id']])->one();

        // $itemVats = PurchaseOrderItems::find()
        //     ->where(['po_id' => $_GET['id']])
        //     ->andWhere(['deduction_id' => '1'])
        //     ->all();

        // $itemEwts = PurchaseOrderItems::find()
        //     ->where(['po_id' => $_GET['id']])
        //     ->andWhere(['deduction_id' => '2'])
        //     ->all();

        // $addServices = AdditionalServices::find()
        //     ->where(['po_id' => $purchaseOrder['id']])->all();

        // $bidding = BiddingList::find()
        //     ->where(['id' => $orderItem1['bid_id']])->one();

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

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_ics_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'icsModel' => $icsModel,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            // 'orderItems' => $orderItems,
            // 'orderItem1' => $orderItem1,
            // 'addServices' => $addServices,
            'biddingLists' => $biddingLists,
            // 'itemVats' => $itemVats,
            'itemSpecs' => $itemSpecs,
            // 'itemEwts' => $itemEwts,

        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-INSPECTION AND ACCEPTANCE FORM',
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
                'SetTitle' => 'ITDI-INSPECTION AND ACCEPTANCE FORM',
            ]
        ]);

        return $pdf->render();
    }

    // pdf purchase order
    public function actionPpmsParPdf()
    {
        $purchaseOrder = PurchaseOrder::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $icsModel = InventoryCustodianSlip::find()
            ->where(['po_id' => $purchaseOrder->id])
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

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_ics_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'icsModel' => $icsModel,
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
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-INSPECTION AND ACCEPTANCE FORM',
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
                'SetTitle' => 'ITDI-INSPECTION AND ACCEPTANCE FORM',
            ]
        ]);

        return $pdf->render();
    }
}
