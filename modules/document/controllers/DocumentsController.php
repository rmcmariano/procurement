<?php

namespace app\modules\document\controllers;

use app\modules\document\models\DocumentSearch;
use app\modules\document\models\Document;
use app\modules\document\models\PurchaseRequestSearch;
use app\modules\document\models\PurchaseRequest;
use app\models\profile\Profile;
use app\modules\document\models\TypeOfAction;
use Yii;
use yii\web\Controller;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;

class DocumentsController extends Controller
{

    public function actionAllReceive()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // var_dump($searchModel);die;.

        return $this->render('receive-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionForReceive()
    {
        $searchModel = new PurchaseRequestSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        // var_dump($searchModel);die;.

        return $this->render('receive-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionReceived()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->allreceived(Yii::$app->request->queryParams);


        return $this->render('receive-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReceiveView($id)
    {
        $model = PurchaseRequest::find()->where(['id' => $id])->one();
        $profile = Profile::find()->where(['user_id' => $model->created_by])->one();
        $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';

        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->received(Yii::$app->request->queryParams);

        $pr_tracking = new Document();

        if ($pr_tracking->load(Yii::$app->request->post())) {
            $date_time_received = $pr_tracking->date_time_received . ' ' . $pr_tracking->time;
            $pr_tracking->pr_id = $id;
            $pr_tracking->date_time_received = $date_time_received;
            $pr_tracking->received_by = Yii::$app->user->identity->id;
            $pr_tracking->status = 1;

            $pr_tracking->save();
        }

        return $this->render('receive-view', [
            'model' => $model,
            'profile' => $profile,
            'created_by' => $created_by,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pr_tracking' => $pr_tracking,
        ]);
    }

    public function actionAllRelease()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->allrelease(Yii::$app->request->queryParams);
        // var_dump($searchModel);die;

        return $this->render('release-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionForRelease()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->forrelease(Yii::$app->request->queryParams);
        // var_dump($searchModel);die;

        return $this->render('release-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    public function actionReleased()
    {
        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->released(Yii::$app->request->queryParams);
        // var_dump($searchModel);die;

        return $this->render('release-index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionReleaseView($id)
    {
        $pr_tracking = Document::find()->where(['id' => $id])->one();
        $model = PurchaseRequest::find()->where(['id' => $pr_tracking->pr_id])->one();
        $profile = Profile::find()->where(['user_id' => $model->created_by])->one();
        $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';
        $pr_name = TypeOfAction::findOne($pr_tracking->type_id)->name;


        $searchModel = new DocumentSearch();
        $dataProvider = $searchModel->released(Yii::$app->request->queryParams);
        $dataProvider->query->andFilterWhere(['pr_id' => $model->id]);

        if ($pr_tracking->load(Yii::$app->request->post())) {
            $date_time_released = $pr_tracking->date_time_released . ' ' . $pr_tracking->time;
            $pr_tracking->date_time_released = $date_time_released;
            $pr_tracking->released_by = Yii::$app->user->identity->id;
            $pr_tracking->status = 2;

            $pr_tracking->save();
        }

        return $this->render('release-view', [
            'model' => $model,
            'profile' => $profile,
            'created_by' => $created_by,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pr_tracking' => $pr_tracking,
            'pr_name' => $pr_name,
        ]);
    }
}
