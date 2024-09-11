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

$this->title = 'Release Document';

?>
<div class="pr-index">
    <?php if (Yii::$app->controller->action->id == 'all-release') { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {

                if ($model->status == 1) {
                    return ['style' => 'background-color: #dff0d8'];
                } else{
                    return ['style' => 'background-color: #d9edf7'];
                }
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
                    'label' => 'PR Type',
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
                    'label' => 'Date of Released',
                    'attribute' => 'date_time_released',
                    'filter' => false,
                    'value' => function ($model) {
                        if ($model && $model->date_time_released) {
                            $dateString = $model->date_time_released;
                            list($date, $time) = explode(' ', $dateString);

                            $date_time_released = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
                            return $date_time_released;
                        }
                        return 'No Action';
                    }
                ],
                [
                    'label' => 'Released By',
                    'attribute' => 'released_by',
                    'filter' => false,
                    'value' => function ($model) {
                        if ($model->released_by != NULL) {

                            $profile = Profile::find()->where(['user_id' => $model->released_by])->one();
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
                            return ['release-view', 'id' => $model->id];
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
    <?php } else if (Yii::$app->controller->action->id == 'for-release') { ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function ($model) {

                return ['style' => 'background-color: #dff0d8'];
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
                    'label' => 'PR Type',
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
                            return ['release-view', 'id' => $model->id];
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
    <?php } else if (Yii::$app->controller->action->id == 'released') { ?>
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
                    'label' => 'PR Type',
                    'attribute' => 'type_id',
                    'filter' => false,
                    'value' => function ($model) {
                        $typeOfAction = TypeOfAction::find()->where(['id' => $model->type_id])->one();

                        // var_dump($pr_tracking['type']);die;
                        return $typeOfAction->name;
                    }
                ],
                [
                    'label' => 'Date of Released',
                    'attribute' => 'date_time_released',
                    'filter' => false,
                    'value' => function ($model) {
                        if ($model && $model->date_time_released) {
                            $dateString = $model->date_time_released;
                            list($date, $time) = explode(' ', $dateString);

                            $date_time_released = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
                            return $date_time_released;
                        }
                        return 'No Action';
                    }
                ],
                [
                    'label' => 'Released By',
                    'attribute' => 'released_by',
                    'filter' => false,
                    'value' => function ($model) {
                        $profile = Profile::find()->where(['user_id' => $model->released_by])->one();
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
                            return ['release-view', 'id' => $model->id];
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