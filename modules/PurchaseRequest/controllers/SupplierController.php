<?php

namespace app\modules\PurchaseRequest\controllers;

use Yii;
use app\modules\PurchaseRequest\models\Supplier;
use app\modules\PurchaseRequest\models\SupplierContacts;
use app\modules\PurchaseRequest\models\Model;
use app\modules\PurchaseRequest\models\SupplierSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;


class SupplierController extends Controller
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
        $searchModel = new SupplierSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionView($id)
    {
        $model = $this->findModel($id);

        $suppliersContact = SupplierContacts::find()
            ->where(['supplier_id' => $model['id']])
            ->all();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $suppliersContact,
        ]);

        return $this->render('view', [
            'model' => $model,
            'dataProvider' => $dataProvider
        ]);
    }

    public function actionCreate()
    {
        $model = new Supplier();
        $modelsuppliercontacts = [new SupplierContacts()];
    
        if ($model->load(Yii::$app->request->post())) {
            // Validate the Supplier model
            $valid = $model->validate();
    
            // Load and validate the SupplierContacts models
            $modelsuppliercontacts = Model::createMultiple(SupplierContacts::classname());
            Model::loadMultiple($modelsuppliercontacts, Yii::$app->request->post());
            $valid = Model::validateMultiple($modelsuppliercontacts) && $valid;
    
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save()) {
                        foreach ($modelsuppliercontacts as $modelothercontacts) {
                            $modelothercontacts->supplier_id = $model->id;
                            if (!($flag = $modelothercontacts->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        Yii::$app->getSession()->setFlash('success', 'Success');
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }
    
        return $this->render('_form', [
            'model' => $model,
            'modelsuppliercontacts' => $modelsuppliercontacts,
            'modelsuppliercontacts' => empty($modelsuppliercontacts) ? [new SupplierContacts()] : $modelsuppliercontacts,
        ]);
    }
    

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelsuppliercontacts = $model->suppliersContact;

        if ($model->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsuppliercontacts, 'id', 'id');
            $modelsuppliercontacts = Model::createMultiple(SupplierContacts::classname(), $modelsuppliercontacts);
            Model::loadMultiple($modelsuppliercontacts, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsuppliercontacts, 'id', 'id')));

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($modelsuppliercontacts) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {
                        if (!empty($deletedIDs)) {
                            Address::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsuppliercontacts as $modelothercontacts) {
                            $modelothercontacts->supplier_id = $model->id;
                            if (!($flag = $modelothercontacts->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }


        return $this->render('_form', [
            'model' => $model,
            // 'modelsuppliercontacts' => $modelsuppliercontacts,
            'modelsuppliercontacts' => empty($modelsuppliercontacts) ? [new SupplierContacts()] : $modelsuppliercontacts,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Supplier::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
