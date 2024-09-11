<?php

namespace app\modules\PurchaseRequest\controllers;

use Yii;
use app\modules\PurchaseRequest\models\BacSignatories;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\MemberSignatories;
use app\modules\PurchaseRequest\models\Model;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * BacSignatoriesController implements the CRUD actions for BacSignatories model.
 */
class BacSignatoriesController extends Controller
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

    /**
     * Lists all BacSignatories models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => BacSignatories::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single BacSignatories model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionCreate($id)
    {
        $request = Yii::$app->request;
        $member = BacSignatories::find()->where(['bid_id' => $_GET['id']])->one();
        $authManager = Yii::$app->authManager;
        $usersWithRole = $authManager->getUserIdsByRole('BAC Members');

        if ($member == NULL) {
            $model = new BacSignatories();
            $modelmember_signatories = [new MemberSignatories];

            $member_signatories = MemberSignatories::find()->where(['bac_signatories_id' => $model->id])->all();
            // var_dump($member_signatories);die;
            $purchaseRequest = PurchaseRequest::find()->where(['id' => $_GET['id']])->one();

            $historylog = new HistoryLog();
            $purchaseRequest->status = 7;

            if ($model->load(Yii::$app->request->post())) {

                $modelmember_signatories = Model::createMultiple(MemberSignatories::classname());
                Model::loadMultiple($modelmember_signatories, Yii::$app->request->post());

                $valid = $model->validate();
                $valid = Model::validateMultiple($modelmember_signatories) && $valid;


                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($modelmember_signatories as $modelmembersignatories) {
                                $modelmembersignatories->bac_signatories_id = $model->id;
                                if (!($flag = $modelmembersignatories->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();

                            $historylog->pr_id = $purchaseRequest->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $purchaseRequest->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;
                            $historylog->remarks = 'BAC- Abstract';

                            $historylog->save();
                            $purchaseRequest->save();

                            return $this->redirect(['bidding/bac-biddingabstract-pdf', 'id' => $_GET['id']]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'modelmember_signatories' => (empty($modelmember_signatories)) ? [new MemberSignatories()] : $modelmember_signatories,
                'member_signatories' => $member_signatories,
                'usersWithRole' => $usersWithRole,
            ]);
        } else {

            $model = BacSignatories::find()->where(['bid_id' => $_GET['id']])->one();
            $modelmember_signatories = $model->bac;
            $member_signatories = MemberSignatories::find()->where(['bac_signatories_id' => $model->id])->all();
            $purchaseRequest = PurchaseRequest::find()->where(['id' => $_GET['id']])->one();
            $historylog = new HistoryLog();
            $purchaseRequest->status = 7;
            $authManager = Yii::$app->authManager;
            $usersWithRole = $authManager->getUserIdsByRole('BAC Members');

            if ($model->load(Yii::$app->request->post())) {
        
                $oldIDs = ArrayHelper::map($modelmember_signatories, 'id', 'id');
                $member_signatories = Model::createMultiple(MemberSignatories::className(), $member_signatories);
                Model::loadMultiple($member_signatories, Yii::$app->request->post());

                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($member_signatories, 'id', 'id')));

                $valid = $model->validate();
                $valid = Model::validateMultiple($member_signatories) && $valid;

                $model->save();
                $purchaseRequest->save();
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    $valid = []; {
                        try {
                            if ($valid[] = $model->save()) {
                                if (!empty($deletedIDs)) {
                                    MemberSignatories::deleteAll(['id' => $deletedIDs]);
                                }
                                foreach ($member_signatories as $member) {
                                    // var_dump($member);die;
                                    $member->bac_signatories_id = $model->id;
                                    $valid[] = $member->save();
                                }
                            }
                            Yii::$app->getSession()->setFlash('success', 'Success');
                            return $this->redirect(['bidding/bac-biddingabstract-pdf', 'id' => $_GET['id']]);
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
            }
            return $this->renderAjax('_form', [
                'model' => $model,
                'modelmember_signatories' => (empty($modelmember_signatories)) ? [new MemberSignatories()] : $modelmember_signatories,
                'member_signatories' => $member_signatories,
                'usersWithRole' => $usersWithRole,
            ]);
        }
    }


    /**
     * Updates an existing BacSignatories model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    //  for public bidding
    public function actionCreatesignatories($id)
    {
        $request = Yii::$app->request;
        $member = BacSignatories::find()->where(['bid_id' => $_GET['id']])->one();
        $authManager = Yii::$app->authManager;
        $usersWithRole = $authManager->getUserIdsByRole('BAC Members');

        if ($member == NULL) {
            $model = new BacSignatories();
            $modelmember_signatories = [new MemberSignatories];

            $member_signatories = MemberSignatories::find()->where(['bac_signatories_id' => $model->id])->all();
            $items = PrItems::find()->where(['id' => $_GET['id']])->one();
            $purchaseRequest = PurchaseRequest::find()->where(['id' => $items['pr_id']])->one();

            $historylog = new HistoryLog();
            $purchaseRequest->status = 7;

            if ($model->load(Yii::$app->request->post())) {
            
                $modelmember_signatories = Model::createMultiple(MemberSignatories::classname());
                Model::loadMultiple($modelmember_signatories, Yii::$app->request->post());

                $valid = $model->validate();
                $valid = Model::validateMultiple($modelmember_signatories) && $valid;


                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    try {
                        if ($flag = $model->save(false)) {
                            foreach ($modelmember_signatories as $modelmembersignatories) {
                                $modelmembersignatories->bac_signatories_id = $model->id;

                                if (!($flag = $modelmembersignatories->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                        if ($flag) {
                            $transaction->commit();

                            $historylog->pr_id = $purchaseRequest->id;
                            $historylog->action_date = date('Y-m-d h:i');
                            $historylog->action_status = $purchaseRequest->status;
                            $historylog->action_user_id = Yii::$app->user->identity->id;
                            $historylog->remarks = 'BAC- Abstract';

                            $historylog->save();
                            $purchaseRequest->save();

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            return $this->redirect(['bidding/bac-biddingabstractpb-pdf', 'id' => $_GET['id']]);
                        }
                    } catch (\Exception $e) {
                        $transaction->rollBack();
                    }
                }
            }

            return $this->renderAjax('_form', [
                'model' => $model,
                'modelmember_signatories' => (empty($modelmember_signatories)) ? [new MemberSignatories()] : $modelmember_signatories,
                'member_signatories' => $member_signatories,
                'usersWithRole' => $usersWithRole
            ]);
        } else {
            $model = BacSignatories::find()->where(['bid_id' => $_GET['id']])->one();
            $modelmember_signatories = $model->bac;
            $member_signatories = MemberSignatories::find()->where(['bac_signatories_id' => $model->id])->all();
            $items = PrItems::find()->where(['id' => $_GET['id']])->one();
            $purchaseRequest = PurchaseRequest::find()->where(['id' => $items['pr_id']])->one();
            $historylog = new HistoryLog();
            $purchaseRequest->status = 7;
            $authManager = Yii::$app->authManager;
            $usersWithRole = $authManager->getUserIdsByRole('BAC Members');

            if ($model->load(Yii::$app->request->post())) {
                $oldIDs = ArrayHelper::map($modelmember_signatories, 'id', 'id');
                $member_signatories = Model::createMultiple(MemberSignatories::className(), $member_signatories);
                Model::loadMultiple($member_signatories, Yii::$app->request->post());

                $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($member_signatories, 'id', 'id')));

                $valid = $model->validate();
                $valid = Model::validateMultiple($member_signatories) && $valid;

                $model->save();
                $purchaseRequest->save();
                if ($valid) {
                    $transaction = \Yii::$app->db->beginTransaction();
                    $valid = []; {
                        try {
                            if ($valid[] = $model->save()) {
                                if (!empty($deletedIDs)) {
                                    MemberSignatories::deleteAll(['id' => $deletedIDs]);
                                }
                                foreach ($member_signatories as $member) {
                                    // var_dump($member);die;
                                    $member->bac_signatories_id = $model->id;
                                    $valid[] = $member->save();
                                }
                            }

                            Yii::$app->getSession()->setFlash('success', 'Success');
                            return $this->redirect(['bidding/bac-biddingabstractpb-pdf', 'id' => $_GET['id']]);
                        } catch (\Exception $e) {
                            $transaction->rollBack();
                        }
                    }
                }
            }
            return $this->renderAjax('_form', [
                'model' => $model,
                'modelmember_signatories' => (empty($modelmember_signatories)) ? [new MemberSignatories()] : $modelmember_signatories,
                'member_signatories' => $member_signatories,
                'usersWithRole' => $usersWithRole
            ]);
        }
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BacSignatories model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BacSignatories model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BacSignatories the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BacSignatories::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
