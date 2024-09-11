<?php

namespace app\modules\PurchaseRequest\controllers;

use AllowDynamicProperties;
use Yii;
use kartik\mpdf\Pdf;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;
use app\modules\PurchaseRequest\models\AdditionalServices;
use app\modules\PurchaseRequest\models\BacSignatories;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ConformeAttachments;
use app\modules\PurchaseRequest\models\DivisionFormSequence;
use app\modules\PurchaseRequest\models\PrCodeFormSequence;
use app\modules\PurchaseRequest\models\Quotation;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\ItemHistoryLogs;
use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\LessDeductions;
use app\modules\PurchaseRequest\models\MemberSignatories;
use app\modules\PurchaseRequest\models\Model;
use app\modules\PurchaseRequest\models\PurchaseOrder;
use app\modules\PurchaseRequest\models\PurchaseOrderItems;
use app\modules\PurchaseRequest\models\PurchaseOrderSearch;
use app\modules\PurchaseRequest\models\Resolution;
use app\modules\PurchaseRequest\models\Supplier;
use mPDF\mPDF;

class BiddingController extends Controller
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

    // BIDDING PROCESS
    // submission and opening of bids tab
    public function actionBacBiddingitemlist()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->andWhere(['not', ['status' => ['18', '17', '32', '21']]]);

        $selectedKeys = Yii::$app->request->get('keys');

        $bidList = BiddingList::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => ['13', '15']])
            ->orderBy(['supplier_price' => SORT_ASC])->all();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $bidList,
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $description,
        ]);

        return $this->render('/adm-bac/pr_bidding_itemlist', [
            'purchaserequest' => $purchaserequest,
            'dataProvider2' => $dataProvider2,
            'dataProvider' => $dataProvider,
            'bidList' => $bidList,
            'selectedKeys' => $selectedKeys
        ]);
    }

    public function actionBacBiddingitemlistSmv()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $description = PrItems::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->andWhere(['not', ['status' => ['18', '17', '32']]]);

        $selectedKeys = Yii::$app->request->get('keys');

        $bidList = BiddingList::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => ['13', '15']])
            ->orderBy(['id' => SORT_DESC])->all();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $bidList,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $description,
        ]);

        return $this->render('/adm-bac/pr_bidding_itemlist_smv', [
            'purchaserequest' => $purchaserequest,
            'dataProvider2' => $dataProvider2,
            'dataProvider' => $dataProvider,
            'bidList' => $bidList,
            'selectedKeys' => $selectedKeys
        ]);
    }

    public function actionBacBiddingindex()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $items = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => '32'])->all();

        $biddingList = BiddingList::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => ['13', '15']])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $biddingList,
        ]);

        return $this->renderAjax('/adm-bac/pr_bidding_index', [
            'dataProvider' => $dataProvider,
            'purchaserequest' => $purchaserequest,
            'items' => $items,
        ]);
    }

    public function actionBacBiddingcreate()
    {
        $request = Yii::$app->request;
        $model = PurchaseRequest::find()->where(['id' => $_GET['id']])->one();
        $modelBidding = [new BiddingList];

        $historylog = new HistoryLog();
        $valid = [];

        $model->status = 7;
        if ($model->load(Yii::$app->request->post())) {

            $modelBidding = Model::createMultiple(BiddingList::classname());
            Model::loadMultiple($modelBidding, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelBidding) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                $valid = []; {
                    try {
                        if ($valid[] = $model->save()) {

                            foreach ($modelBidding as $modelotherbidding) {
                                $item = PrItems::find()->where(['id' => $modelotherbidding->item_id])->one();

                                $modelotherbidding->pr_id = $model->id;
                                $modelotherbidding->status = 13;

                                $modelotherbidding->save();
                                $valid[] = $modelotherbidding->save();

                                $item->status = 13;
                                $item->save();

                                $itemHistorylog = new ItemHistoryLogs();

                                $itemHistorylog->item_id = $item->id;
                                $itemHistorylog->action_date = date('Y-m-d h:i');
                                $itemHistorylog->action_status = $item->status;
                                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                                // $itemHistorylog->action_remarks = 'Reason: ' . $_POST['remarks'];
                                $itemHistorylog->save();
                            }

                            $historylog->pr_id = $model->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $model->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;
                            $historylog->remarks = 'BAC- Bidding';

                            $historylog->save();

                            Yii::$app->getSession()->setFlash('success', 'Success');

                            $this->redirect(Yii::$app->request->referrer);
                            // return $this->redirect(['purchase-request/bac-biddingitemlist-smv', 'id' => $model->id]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }
        }

        return $this->renderAjax('/adm-bac/pr_bidding_create', [
            'model' => $model,
            'modelBidding' => (empty($modelBidding)) ? [new BiddingList()] : $modelBidding,
        ]);
    }

    public function actionBacNonbiddingCreate()
    {
        $request = Yii::$app->request;
        $model = PurchaseRequest::findOne($request->get('id'));
        $modelBidding = [new BiddingList];
        $authManager = Yii::$app->authManager;

        $usersWithRole = $authManager->getUserIdsByRole('BAC TWG');
        $historylog = new HistoryLog();
        $valid = [];

        if ($model === null) {
            Yii::$app->getSession()->setFlash('error', 'Purchase request not found.');
            return $this->redirect(Yii::$app->request->referrer);
        }

        $model->status = 7;

        if ($model->load(Yii::$app->request->post())) {
            $modelBidding = Model::createMultiple(BiddingList::classname());
            Model::loadMultiple($modelBidding, Yii::$app->request->post());

            $valid = $model->validate();
            $valid = Model::validateMultiple($modelBidding) && $valid;
            if ($valid) {
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        foreach ($modelBidding as $modelotherbidding) {

                            $item = PrItems::findOne($modelotherbidding->item_id);

                            if ($item !== null) {
                                $modelotherbidding->pr_id = $model->id;
                                $modelotherbidding->status = 13;

                                if ($modelotherbidding->save()) {
                                    $item->status = 13;
                                    $item->save();

                                    $itemHistorylog = new ItemHistoryLogs();
                                    $itemHistorylog->item_id = $item->id;
                                    $itemHistorylog->action_date = date('Y-m-d h:i');
                                    $itemHistorylog->action_status = $item->status;
                                    $itemHistorylog->action_by = Yii::$app->user->identity->id;
                                    $itemHistorylog->save();
                                } else {
                                    Yii::$app->getSession()->setFlash('error', 'Error saving bidding item.');
                                    $transaction->rollBack();
                                    return $this->redirect(Yii::$app->request->referrer);
                                }
                            } else {
                                Yii::$app->getSession()->setFlash('error', 'Bidding item not found.');
                                $transaction->rollBack();
                                return $this->redirect(Yii::$app->request->referrer);
                            }
                        }

                        $historylog->pr_id = $model->id;
                        $historylog->action_date = date('Y-m-d h:i');
                        $historylog->action_status = $model->status;
                        $historylog->action_user_id = Yii::$app->user->identity->id;
                        $historylog->remarks = 'BAC- Bidding';
                        $historylog->save();

                        Yii::$app->getSession()->setFlash('success', 'Success');
                        $transaction->commit();
                        return $this->redirect(Yii::$app->request->referrer);
                    } else {
                        Yii::$app->getSession()->setFlash('error', 'Error saving purchase request.');
                    }
                } catch (\Exception $e) {
                    Yii::$app->getSession()->setFlash('error', 'An error occurred. Please try again later.');
                    $transaction->rollBack();
                    Yii::error($e->getMessage());
                }
            }
        }

        return $this->renderAjax('/adm-bac/pr_nonbidding_create', [
            'model' => $model,
            'usersWithRole' => $usersWithRole,
            'modelBidding' => (empty($modelBidding)) ? [new BiddingList()] : $modelBidding,
        ]);
    }

    public function actionBacBiddingnoncomply()
    {
        $id = Yii::$app->request->post('id');
        $model = BiddingList::find()
            ->where(['id' => $id])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $description['pr_id']])
            ->one();

        $itemHistorylog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $model->status = 15;
        $purchaserequest->status = 7;

        $itemHistorylog->item_id = $description->id;
        $itemHistorylog->action_date = date('Y-m-d h:i');
        $itemHistorylog->action_status = $model->status;
        $itemHistorylog->action_by = Yii::$app->user->identity->id;
        $itemHistorylog->action_remarks = 'Reason: ' . $_POST['remarks'];
        $itemHistorylog->save();
        $model->save();
        $description->save();
        $purchaserequest->save();

        if ($description->status == 14) {
            return $description->status;
        }
        return $description->status = '13';

        if ($itemHistorylog->save()) {

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Non-Comply Bidders';

            $historylog->save();

            $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionBacBiddingcomplyModal()
    {
        $bidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $bidding['pr_id']])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding->item_id])
            ->one();

        $itemHistorylog = new ItemHistoryLogs();

        return $this->renderAjax(
            '/adm-bac/pr_modal_bidding_comply',
            [
                'itemHistorylog' => $itemHistorylog,
                'bidding' => $bidding,
                'description' => $description,
                'purchaserequest' => $purchaserequest
            ]
        );
    }

    public function actionBacBiddingOfferCreate()
    {
        $bidding = BiddingList::findOne($_GET['id']);

        if (!$bidding) {
            throw new NotFoundHttpException('Bidding not found.');
        }

        $item = PrItems::findOne($bidding->item_id);

        if (!$item) {

            throw new NotFoundHttpException('Item not found.');
        }

        $purchaserequest = PurchaseRequest::findOne($item->pr_id);

        if (!$purchaserequest) {

            throw new NotFoundHttpException('PurchaseRequest not found.');
        }
        
        $itemSpecs = ItemSpecification::find()->where(['item_id' => $item['id']])->all();

        $itemHistoryLog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $item->status = 56;
        $purchaserequest->status = 56;

        if ($purchaserequest->save() && $item->save()) {

            if ($bidding->load(Yii::$app->request->post())) {
                $bidding->item_remarks = $bidding->item_remarks;
                $bidding->status = 56;

                if ($bidding->save()) {
                    $itemHistoryLog->item_id = $item->id;
                    $itemHistoryLog->action_date = date('Y-m-d h:i');
                    $itemHistoryLog->action_status = $item->status;
                    $itemHistoryLog->action_by = Yii::$app->user->identity->id;

                    $historylog->pr_id = $purchaserequest->id;
                    $historylog->action_date = date('Y-m-d h:i');
                    $historylog->action_status = $purchaserequest->status;
                    $historylog->action_user_id = Yii::$app->user->identity->id;
                    $historylog->remarks = 'BAC - Additional Bid Offer';

                    $historylog->save();
                    $itemHistoryLog->save();

                    return $this->redirect(Yii::$app->request->referrer);
                }
            }
        }

        return $this->renderAjax('/adm-bac/pr_items_bid_offer_create', [
            'bidding' => $bidding,
            'item' => $item,
            'historylog' => $historylog,
            'itemSpecs' => $itemSpecs
        ]);
    }

    public function actionBacBiddingcomplySaved($id)
    {
        $bidding = BiddingList::find()
            ->where(['id' => $id])
            ->one();

        if ($bidding === null) {
            // Handle the case where the record with the given ID is not found
            throw new NotFoundHttpException('The requested record does not exist.');
        }

        $description = PrItems::find()
            ->where(['id' => $bidding->item_id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $description['pr_id']])
            ->one();

        $biddingAll = BiddingList::find()
            ->where(['item_id' => $description->id])
            ->andWhere(['status' => '13'])
            ->all();

        // Check if the checkbox is checked
        $isChecked = Yii::$app->request->post('yourCheckboxAttribute');

        if (!$isChecked) {
            $bidding->status = '55';
            $bidding->save();
        }

        // $bidding->status = '12';
        // if ($isChecked) {
        //     $bidding->item_remarks = '3';
        // }
        // Update only the item_remarks column for the current BiddingList record
        // $bidding->item_remarks = !$isChecked ? '1' : '-';
        // $bidding->save();

        $itemHistoryLog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $purchaserequest->status = 7;

        $purchaserequest->save();
        $description->save();

        // Save the history logs
        $itemHistoryLog->item_id = $description->id;
        $itemHistoryLog->action_date = date('Y-m-d h:i');
        $itemHistoryLog->action_status = $description->status;
        $itemHistoryLog->action_by = Yii::$app->user->identity->id;
        $itemHistoryLog->save();

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'BAC- Comply Bidders';
        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }


    public function actionBacBiddinginsufficientbidder()
    {
        $id = Yii::$app->request->post('id');
        $model = BiddingList::find()
            ->where(['id' => $id])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $description['pr_id']])
            ->one();

        $itemHistorylog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $model->status = 15;
        $description->status = 15;
        $purchaserequest->status = 7;

        $itemHistorylog->item_id = $description->id;
        $itemHistorylog->action_date = date('Y-m-d h:i');
        $itemHistorylog->action_status = $description->status;
        $itemHistorylog->action_by = Yii::$app->user->identity->id;
        $itemHistorylog->action_remarks = $_POST['remarks'];
        $itemHistorylog->save();
        $model->save();
        $description->save();
        $purchaserequest->save();

        if ($itemHistorylog->save()) {

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Non-Comply Bidders';

            $historylog->save();

            $this->redirect(Yii::$app->request->referrer);
        }
    }

    public function actionBacBiddingComplyinglist()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $test = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->one();

        $bidding = BiddingList::find()
            ->where(['pr_id' => $test->pr_id])
            ->andWhere(['status' => ['14', '16', '56', '55']])
            ->all();

        $biddingNew = new BiddingList();
        // var_dump($bidding);die;
        $dataProvider = new ActiveDataProvider([
            'query' => BiddingList::find()->where(['id' => $bidding])
        ]);

        return $this->render('/adm-bac/pr_bidding_complying_list', [
            'dataProvider' => $dataProvider,
            'purchaserequest' => $purchaserequest,
            'bidding' => $bidding,
            'biddingNew' => $biddingNew
        ]);
    }

    public function actionBacResolutionlist()
    {
        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $resolution = Resolution::find()
            ->where(['pr_id' => $purchaserequest->id])

            ->all();

        $test = PrItems::find()
            ->where(['pr_id' => $_GET['id']])
            ->one();

        $bidding = BiddingList::find()
            ->where(['pr_id' => $test->pr_id])
            ->andWhere(['status' => ['14', '16', '56', '55']])
            ->all();

        // var_dump($bidding);die;
        // $dataProvider = new ActiveDataProvider([
        //     'query' => BiddingList::find()->where(['id' => $bidding])
        // ]);

        $dataProvider = new ActiveDataProvider([
            'query' => Resolution::find()->where(['id' => $resolution])
        ]);

        return $this->render('/adm-bac/pr_resolution_list', [
            'dataProvider' => $dataProvider,
            'purchaserequest' => $purchaserequest,
            'bidding' => $bidding,
        ]);
    }

    public function actionBacBidOfferList()
    {
        $bidding = BiddingList::find()
            ->where(['status' => ['14', '55', '56']])
            ->all();

        // var_dump($bidding);die;
        $dataProvider = new ActiveDataProvider([
            'query' => BiddingList::find()
                ->where(['id' => $bidding])
                ->orderBy(['time_stamp' => SORT_DESC])
        ]);

        return $this->render('/adm-bac/pr_bid_offer_list', [
            'dataProvider' => $dataProvider,
            'bidding' => $bidding,
        ]);
    }

    public function actionBacBiddingresoSubmit($sub)
    {
        $resolution = Resolution::find()
            ->where(['id' => $sub])
            ->one();

        $bidding = BiddingList::find()
            ->where(['resolution_no' => $resolution->id])
            ->all();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $resolution->pr_id])
            ->one();

        $resolution->status = 17;
        $resolution->save();

        foreach ($bidding as $bid) {

            $bid->status = 17;
            $bid->save();

            $description = PrItems::find()
                ->where(['id' => $bid->item_id])
                ->all();

            foreach ($description as $desc) {

                $desc->status = 17;
                $desc->save();

                $itemHistorylog = new ItemHistoryLogs();
                $itemHistorylog->item_id = $desc->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $desc->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }
        }

        $historylog = new HistoryLog();


        if ($resolution->save()) {
            $purchaserequest->status = 7;
            $purchaserequest->save();

            $historylog->pr_id = $resolution->pr_id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Awarded';

            $historylog->save();

            return $this->redirect(['purchase-request/bac-request-index', 'id' => $purchaserequest->id]);
        }
    }

    // create BAC bid bulletin (item specs)
    public function actionBacResolutionGeneratenum()
    {
        $model = new Resolution();

        $selectedKeys = Yii::$app->request->get('keys');
        $item = BiddingList::find()->where(['id' => $selectedKeys])->one();
        $sample = BiddingList::find()
            ->where(['id' => $selectedKeys])->asArray()
            ->all();

        $selectedItems = $sample;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $sample,
        ]);
        $purchaserequest = PurchaseRequest::find()->where(['id' => $item->pr_id])->one();

        $historylog = new HistoryLog();

        switch ($purchaserequest->mode_pr_id) {
            case 1:
                $sequencePb = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_PB'])->one();
                $resolutionNo = 'PB-' . date('Y') . '-' .  str_pad($sequencePb->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequencePb->form_sequence += 1;
                break;
            case 2:
                $sequenceInfra = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_INFRA'])->one();
                $resolutionNo = date('Y') . '-INFRA-' . str_pad($sequenceInfra->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequenceInfra->form_sequence += 1;
                break;
            case 3:
                $sequencePb = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_PB'])->one();
                $resolutionNo = 'PB-' . date('Y') . '-' .  str_pad($sequencePb->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequencePb->form_sequence += 1;
                break;
            case 4:
                $sequenceSvp = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_SVP'])->one();
                $resolutionNo = date('Y') . '-SVP-' .  date('m') . '-' . str_pad($sequenceSvp->form_sequence, 4, '0', STR_PAD_LEFT);
                $sequenceSvp->form_sequence += 1;
                break;
            case 5:
                $sequenceShopping = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_SH'])->one();
                $resolutionNo = date('Y') . '-SH-' . date('m') . '-' .  str_pad($sequenceShopping->form_sequence, 4, '0', STR_PAD_LEFT);
                $sequenceShopping->form_sequence += 1;
                break;
            case 8:
                $sequenceDirect = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_DC'])->one();
                $resolutionNo = date('Y') . '-DC-' . date('m') . '-' .  str_pad($sequenceDirect->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequenceDirect->form_sequence += 1;
                break;
            case 9:
                $sequenceDirect = PrCodeFormSequence::find()->where(['pr_code' => 'RESO_AA'])->one();
                $resolutionNo = date('Y') . '-AA-' . date('m') . '-' .  str_pad($sequenceDirect->form_sequence, 3, '0', STR_PAD_LEFT);
                $sequenceDirect->form_sequence += 1;
                break;
            default:
                $resolutionNo = ''; // Handle other cases if needed
                break;
        }

        if ($model->load(Yii::$app->request->post())) {

            switch ($purchaserequest->mode_pr_id) {
                case 1:
                    $sequencePb->save();
                    break;
                case 2:
                    $sequenceInfra->save();
                    break;
                case 3:
                    $sequencePb->save();
                    break;
                case 4:
                    $sequenceSvp->save();
                    break;
                case 5:
                    $sequenceShopping->save();
                    break;
                case 8:
                    $sequenceDirect->save();
                    break;
                case 9:
                    $sequenceDirect->save();
                    break;
                default:
                    $resolutionNo = '';
                    break;
            }

            $listData = ArrayHelper::map($sample, 'id', function ($model) {
                return $model['id'];
            });
            $itemUpdates = BiddingList::find()->select(['id', 'resolution_no', 'item_id', 'status'])->where(['id' => $listData])->all();


            // Generate resolution number based on mode_pr_id
            $model->pr_id = $purchaserequest->id;
            $model->resolution_no = $resolutionNo;
            // $model->resolution_date = date('Y-m-d h:i');
            $model->created_by = Yii::$app->user->identity->id;
            $model->save();

            foreach ($itemUpdates as $itemUpdate) {
                $item = PrItems::find()->where(['id' => $itemUpdate->item_id])->one();
                $item->status = 16;
                $item->save();

                $itemHistorylog = new ItemHistoryLogs();
                $itemUpdate->resolution_no = $model->id;
                $itemUpdate->status = 16;
                $itemUpdate->save();

                $nonComplying = BiddingList::find()->where(['item_id' => $item->id])->andWhere(['status' => '13'])->all();
                foreach ($nonComplying as $noncomply) {
                    $noncomply->status = 15;
                    $noncomply->save();
                }

                $itemHistorylog = new ItemHistoryLogs();
                $itemHistorylog->item_id = $item->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = 16;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
            }

            $historylog->pr_id = $model->pr_id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'BAC- Resolution';
            $historylog->save();

            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-bac/pr_modal_resolution_generatenum', [
            'item' => $item,
            'dataProvider' => $dataProvider,
            'selectedItems' => $selectedItems,
            'model' => $model,
            'purchaserequest' => $purchaserequest,
            'resolutionNo' => $resolutionNo,
        ]);
    }

    // winning bidders reso
    public function actionBacBiddingresolutionpbPdf()
    {

        $model = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $prDetails = PurchaseRequest::find()
            ->where(['id' => $model->pr_id])
            ->one();

        $quotation = Quotation::find()
            ->where(['pr_id' => $prDetails->id])
            ->one();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(
                ['id' => SORT_DESC]
            )->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $countbid = BiddingList::find()
            ->where(['item_id' => $description['id']])
            ->all();

        $bidIdarray = ArrayHelper::map($countbid, 'supplier_id', function ($model) {
            return $model['supplier_id'];
        });

        $count = BiddingList::find()->select(['supplier_id'])->where(['supplier_id' => $bidIdarray])->distinct();
        $countSupplier = $count->count();

        // $description->status = 16;
        // $prDetails->status = 7;
        // $itemHistorylog = new ItemHistoryLogs();
        // $historylog = new HistoryLog();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_resowinning_pb_pdf', [
            'quotation' => $quotation,
            'model' => $model,
            'prDetails' => $prDetails,
            'countSupplier' => $countSupplier,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'description' => $description
        ]);

        $head = '
            <div>
            <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"/>
            </div>
         ';

        $foot = '
            <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
            Page {PAGENO} of {nb} <br>
            RESOLUTION NO. ' . $model->resolution_no . '
            </div> <br>
                <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
         ';


        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-WINNING BIDDERS RESOLUTION FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-WINNING BIDDERS RESOLUTION FORM',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // LCRB reso
    public function actionBacBiddingresolutionlcrbPdf()
    {

        $model = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $prDetails = PurchaseRequest::find()
            ->where(['id' => $model->pr_id])
            ->one();

        $prebid = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 3])->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 4])->orderBy(['id' => SORT_DESC])
            ->one();

        $posted = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 2])->orderBy(['id' => SORT_DESC])
            ->one();

        $preproc = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 1])->orderBy(['id' => SORT_DESC])
            ->one();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $complyBidcount = BiddingList::find()
            ->where(['item_id' => $description['id']])->andWhere(['status' => ['14', '16']])->orderBy(['supplier_price' => SORT_ASC])
            ->all();

        $countbid2 = BiddingList::find()
            ->where(['item_id' => $description['id']])->andWhere(['status' => ['14', '16', '13']])->orderBy(['supplier_price' => SORT_ASC])
            ->all();

        $bidIdarray = ArrayHelper::map($countbid2, 'supplier_id', function ($model) {
            return $model['supplier_id'];
        });

        $suppliers = Supplier::find()->where(['id' => $bidIdarray])->all();

        $count = BiddingList::find()->select(['supplier_id'])->where(['supplier_id' => $bidIdarray])->distinct();
        $countSupplier = $count->count();

        // $description->status = 16;
        // // $model->status = 16;
        // $prDetails->status = 7;

        // $itemHistorylog = new ItemHistoryLogs();
        // $historylog = new HistoryLog();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_resolcrb_pb_pdf', [
            // 'quotation' => $quotation,
            'model' => $model,
            'prDetails' => $prDetails,
            'countSupplier' => $countSupplier,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'description' => $description,
            'suppliers' => $suppliers,
            'prebid' => $prebid,
            'openbid' => $openbid,
            'posted' => $posted,
            'preproc' => $preproc,
            'complyBidcount' => $complyBidcount
            // 'rante' => $rante,
        ]);

        $head = '
              <div>
              <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"/>
              </div>
           ';

        $foot = '
            <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
            Page {PAGENO} of {nb} <br>
              RESOLUTION NO. ' . $model->resolution_no . '
              </div> <br>
                  <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
           ';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-LCRB RESOLUTION FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-LCRB RESOLUTION FORM',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);
        return $pdf->render();
    }

    // LCRB reso for INFRA
    public function actionBacBiddingresolutionlcrbinfraPdf()
    {
        $model = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $prDetails = PurchaseRequest::find()
            ->where(['id' => $model->pr_id])
            ->one();

        $prebid = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 3])->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 4])->orderBy(['id' => SORT_DESC])
            ->one();

        $posted = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 2])->orderBy(['id' => SORT_DESC])
            ->one();

        $preproc = Quotation::find()
            ->where(['pr_id' => $prDetails->id])->andWhere(['option_id' => 1])->orderBy(['id' => SORT_DESC])
            ->one();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $complyBidcount = BiddingList::find()
            ->where(['item_id' => $description['id']])->andWhere(['status' => ['14', '16']])->orderBy(['supplier_price' => SORT_ASC])
            ->all();

        $countbid2 = BiddingList::find()
            ->where(['item_id' => $description['id']])->andWhere(['status' => ['14', '16', '13']])->orderBy(['supplier_price' => SORT_ASC])
            ->all();

        $bidIdarray = ArrayHelper::map($countbid2, 'supplier_id', function ($model) {
            return $model['supplier_id'];
        });

        $suppliers = Supplier::find()->where(['id' => $bidIdarray])->all();

        $count = BiddingList::find()->select(['supplier_id'])->where(['supplier_id' => $bidIdarray])->distinct();
        $countSupplier = $count->count();

        // $description->status = 16;
        // // $model->status = 16;
        // $prDetails->status = 7;

        // $itemHistorylog = new ItemHistoryLogs();
        // $historylog = new HistoryLog();

        $pdf_content = $this->renderPartial('/adm-bac/infra_bidding_resolcrb_pdf', [
            // 'quotation' => $quotation,
            'model' => $model,
            'prDetails' => $prDetails,
            'countSupplier' => $countSupplier,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'description' => $description,
            'suppliers' => $suppliers,
            'prebid' => $prebid,
            'openbid' => $openbid,
            'posted' => $posted,
            'preproc' => $preproc,
            'complyBidcount' => $complyBidcount
        ]);

        $head = '
                 <div>
                 <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"/>
                 </div>
              ';

        $foot = '
                <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
                Page {PAGENO} of {nb} <br>
                 RESOLUTION NO. ' . $model->resolution_no . '
                 </div> <br>
                     <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
              ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-INFRA LCRB RESOLUTION FORM',
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
                'SetTitle' => 'ITDI-INFRA LCRB RESOLUTION FORM',
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

    // small value resolution
    public function actionBacBiddingresolutionPdf()
    {
        $model = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $resolution = Resolution::find()
            ->where(['id' => $model->resolution_no])
            ->one();

        $allBiddinglist = BiddingList::find()
            ->where(['resolution_no' => $resolution->id])
            ->all();

        $prDetails = PurchaseRequest::find()
            ->where(['id' => $model->pr_id])
            ->one();

        $quotation = Quotation::find()
            ->where(['pr_id' => $prDetails->id])
            ->andWhere(['option_id' => ['4', '11', '12']])
            ->orderBy(['option_date' => SORT_ASC])
            ->all();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(
                ['id' => SORT_DESC]
            )->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_resolution_pdf', [
            'quotation' => $quotation,
            'model' => $model,
            'prDetails' => $prDetails,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'description' => $description,
            'allBiddinglist' => $allBiddinglist,
            'resolution' => $resolution
        ]);

        $head = '
            <div>
            <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . ' /header_reso.png"/>
            </div>
         ';

        $foot = '
            <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
            Page {PAGENO} of {nb} <br>
            RESOLUTION NO.: ' . $resolution->resolution_no . '
            </div> 
                <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . ' /footer_doc.png"/>
         ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'filename' => 'ITDI-BAC RESOLUTION FORM',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 30,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // 'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-BAC RESOLUTION  FORM',
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

    // small value resolution for pdea permit
    public function actionBacBiddingresopdeapermitPdf()
    {
        $model = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $resolution = Resolution::find()
            ->where(['id' => $model->resolution_no])
            ->one();

        $allBiddinglist = BiddingList::find()
            ->where(['resolution_no' => $resolution->id])
            ->all();

        $prDetails = PurchaseRequest::find()
            ->where(['id' => $model->pr_id])
            ->one();

        $quotation = Quotation::find()
            ->where(['pr_id' => $prDetails->id])
            ->one();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(
                ['id' => SORT_DESC]
            )->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $countbid = BiddingList::find()
            ->where(['item_id' => $description['id']])
            ->all();

        $bidIdarray = ArrayHelper::map($countbid, 'supplier_id', function ($model) {
            return $model['supplier_id'];
        });

        $count = BiddingList::find()
            ->select(['supplier_id'])
            ->where(['supplier_id' => $bidIdarray])
            ->distinct();

        $countSupplier = $count->count();


        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_resopdeapermit_pdf', [
            'quotation' => $quotation,
            'model' => $model,
            'prDetails' => $prDetails,
            'countSupplier' => $countSupplier,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'description' => $description,
            'allBiddinglist' => $allBiddinglist,
            'resolution' => $resolution
        ]);

        $head = '
               <div>
               <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/header_reso.png"/>
               </div>
            ';

        $foot = '
            <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
            Page {PAGENO} of {nb} <br>
               RESOLUTION NO.:' . $resolution->resolution_no . '
               </div> <br>
                   <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
            ';

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-BAC RESOLUTION (PDEA Permit) FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 50,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-BAC RESOLUTION (PDEA Permit) FORM',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // small value resolution for canvass
    public function actionBacBiddingresocanvassPdf()
    {
        $model = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $resolution = Resolution::find()
            ->where(['id' => $model->resolution_no])
            ->one();

        $allBiddinglist = BiddingList::find()
            ->where(['resolution_no' => $resolution->id])
            ->all();

        $prDetails = PurchaseRequest::find()
            ->where(['id' => $model->pr_id])
            ->one();

        $quotation = Quotation::find()
            ->where(['pr_id' => $prDetails->id])
            ->andWhere(['option_id' => ['4']])
            ->orderBy(['option_date' => SORT_DESC])
            ->one();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(
                ['id' => SORT_DESC]
            )->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $description = PrItems::find()
            ->where(['id' => $model->item_id])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_resocanvass_pdf', [
            'quotation' => $quotation,
            'model' => $model,
            'prDetails' => $prDetails,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'description' => $description,
            'allBiddinglist' => $allBiddinglist,
            'resolution' => $resolution
        ]);

        $head = '
                <div>
                <img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/header_reso.png"/>
                </div>
             ';

        $foot = '
            <div style="text-align:center; font-style: normal; font-family: Arial, Helvetica, sans-serif; font-weight: normal;">
            Page {PAGENO} of {nb} <br>
                RESOLUTION NO.: ' . $resolution->resolution_no . '
                </div> <br>
                    <img style="width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/footer_doc.png"/>
             ';


        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-BAC RESOLUTION (Canvass) FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 50,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-BAC RESOLUTION (Canvass) FORM',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    public function actionBacBiddingitemdetailsCreate()
    {
        $description = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $description->pr_id])
            ->one();

        $quotationNew = new Quotation();

        $quotation = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->asArray()->all();

        $preprocQuery = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbidQuery = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgepsQuery = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebidQuery = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $preprocCondition = ($preprocQuery != NULL ? $preprocQuery : $quotationNew);
        $openbidCondition = ($openbidQuery != NULL ? $openbidQuery : $quotationNew);
        $philgepsCondition = ($philgepsQuery != NULL ? $philgepsQuery : $quotationNew);
        $prebidCondition = ($prebidQuery != NULL ? $prebidQuery : $quotationNew);

        if ($description->load(Yii::$app->request->post()) && $purchaserequest->load(Yii::$app->request->post()) && $preprocCondition->load(Yii::$app->request->post()) && $openbidCondition->load(Yii::$app->request->post()) && $philgepsCondition->load(Yii::$app->request->post()) && $prebidCondition->load(Yii::$app->request->post())) {
            $isValid = $description->validate();
            $isValid = $purchaserequest->validate() && $isValid;

            if ($isValid) {
                $purchaserequest->delivery_period = $purchaserequest->delivery_period;
                $purchaserequest->charge_to = $purchaserequest->charge_to;
                $description->bid_title = $description->bid_title;
                $description->bidding_docs_fee = $description->bidding_docs_fee;
                $purchaserequest->save();
                $description->save();
            }

            Yii::$app->getSession()->setFlash('success', 'Success');
            return $this->redirect(Yii::$app->request->referrer);
        }

        return $this->renderAjax('/adm-bac/pr_bidding_itemdetails_create', [
            'purchaserequest' => $purchaserequest,
            'description' => $description,
            'preprocCondition' => $preprocCondition,
            'openbidCondition' => $openbidCondition,
            'philgepsCondition' => $philgepsCondition,
            'prebidCondition' => $prebidCondition
        ]);
    }

    // Public Bidding goods
    public function actionBacInvitationtobidpbPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $preproc = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgeps = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebid = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_invitationpb_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
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
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-BAC Bid Invitation Form',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 40,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-BAC Bid Invitation Form',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // INFRA
    public function actionBacInvitationtobidinfraPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $preproc = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgeps = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebid = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        // computation of time less 30mins from opening date
        $openingDate = $openbid->option_date;
        $openingTime = strtotime($openingDate);
        $calculateTime = $openingTime - (30 * 60);
        $submissionTime = date("Y-m-d H:i:s", $calculateTime);

        // computation of time add 1min from opening date
        $openingDate2 = $openbid->option_date;
        $openingTime2 = strtotime($openingDate2);
        $calculateTime2 = $openingTime2 + (1 * 60);
        $openingTime = date("Y-m-d H:i:s", $calculateTime2);

        $pdf_content = $this->renderPartial('/adm-bac/infra_bidding_invitation_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
            'submissionTime' => $submissionTime,
            'openingTime' => $openingTime

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
            'filename' => 'ITDI-INFRA Bid Invitation Form',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 40,
            'marginLeft' => 20,
            'marginRight' => 20,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-INFRA Bid Invitation Form',
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

    // Public bidding goods
    public function actionBacBiddatasheetpbPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $quot = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->one();

        $preproc = Quotation::find()->where(['option_id' => 1])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $openbid = Quotation::find()->where(['option_id' => 4])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $philgeps = Quotation::find()->where(['option_id' => 2])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $prebid = Quotation::find()->where(['option_id' => 3])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();


        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_datasheetpb_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
            'quot' => $quot
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-BAC Bid Data Sheet',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 20,
            'marginBottom' => 20,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-BAC Bid Data Sheet',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                // 'SetHeader' => [$head],
                'SetFooter' => ['|{PAGENO}|'],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        // $mpdf->setHeader($head);
        $mpdf->SetFooter('|Page {PAGENO}|');
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // Infra
    public function actionBacBiddatasheetinfraPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $quot = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->one();

        $preproc = Quotation::find()->where(['option_id' => 1])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $openbid = Quotation::find()->where(['option_id' => 4])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $philgeps = Quotation::find()->where(['option_id' => 2])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $prebid = Quotation::find()->where(['option_id' => 3])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();

        $openingDate = $openbid->option_date;
        $openingTime = strtotime($openingDate);
        $calculateTime = $openingTime - (30 * 60);
        $submissionTime = date("Y-m-d H:i:s", $calculateTime);

        // computation of time add 1min from opening date
        $openingDate2 = $openbid->option_date;
        $openingTime2 = strtotime($openingDate2);
        $calculateTime2 = $openingTime2 + (1 * 60);
        $openingTime = date("Y-m-d H:i:s", $calculateTime2);

        $pdf_content = $this->renderPartial('/adm-bac/infra_bidding_datasheet_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
            'quot' => $quot,
            'submissionTime' => $submissionTime,
            'openingTime' => $openingTime
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-INFRA Bid Data Sheet',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 20,
            'marginBottom' => 20,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-INFRA Bid Data Sheet',
                'SetFooter' => ['|{PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->SetFooter('|Page {PAGENO}|');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // public bidding goods
    public function actionBacSpecialconditionpbPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $quot = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->one();

        $preproc = Quotation::find()->where(['option_id' => 1])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $openbid = Quotation::find()->where(['option_id' => 4])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $philgeps = Quotation::find()->where(['option_id' => 2])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $prebid = Quotation::find()->where(['option_id' => 3])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();


        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_specialconditionpb_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
            'quot' => $quot
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-BAC Special Condition of Contract',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 20,
            'marginBottom' => 10,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-BAC Special Condition of Contract',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                // 'SetHeader' => [$head],
                'SetFooter' => ['|{PAGENO}|'],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        // $mpdf->setHeader($head);
        $mpdf->SetFooter('|Page {PAGENO}|');
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // infra
    public function actionBacSpecialconditioninfraPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $quot = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->one();

        $preproc = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgeps = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebid = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/infra_bidding_specialcondition_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
            'quot' => $quot
        ]);

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI-INFRA Special Condition of Contract',
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 20,
            'marginBottom' => 10,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            'options' => ['title' => 'Krajee Report Title'],
            'methods' => [
                'SetTitle' => 'ITDI-INFRA Special Condition of Contract',
                'SetFooter' => ['|{PAGENO}|'],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->SetFooter('|Page {PAGENO}|');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // Technical Specification
    public function actionBacTechspecificationpbPdf()
    {

        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $itemSpecs = ItemSpecification::find()->where(['item_id' => $items['id']])->all();

        $preproc = Quotation::find()->where(['option_id' => 1])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $openbid = Quotation::find()->where(['option_id' => 4])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $philgeps = Quotation::find()->where(['option_id' => 2])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();
        $prebid = Quotation::find()->where(['option_id' => 3])->andWhere(['pr_id' => $purchaserequest['id']])->orderBy(['id' => SORT_DESC])->one();

        $itemAll = PrItems::find()->where(['pr_id' => $purchaserequest['id']])->all();
        $itemId = ArrayHelper::map($itemAll, 'pr_id', function ($model) {
            return $model['pr_id'];
        });
        $count = PrItems::find()->select(['pr_id'])->where(['pr_id' => $itemId])->distinct();
        $countItem = $count->count();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_techspecificationspb_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
            'countItem' => $countItem,
            'itemSpecs' => $itemSpecs
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-Technical Specification Form',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 20,
            'marginBottom' => 20,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-Technical Specification Form',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                // 'SetHeader' => [$head],
                // 'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        // $mpdf->setHeader($head);
        // $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // Checklist of Financial Envelope
    public function actionBacFinancialenvelopePdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $preproc = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgeps = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebid = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_financial_envelope_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
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
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-Checklist PB Financial',
            // A4 paper format
            'format' => Pdf::FORMAT_LEGAL,
            'marginTop' => 40,
            'marginBottom' => 40,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-Checklist PB Financial',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // Checklist of Technical Requirements page 1
    public function actionBacTechnicalrequirementsPdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $preproc = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgeps = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebid = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_technical_requirements_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
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
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-Checklist of Public Bidding pg 1',
            // A4 paper format
            'format' => Pdf::FORMAT_LEGAL,
            'marginTop' => 40,
            'marginBottom' => 40,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-Checklist of Public Bidding pg 1',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    // Checklist of Technical Requirements page 2
    public function actionBacTechnicalrequirements2Pdf()
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $preproc = Quotation::find()
            ->where(['option_id' => 1])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $openbid = Quotation::find()
            ->where(['option_id' => 4])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $philgeps = Quotation::find()
            ->where(['option_id' => 2])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $prebid = Quotation::find()
            ->where(['option_id' => 3])
            ->andWhere(['pr_id' => $purchaserequest['id']])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_technical_requirements_page2_pdf', [
            'purchaserequest' => $purchaserequest,
            'preproc' => $preproc,
            'openbid' => $openbid,
            'philgeps' => $philgeps,
            'prebid' => $prebid,
            'items' => $items,
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
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-Checklist of Public Bidding pg 2',
            // A4 paper format
            'format' => Pdf::FORMAT_LEGAL,
            'marginTop' => 40,
            'marginBottom' => 40,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-Checklist of Public Bidding pg 2',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    public function actionBulletinpdf()
    {
        $selectedKeys = explode(',', Yii::$app->request->get('selected'));
        $sample = PrItems::find()->where(['id' => $selectedKeys])->andWhere(['not', ['status' => ['35', '38', '43', '21']]])->all();

        $model = PurchaseRequest::find()->where(['id' => $_GET['id']])->one();
        $prebid = Quotation::find()->where(['pr_id' => $model['pr_no']])->andWhere(['option_id' => '3'])->one();
        $openbid = Quotation::find()->where(['pr_id' => $model['pr_no']])->andWhere(['option_id' => '4'])->one();

        $dataProvider2 = new ArrayDataProvider([
            'allModels' => $sample,
        ]);

        $pdf_content = $this->renderPartial('bulletin_pdf', [
            'model' => $model,
            'dataProvider2' => $dataProvider2,
            'sample' => $sample,
            'prebid' => $prebid,
            'openbid' => $openbid
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
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-BAC Bid Invitation Form',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 40,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-BAC Bid Invitation Form',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        // $mpdf->SetTitle('Request Form');
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        $mpdf->WriteHtml(file_get_contents(Yii::getAlias('@vendor') . '/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css'), 1);

        return $pdf->render();
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = BiddingList::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionBacBiddingabstractPdf()
    {
        $purchaserequest = PurchaseRequest::findOne($_GET['id']);

        if (!$purchaserequest) {
            throw new NotFoundHttpException('Purchase request not found.');
        }

        $descriptiontest = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => [10, 11, 12, 13, 14, 15]])
            // ->andWhere(['status' => [13, 14, 15]])
            ->all();


        $biddingList = BiddingList::find()
            ->where(['pr_id' => $_GET['id']])
            ->andWhere(['status' => 13])
            // ->orderBy(['time_stamp' => SORT_DESC])
            ->all();

        $desId = ArrayHelper::map($descriptiontest, 'id', 'id');
        $bidId = ArrayHelper::map($biddingList, 'supplier_id', 'supplier_id');

        $suppliers = Supplier::find()
            ->where(['id' => $bidId])
            ->all();

        $quot = Quotation::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['option_id' => '4'])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->one();

        $rebid = Quotation::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['option_id' => '6'])
            ->orderBy(['time_stamp' => SORT_DESC])
            ->one();

        $latest_assignatory = BacSignatories::find()
            ->orderBy(['id' => SORT_DESC])
            ->one();

        $members = MemberSignatories::find()
            ->where(['bac_signatories_id' => $latest_assignatory->id])
            ->all();

        $purchaserequest->status = 7;
        // $pageSize = 2;

        $dataProvider = new ArrayDataProvider([
            'allModels' => $suppliers,
            // 'pagination' => [
            //     'pageSize' => $pageSize
            // ],
        ]);

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_abstract_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'quot' => $quot,
            'biddingList' => $biddingList,
            'desId' => $desId,
            'suppliers' => $suppliers,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'bidId' => $bidId,
            'dataProvider' => $dataProvider,
            'rebid' => $rebid
        ]);

        $head = '
        <div class="abstract" style="text-align:center; font-weight: normal; font-style: normal; font-size: 10pt; margin-top: 20px">
            <i>Republic of the Philippines</i> <br>
            Department of Science and Technology <br>
            <strong>INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE</strong> <br>
            <span style="font-size: 11pt"><strong>ABSTRACT OF SPECIAL CANVASS</strong></span>
        </div>
        
        <div class="abstract-body">
            <div style="text-align:right; font-weight: normal; font-style: normal;">
                <span style="font-size: 11pt;">
                    ' . ($rebid === NULL ? '' : '<br>Date Opening of Rebid: <strong>' . Yii::$app->formatter->asDatetime(strtotime($rebid->option_date), 'php:M d, Y')) . '</strong><br>
                    Date Opening: <strong>' . Yii::$app->formatter->asDatetime(strtotime($quot->option_date), 'php:M d, Y') . '</strong> <br>
                    Solicitation No.: <strong>' . $quot->quotation_no . '</strong> <br>
                    PR No.: <strong>' . $purchaserequest->pr_no . '</strong> <br>
                </span>
            </div>
        </div>';
        

        $foot = '<div style = "text-align: left; padding-bottom: 5%">
                    <i>Noted by:</i>
                </div>
    
    <table class="signatories">
        <tr>
            <td style="padding-right: 30px"><strong> <u> ERIC M. CHARLON </u></strong> </td>
            <td style="padding-right: 30px"><strong> <u> MARGARITA V. ATIENZA</u></strong></td>
            <td style="padding-right: 30px"><strong> <u> RODA MAE O. URMENETA </u> </strong> </td>
            <td style="padding-right: 30px"><strong> <u> MONICA R. MANALO</u></strong></td>
            <td style="padding-right: 30px"><strong> <u> ABIGAIL GRACE H. BION</u></strong> </td>
            <td style="padding-right: 30px"><strong> <u> SANIELOU V. JARDIN</u></strong></td>
            <td style="padding-right: 30px"><strong> <u> JENNY LYN H. LAGA</u></strong></td>
        </tr>
        <tr>
            <td style="padding-right: 30px; text-align: center"> Chairperson </td>
            <td style="padding-right: 30px; text-align: center"> Co-Chairperson</td>
            <td style="padding-right: 30px; text-align: center"> Member </td>
            <td style="padding-right: 30px; text-align: center"> Member</td>
            <td style="padding-right: 30px; text-align: center"> Member </td>
            <td style="padding-right: 30px; text-align: center"> Member</td>
            <td style="padding-right: 30px; text-align: center"> Member</td>
        </tr>
    </table><br><br>

        <table style="font-size: 7pt; border: none">
        <tr><td><strong>' . $purchaserequest->enduser . '</strong></td></tr>
        <tr><td>' . ($purchaserequest->charge_to == 0 || $purchaserequest->charge_to == NULL  ? 'GAA' : $purchaserequest->chargedisplay->project_title) . '</td></tr>
            </table>';


        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'filename' => 'ABSTRACT_OF_SPECIAL_CANVASS_FORM.pdf',
            'format' => [215.9, 330],
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'destination' => Pdf::DEST_BROWSER,
            'marginRight' => 3,
            'marginLeft' => 3,
            'marginTop' => 55,
            'marginBottom' => 55,
            'content' => $pdf_content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // 'options' => [
            //     'title' => 'ITDI-Abstract Form',
            //     'splitTable' => true,
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-Abstract Form',
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

    // abstract btn in public bidding bidders
    public function actionBacBiddingabstractpbPdf($id)
    {
        $items = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $items['pr_id']])
            ->one();

        $biddingList = BiddingList::find()
            ->where(['item_id' => $items['id']])
            ->andWhere(['status' => ['13']])
            ->all();

        $bidId = ArrayHelper::map($biddingList, 'supplier_id', function ($model) {
            return $model['supplier_id'];
        });

        $suppliers = Supplier::find()->where(['id' => $bidId])->all();

        $quot = Quotation::find()->where(['pr_id' => $purchaserequest->id])->andWhere(['option_id' => 4])->one();

        $latest_assignatory = BacSignatories::find()->where(['bid_id' => $id])->one();

        $members = MemberSignatories::find()->where(['bac_signatories_id' => $latest_assignatory->id])->all();
        // var_dump($members);die;
        $purchaserequest->status = 7;

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_abstractpb_pdf', [
            'purchaserequest' => $purchaserequest,
            'items' => $items,
            'quot' => $quot,
            'biddingList' => $biddingList,
            // 'desId' => $desId,
            'suppliers' => $suppliers,
            'latest_assignatory' => $latest_assignatory,
            'members' => $members,
            'bidId' => $bidId,
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ABSTRACT FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ]
            'methods' => [
                'SetTitle' => 'ITDI-Abstract Form',
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    // generate evaluation report (bidding item list)
    public function actionBacEvalutionreportPdf()
    {
        $item = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $item->pr_id])
            ->one();

        $itemSpecs = ItemSpecification::find()
            ->where(['item_id' => $item['id']])
            ->all();

        $items = PrItems::find()
            ->where(['pr_id' => $purchaserequest->id])
            ->andWhere(['status' => ['13']])->all();

        $biddingList = BiddingList::find()
            ->where(['item_id' => $item['id']])
            ->andWhere(['status' => ['13']])
            ->all();

        $itemHistoryLog = new ItemHistoryLogs();
        $historylog = new HistoryLog();

        $bidId = ArrayHelper::map($biddingList, 'supplier_id', function ($model) {
            return $model['supplier_id'];
        });

        $suppliers = Supplier::find()->where(['id' => $bidId])->all();

        $quot = Quotation::find()->where(['pr_id' => $purchaserequest->id])->one();

        $count = BiddingList::find()->select(['supplier_id'])->where(['supplier_id' => $bidId])->distinct();

        $countSupplier = $count->count();

        $purchaserequest->status = 7;
        $item->status = 13;
        $item->save();

        $itemHistoryLog->item_id = $item->id;
        $itemHistoryLog->action_date = date('Y-m-d h:i');
        $itemHistoryLog->action_status = 'Evaluation Report';
        $itemHistoryLog->action_by = Yii::$app->user->identity->id;
        $itemHistoryLog->action_remarks = $_POST['remarks'];
        $itemHistoryLog->save();

        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'Evaluation Report';

        $historylog->save();
        $purchaserequest->save();

        $pdf_content = $this->renderPartial('/adm-bac/pr_bidding_evaluationreport_pdf', [
            'purchaserequest' => $purchaserequest,
            'item' => $item,
            'quot' => $quot,
            'biddingList' => $biddingList,
            'itemSpecs' => $itemSpecs,
            'countSupplier' => $countSupplier,
            'items' => $items,
            'suppliers' => $suppliers,
            // 'latest_assignatory' => $latest_assignatory,
            // 'members' => $members,
            'bidId' => $bidId,
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'Evaluation Report',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ]
            'methods' => [
                'SetTitle' => 'Evaluation Report',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                // 'SetHeader' => ['Krajee Privacy Policy||Generated On: ' . date("r")],
                // 'SetFooter' => ['|Page {PAGENO}|'],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);
        // return the pdf output as per the destination setting
        return $pdf->render();
    }

    // BAC Evaluation Btn
    public function actionBacEvaluationRemarks()
    {
        $id = Yii::$app->request->post('id');
        $items = PrItems::findOne($id);

        if (!$items) {
            Yii::$app->getSession()->setFlash('error', 'Item not found.');
            return $this->redirect(['purchase-request/bac-request-index']);
        }

        $purchaserequest = PurchaseRequest::findOne($items->pr_id);

        if (!$purchaserequest) {
            Yii::$app->getSession()->setFlash('error', 'Purchase request not found.');
            return $this->redirect(['purchase-request/bac-request-index']);
        }

        $items->status = 48;
        $purchaserequest->status = 48;

        $itemHistorylog = new ItemHistoryLogs();
        $itemHistorylog->item_id = $items->id;
        $itemHistorylog->action_date = date('Y-m-d h:i');
        $itemHistorylog->action_status = $items->status;
        $itemHistorylog->action_by = Yii::$app->user->identity->id;
        $itemHistorylog->action_remarks = Yii::$app->request->post('remarks');

        if ($items->save() && $purchaserequest->save() && $itemHistorylog->save()) {
            $historylog = new HistoryLog();
            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = '(Check history logs of items for remarks)';

            if ($historylog->save()) {
                Yii::$app->getSession()->setFlash('success', 'Success');
            } else {
                Yii::$app->getSession()->setFlash('error', 'Failed to save history log.');
            }
        } else {
            Yii::$app->getSession()->setFlash('error', 'Failed to save items or purchase request or history log.');
        }

        return $this->redirect(['purchase-request/bac-request-index']);
    }

    // End-user Evaluation Btn
    public function actionEnduserEvaluationRemarks()
    {
        $id = Yii::$app->request->post('id');
        $items = PrItems::findOne($id);

        if (!$items) {
            Yii::$app->getSession()->setFlash('error', 'Item not found.');
            return $this->redirect(['purchase-request/on-process-request-index']);
        }

        $purchaserequest = PurchaseRequest::findOne($items->pr_id);

        if (!$purchaserequest) {
            Yii::$app->getSession()->setFlash('error', 'Purchase request not found.');
            return $this->redirect(['purchase-request/on-process-request-index']);
        }

        $items->status = 49;
        $purchaserequest->status = 49;

        $itemHistorylog = new ItemHistoryLogs();
        $itemHistorylog->item_id = $items->id;
        $itemHistorylog->action_date = date('Y-m-d h:i');
        $itemHistorylog->action_status = $items->status;
        $itemHistorylog->action_by = Yii::$app->user->identity->id;
        $itemHistorylog->action_remarks = Yii::$app->request->post('remarks');

        if (!$itemHistorylog->save()) {
            Yii::$app->getSession()->setFlash('error', 'Failed to save item history log.');
            return $this->redirect(['purchase-request/on-process-request-index']);
        }

        $historylog = new HistoryLog();
        $historylog->pr_id = $purchaserequest->id;
        $historylog->action_date = date('Y-m-d h:i');
        $historylog->action_status = $purchaserequest->status;
        $historylog->action_user_id = Yii::$app->user->identity->id;
        $historylog->remarks = 'Done Evaluation of Bidders';

        if (!$historylog->save()) {
            Yii::$app->getSession()->setFlash('error', 'Failed to save history log.');
            return $this->redirect(['purchase-request/on-process-request-index']);
        }

        Yii::$app->getSession()->setFlash('success', 'Success');
        return $this->redirect(['purchase-request/on-process-request-index']);
    }

    public function actionPpmsBiddingawardlist()
    {

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $biddingQuery = BiddingList::find()
            ->where(['pr_id' => $_GET['id']]);

        $searchModel = new PurchaseOrderSearch();
        $dataProvider2 = $searchModel->ppms(Yii::$app->request->queryParams, $_GET['id']);

        $dataProvider = new ActiveDataProvider([
            'query' => $biddingQuery,
        ]);

        return $this->render('/adm-procurement/pr_bidding_award_list', [
            'purchaserequest' => $purchaserequest,
            'dataProvider' => $dataProvider,
            'dataProvider2' => $dataProvider2,
            'bidding' => $biddingQuery->all(),
        ]);
    }

    // Purchase Order Process
    public function actionPpmsPurchaseorderCreate()
    {
        $purchaseOrder = new PurchaseOrder();
        $modelItems = new PurchaseOrderItems();
        $modelDeductions = [new LessDeductions()];
        $model_deductions = LessDeductions::find()->where(['deduction_id' => $purchaseOrder->id])->all();
        $modelAdditions = [new AdditionalServices()];
        $model_additions = AdditionalServices::find()->where(['add_service_id' => $purchaseOrder->id])->all();

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

        $sequence = DivisionFormSequence::find()->where(['division_code' => 'PO'])->one();
        $ponumber  = $sequence->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($sequence->form_sequence, 4, '0', STR_PAD_LEFT);
        $sequence->form_sequence = $sequence->form_sequence + 1;
        $purchaseOrder->po_no = $ponumber;

        // $orsburs = DivisionFormSequence::find()->where(['division_code' => 'TF'])->one();
        // $orsbursnumber  = $orsburs->division_code . '-' . date('Y') .  '-' . date('m') .  '-' .  str_pad($orsburs->form_sequence, 4, '0', STR_PAD_LEFT);
        // $orsburs->form_sequence = $sequence->form_sequence + 1;
        // $purchaseOrder->ors_burs_num = $orsbursnumber;

        $historylog = new HistoryLog();
        $biddingLists = Yii::$app->request->post('BiddingList');


        if ($purchaseOrder->load(Yii::$app->request->post())) {
            // $purchaserequest = PurchaseRequest::find()->where(['id' => $purchaseOrder['pr_id']])->one();
            // $bidding = BiddingList::find()->where(['id' => $purchaseOrder->bid_id])->one();
            $purchaserequest->status = 7;

            $purchaseOrder->po_status = 21;
            $purchaseOrder->created_by = Yii::$app->user->identity->id;
            $purchaseOrder->pr_id = $purchaserequest->id;
            $purchaseOrder->supplier_id = $bidding->supplier_id;
            $purchaseOrder->bid_id = $bidding->id;

            $modelDeductions = Model::createMultiple(LessDeductions::classname());
            Model::loadMultiple($modelDeductions, Yii::$app->request->post());

            $valid = $purchaseOrder->validate();
            $valid = Model::validateMultiple($modelDeductions) && $valid;

            $modelAdditions = Model::createMultiple(AdditionalServices::classname());
            Model::loadMultiple($modelAdditions, Yii::$app->request->post());

            $valid = $purchaseOrder->validate();
            $valid = Model::validateMultiple($modelAdditions) && $valid;

            $sequence->save();
            // $orsburs->save();
            $purchaseOrder->save();
            $purchaserequest->save();

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $purchaseOrder->save()) {
                        foreach ($modelDeductions as $modelLessDeductions) {
                            $modelLessDeductions->po_id = $purchaseOrder->id;
                            if (!($flag = $modelLessDeductions->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        foreach ($modelAdditions as $modelAddservices) {
                            $modelAddservices->po_id = $purchaseOrder->id;
                            if (!($flag = $modelAddservices->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        foreach ($sample as $modelBidding) {
                            $description = PrItems::find()->where(['id' => $modelBidding->item_id])->one();

                            $modelBidding->item_remarks = $modelBidding->item_remarks;
                            $modelBidding->status = 21;

                            $description->status = 21;
                            $modelItems = new PurchaseOrderItems();
                            $itemHistorylog = new ItemHistoryLogs();

                            $modelItems->po_id = $purchaseOrder->id;
                            $modelItems->bid_id = $modelBidding['id'];
                            $modelItems->isNewRecord = true;
                            $modelItems->save();
                            $modelBidding->save();
                            $description->save();



                            $itemHistorylog->item_id = $description->id;
                            $itemHistorylog->action_date = date('Y-m-d h:i');
                            $itemHistorylog->action_status = $modelBidding->status;
                            $itemHistorylog->action_by = Yii::$app->user->identity->id;

                            $itemHistorylog->save();
                        }
                    }
                    if ($flag) {
                        $transaction->commit();

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
            return $this->redirect(Yii::$app->request->referrer);
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
        $purchaseOrder->po_status = 32;
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
    public function actionPurchaseorderCancel()
    {
        $id = Yii::$app->request->post('id');

        $model = PurchaseOrder::find()
            ->where(['id' => $id])
            ->one();

        $bidding = BiddingList::find()->where(['id' => $model['bid_id']])->one();

        $purchaseRequest = PurchaseRequest::find()
            ->where(['id' => $bidding['pr_id']])
            ->one();

        $descriptions = PrItems::find()
            ->where(['id' => $bidding['item_id']])->all();

        $historylog = new HistoryLog();
        $itemHistoryLog = new ItemHistoryLogs();
        $purchaseRequest->status = 46;
        $model->po_status = 46;
        $model->save();
        $purchaseRequest->save();

        foreach ($descriptions as $description) {
            $description->status = 46;
            $description->save();

            $itemHistoryLog->item_id = $description->id;
            $itemHistoryLog->action_date = date('Y-m-d h:i');
            $itemHistoryLog->action_status = $description->status;
            $itemHistoryLog->action_by = Yii::$app->user->identity->id;
            $itemHistoryLog->save();
        }

        if ($model->save()) {
            $historylog->pr_id = $purchaseRequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->remarks = $_POST['remarks'];
            $historylog->action_status = $purchaseRequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;

            $historylog->save();

            return $this->redirect(['purchase-request/procurement-request-index']);
        }
    }


    // Budget-purchase order process
    // public function actionBudgetPoIndex()
    // {
    //     $model = new PurchaseOrder();
    //     $searchModel = new PurchaseOrderSearch();
    //     $dataProvider = $searchModel->fmd(Yii::$app->request->queryParams, $_GET['id']);

    //     return $this->renderAjax('/fmd-budget/budget_po_index', [
    //         'dataProvider' => $dataProvider,
    //         'searchModel' => $searchModel,
    //         'model' => $model,

    //     ]);
    // }

    public function actionBudgetPoObligate($fmd)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $fmd])->one();

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

        $description->status = 22;
        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 22;
        $bidding->status = 22;
        $purchaserequest->save();
        $description->save();
        $purchaseOrder->save();


        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem['bid_id']])->one();
                $description = PrItems::find()->where(['id' => $bidding['item_id']])->one();
                $bidding->status = 22;

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
                $bidding->save();
            }

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'FMD(BUDGET)- P.O. Obligated';


            $historylog->save();

            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // Accounting-purchase order process
    // public function actionAccountingPoIndex()
    // {
    //     $searchModel = new PurchaseOrderSearch();
    //     $dataProvider = $searchModel->accounting(Yii::$app->request->queryParams, $_GET['id']);

    //     return $this->renderAjax('/fmd-accounting/accounting_po_index', [
    //         'dataProvider' => $dataProvider,
    //         'searchModel' => $searchModel,

    //     ]);
    // }

    public function actionAccountingPoValidate($val)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $val])->one();

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

        $description->status = 23;
        $purchaserequest->status = 7;
        $purchaseOrder->po_status = 23;
        $bidding->status = 23;
        $purchaserequest->save();
        $description->save();
        $purchaseOrder->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem['bid_id']])->one();
                $description = PrItems::find()->where(['id' => $bidding['item_id']])->one();
                $bidding->status = 23;

                $itemHistorylog = new ItemHistoryLogs();

                $itemHistorylog->item_id = $description->id;
                $itemHistorylog->action_date = date('Y-m-d h:i');
                $itemHistorylog->action_status = $description->status;
                $itemHistorylog->action_by = Yii::$app->user->identity->id;
                $itemHistorylog->save();
                $bidding->save();
            }

            $historylog->pr_id = $purchaserequest->id;
            $historylog->action_date = date('Y-m-d h:i');
            $historylog->action_status = $purchaserequest->status;
            $historylog->action_user_id = Yii::$app->user->identity->id;
            $historylog->remarks = 'FMD(BUDGET)- P.O. Obligated';


            $historylog->save();

            return $this->redirect(Yii::$app->request->referrer);
        }
    }

    // Conforme process in PPMS
    public function actionPpmsPoSubmitforconforme($con)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $con])->one();

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
        $purchaseOrder->po_status = 24;
        $purchaseOrder->save();
        $purchaserequest->save();

        if ($purchaseOrder->save()) {

            foreach ($orderItem as $modelItem) {
                $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
                $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
                $bidding->status = 24;
                $description->status = 24;
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
        $historylog->remarks = 'PPMS- Conforme of Supplier';


        $historylog->save();

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionPpmsSuppliersconformeCreate()
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $_GET['id']])->one();

        $modelAttachments = new ConformeAttachments();
        $uploads = UploadedFile::getInstances($modelAttachments, 'files');

        if ($purchaseOrder->load(Yii::$app->request->post())) {

            foreach ($uploads as $upload) {

                $path = 'uploads/pr_conforme_files/' . md5($upload->baseName . date("m/d/y G:i:s:u")) . '.' . $upload->extension;

                if ($upload->saveAs($path)) {
                    $file_name = $upload->baseNAme . '.' . $upload->extension;
                    // $generated_file_name = md5($uploads->baseName . date("m/d/y G:i:s:u")) . '.' . $uploads->extension;
                    $file_extension = $upload->extension;
                    $file_directory = $path;
                    $po_id = $modelAttachments->po_id = $purchaseOrder->id;
                    Yii::$app->itdidb_procurement_system->createCommand()->insert('conforme_attachments', ['file_name' => $file_name, 'file_directory' => $file_directory, 'po_id' => $po_id, 'file_extension' => $file_extension])->execute();
                }
            }

            if ($purchaseOrder->save()) {

                $purchaseOrder->date_conforme = $purchaseOrder->date_conforme;
                return $this->redirect(Yii::$app->request->referrer);
            }
        }

        return $this->renderAjax('/adm-procurement/ppms_suppliersconforme_create', [
            'purchaseOrder' => $purchaseOrder,
            'modelAttachments' => $modelAttachments,

        ]);
    }

    // public function actionPpmsSuppliersconformeAccept($conId)
    // {
    //     $purchaseOrder = PurchaseOrder::find()->where(['id' => $conId])->one();

    //     $orderItem = PurchaseOrderItems::find()
    //         ->where(['po_id' => $purchaseOrder->id])->all();

    //     $purchaserequest = PurchaseRequest::find()
    //         ->where(['id' => $purchaseOrder->pr_id])
    //         ->one();

    //     $itemData = ArrayHelper::map($orderItem, 'bid_id', function ($model) {
    //         return $model['bid_id'];
    //     });

    //     $historylog = new HistoryLog();

    //     $purchaserequest->status = 7;
    //     $purchaseOrder->po_status = 34;
    //     $purchaseOrder->save();
    //     $purchaserequest->save();

    //     if ($purchaseOrder->save()) {

    //         foreach ($orderItem as $modelItem) {
    //             $bidding = BiddingList::find()->where(['id' => $modelItem->bid_id])->one();
    //             $description = PrItems::find()->where(['id' => $bidding->item_id])->one();
    //             $bidding->status = 34;
    //             $description->status = 34;
    //             $bidding->save();
    //             $description->save();

    //             $itemHistorylog = new ItemHistoryLogs();

    //             $itemHistorylog->item_id = $description->id;
    //             $itemHistorylog->action_date = date('Y-m-d h:i');
    //             $itemHistorylog->action_status = $description->status;
    //             $itemHistorylog->action_by = Yii::$app->user->identity->id;
    //             $itemHistorylog->save();
    //         }
    //     }

    //     $historylog->pr_id = $purchaserequest->id;
    //     $historylog->action_date = date('Y-m-d h:i');
    //     $historylog->action_status = $purchaserequest->status;
    //     $historylog->action_user_id = Yii::$app->user->identity->id;
    //     $historylog->remarks = 'PPMS- Accepted Conforme of Supplier';


    //     $historylog->save();

    //     return $this->redirect(Yii::$app->request->referrer);
    // }

    public function actionPpmsSuppliersconformeDecline($dec)
    {
        $purchaseOrder = PurchaseOrder::find()->where(['id' => $dec])->one();

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

        $orderItem = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->all();

        $orderItem1 = PurchaseOrderItems::find()
            ->where(['po_id' => $_GET['id']])->one();

        $lessDeduction = LessDeductions::find()
            ->where(['po_id' => $purchaseOrder['id']])->all();

        $addServices = AdditionalServices::find()
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

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_po_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'model' => $model,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItem' => $orderItem,
            'orderItem1' => $orderItem1,
            'lessDeduction' => $lessDeduction,
            'addServices' => $addServices
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-PURCHASE ORDER FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-PURCHASE ORDER FORM',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                // 'SetHeader' => [$head],
                // 'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        return $pdf->render();
    }

    // ORS
    public function actionPpmsOrsPdf()
    {
        $model = new PurchaseOrder();

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

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-procurement/ppms_ors_pdf', [
            'purchaserequest' => $purchaserequest,
            'descriptiontest' => $descriptiontest,
            'bidding' => $bidding,
            'model' => $model,
            'purchaseOrder' => $purchaseOrder,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'orderItem' => $orderItem,
            'orderItem1' => $orderItem1,
            'lessDeduction' => $lessDeduction
        ]);

        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_CORE,
            //Name for the file
            'filename' => 'ITDI-PURCHASE ORDER FORM',
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            'marginTop' => 40,
            'marginBottom' => 0,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_BROWSER,
            // your html content input
            'content' => $pdf_content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => 'Krajee Report Title'],
            // call mPDF methods on the fly
            // 'methods' => [
            //     'SetHeader'=>['Krajee Report Header'],
            //     'SetFooter'=>['{PAGENO}'],
            // ],
            'methods' => [
                'SetTitle' => 'ITDI-PURCHASE ORDER FORM',
                // 'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                // 'SetHeader' => [$head],
                // 'SetFooter' => [$foot],
                // 'SetAuthor' => 'Kartik Visweswaran',
                // 'SetCreator' => 'Kartik Visweswaran',
                // 'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        return $pdf->render();
    }

    // Notice to Proceed
    public function actionBacNtpPdf()
    {
        $bidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $bidding['pr_id']])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding['item_id']])
            ->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()
                ->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-bac/pr_ntp_pdf', [
            'purchaserequest' => $purchaserequest,
            'description' => $description,
            'bidding' => $bidding,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
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
       ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI- NOTICE TO PROCEED',
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
                'SetTitle' => 'ITDI- NOTICE TO PROCEED',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        return $pdf->render();
    }

    // Notice of Award
    public function actionBacNoaPdf()
    {
        $bidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $bidding['pr_id']])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding['item_id']])
            ->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()
                ->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-bac/pr_noa_pdf', [
            'purchaserequest' => $purchaserequest,
            'description' => $description,
            'bidding' => $bidding,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
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
        ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI- NOTICE OF AWARD',
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
                'SetTitle' => 'ITDI- NOTICE OF AWARD',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        return $pdf->render();
    }

    // Notice of Post Qualification
    public function actionBacNopqPdf()
    {
        $bidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $bidding['pr_id']])
            ->one();

        $openDate = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->andWhere(['option_id' => 4])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding['item_id']])
            ->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()
                ->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-bac/pr_nopq_pdf', [
            'purchaserequest' => $purchaserequest,
            'description' => $description,
            'bidding' => $bidding,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'openDate' => $openDate
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
         ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI- NOTICE OF POST QUALIFICATION',
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
                'SetTitle' => 'ITDI- NOTICE OF POST QUALIFICATION',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        return $pdf->render();
    }

    // Notice of Lowest Calculated Bid
    public function actionBacNolcbPdf()
    {
        $bidding = BiddingList::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $purchaserequest = PurchaseRequest::find()
            ->where(['id' => $bidding['pr_id']])
            ->one();

        $openDate = Quotation::find()
            ->where(['pr_id' => $purchaserequest['id']])
            ->andWhere(['option_id' => 4])
            ->one();

        $description = PrItems::find()
            ->where(['id' => $bidding['item_id']])
            ->one();

        $supplier = Supplier::find()
            ->where(['id' => $bidding->supplier_id])
            ->one();

        $dataProvider = new ActiveDataProvider([
            'query' => PrItems::find()
                ->where(['id' => $bidding->item_id])
        ]);

        $pdf_content = $this->renderPartial('/adm-bac/pr_nolcb_pdf', [
            'purchaserequest' => $purchaserequest,
            'description' => $description,
            'bidding' => $bidding,
            'dataProvider' => $dataProvider,
            'supplier' => $supplier,
            'openDate' => $openDate
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
          ';

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'filename' => 'ITDI- NOTICE OF LOWEST CALCULATED BID',
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
                'SetTitle' => 'ITDI- NOTICE OF LOWEST CALCULATED BID',
                'SetHeader' => [$head],
                'SetFooter' => [$foot],
            ]
        ]);

        $mpdf = $pdf->api;
        $mpdf->setHeader($head);
        $mpdf->SetFooter($foot);
        $mpdf->defaultfooterline = 0;
        $mpdf->defaultheaderline = 0;
        return $pdf->render();
    }
}
