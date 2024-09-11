<?php

use app\modules\document\models\Document;
use app\modules\document\models\TypeOfAction;
use app\modules\document\models\PurchaseRequest;
use app\models\profile\Profile;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
$this->title = 'Receive Document';

?>
<div class="pr-index">
    <?php if (Yii::$app->controller->action->id == 'all-receive') { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {

                if (!(Document::find()->where(['pr_id' => $model->id])->one())) {
                    return ['style' => 'background-color: #dff0d8'];
                } else {

                    return ['style' => 'background-color: #d9edf7'];
                }
            },
            'columns' => [
                [
                    'label' => 'Purchase Request Number',
                    'attribute' => 'pr_no',
                ],
                [
                    'label' => 'Created By',
                    'attribute' => 'created_by',
                    'value' => function ($model) {
                        $profile = Profile::find()->where(['user_id' => $model->created_by])->one();
                        $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';

                        return $profile ? $created_by : "";
                    }
                ],
                [
                    'label' => 'Date Created',
                    'attribute' => 'date_of_pr',
                    'value' => function ($model) {
                        $date_of_pr = date('F j, Y', strtotime($model->date_of_pr));
                        return $date_of_pr;
                    }
                ],
                [
                    'label' => 'Latest Received Type',
                    'value' => function ($model) {
                        $pr_tracking = Document::find()->where(['pr_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();
                        $typeOfAction = $pr_tracking ? TypeOfAction::find()->where(['id' => $pr_tracking->type_id])->one() : null;

                        // var_dump($pr_tracking['type']);die;
                        return $pr_tracking ? $typeOfAction->name : 'No Action';
                    }
                ],
                [
                    'label' => 'Date of Received',
                    'value' => function ($model) {
                        $pr_tracking = Document::find()->where(['pr_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();

                        if ($pr_tracking && $pr_tracking->date_time_received) {
                            $dateString = $pr_tracking->date_time_received;
                            list($date, $time) = explode(' ', $dateString);

                            $date_time_received = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
                            return $date_time_received;
                        }

                        return 'No Action';
                    }
                ],
                [
                    'label' => 'Received By',
                    'value' => function ($model) {
                        $pr_tracking = Document::find()->where(['pr_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();
                        $profile = $pr_tracking ? Profile::find()->where(['user_id' => $pr_tracking->received_by])->one() : null;
                        if ($profile != null) {

                            $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';
                            return $profile ? $created_by : "";
                        } else {
                            return 'No Action';
                        }
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'contentOptions' => ['class' => 'action-column'],
                    'template' => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'view') {
                            return ['receive-view', 'id' => $model->id];
                        }
                        if ($action === 'update') {
                            return ['update', 'id' => $model->id];
                        }
                        if ($action === 'delete') {
                            return ['toinks', 'id' => $model->id];
                        }
                        return null;
                    },
                ],
            ],
        ]) ?>
    <?php } else if (Yii::$app->controller->action->id == 'for-receive') { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {

                if (!(Document::find()->where(['pr_id' => $model->id])->one())) {
                    return ['style' => 'background-color: #dff0d8'];
                } else {

                    return ['style' => 'background-color: #d9edf7'];
                }
            },
            'columns' => [
                [
                    'label' => 'Purchase Request Number',
                    'attribute' => 'pr_no',
                ],
                [
                    'label' => 'Created By',
                    'attribute' => 'created_by',
                    'value' => function ($model) {
                        $profile = Profile::find()->where(['user_id' => $model->created_by])->one();
                        $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';

                        return $profile ? $created_by : "";
                    }
                ],
                [
                    'label' => 'Date Created',
                    'attribute' => 'date_of_pr',
                    'value' => function ($model) {
                        $date_of_pr = date('F j, Y', strtotime($model->date_of_pr));
                        return $date_of_pr;
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'contentOptions' => ['class' => 'action-column'],
                    'template' => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'view') {
                            return ['receive-view', 'id' => $model->id];
                        }
                        if ($action === 'update') {
                            return ['update', 'id' => $model->id];
                        }
                        if ($action === 'delete') {
                            return ['toinks', 'id' => $model->id];
                        }
                        return null;
                    },
                ],
            ],
        ]) ?>
    <?php } else if (Yii::$app->controller->action->id == 'received') { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {
                return ['style' => 'background-color: #d9edf7'];
            },
            'columns' => [
                [
                    'attribute' => 'pr_id',
                    'label' => 'Purchase Request Number',
                    'value' => 'purchaseRequestNumber.pr_no',
                    'filter' => Select2::widget([
                        'name' => 'pr_id',
                        'model' => $searchModel,
                        'attribute' => 'pr_id',
                        'data' => ArrayHelper::map(
                            Document::find()->groupBy('pr_id')
                                ->orderBy(['id' => SORT_DESC])
                                ->all(),
                            'pr_id',
                            'purchaseRequestNumber.pr_no'
                        ),
                        'options' => ['placeholder' => ''],
                        'pluginOptions' => [
                            'allowClear' => true,
                        ],
                    ]),
                ],
                [
                    'label' => 'Received Type',
                    'attribute' => 'type_id',
                    'filter' => false,
                    'value' => function ($model) {
                        $typeOfAction = TypeOfAction::find()->where(['id' => $model->type_id])->one();

                        // var_dump($pr_tracking['type']);die;
                        return $typeOfAction->name;
                    }
                ],
                [
                    'label' => 'Date of Received',
                    'attribute' => 'date_time_received',
                    'filter' => false,
                    'value' => function ($model) {
                        if ($model && $model->date_time_received) {
                            $dateString = $model->date_time_received;
                            list($date, $time) = explode(' ', $dateString);

                            $date_time_received = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
                            return $date_time_received;
                        }
                        return 'No Action';
                    }
                ],
                [
                    'label' => 'Received By',
                    'attribute' => 'received_by',
                    'filter' => false,
                    'value' => function ($model) {
                        $profile = Profile::find()->where(['user_id' => $model->received_by])->one();
                        $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';
                        return $profile ? $created_by : "";
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => 'Action',
                    'contentOptions' => ['class' => 'action-column'],
                    'template' => '{view}',
                    'urlCreator' => function ($action, $model, $key, $index) {
                        if ($action === 'view') {
                            return ['receive-view', 'id' => $model->pr_id];
                        }
                        if ($action === 'update') {
                            return ['update', 'id' => $model->id];
                        }
                        if ($action === 'delete') {
                            return ['toinks', 'id' => $model->id];
                        }
                        return null;
                    },
                ],
            ],
        ]) ?>
    <?php } ?>
</div>

<style>
    .pr-index {
        padding-bottom: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background-color: white;
        display: inline-block;
        width: 100%;
    }
</style>