<?php

use app\modules\document\models\Document;
use app\modules\document\models\TypeOfAction;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

?>
<div class="pr-index">
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
                'label' => 'Latest Action Type',
                'value' => function ($model) {
                    $pr_tracking = Document::find()->where(['pr_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();
                    $typeOfAction = $pr_tracking ? TypeOfAction::find()->where(['id' => $pr_tracking->type])->one() : null;

                    // var_dump($pr_tracking['type']);die;
                    return $pr_tracking ? $typeOfAction->name : 'No Action';
                }
            ],
            [
                'label' => 'Date of Action',
                'value' => function ($model) {
                    $pr_tracking = Document::find()->where(['pr_id' => $model->id])->orderBy(['id' => SORT_DESC])->one();

                    if ($pr_tracking && $pr_tracking->date_time) {
                        $dateString = $pr_tracking->date_time;
                        list($date, $time) = explode(' ', $dateString);

                        $date_time = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
                        return $date_time;
                    }

                    return 'No Action';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Action',
                'contentOptions' => ['class' => 'action-column', 'style' => 'text-align:center;'],
                'template' => '{view} {update} {delete}',
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
                'buttons' => [
                    'receive-view' => function ($url, $model) {
                        if ($model->customer_type != 1) {
                            return Html::a('<span class="fas fa-info-circle"></span>', $url, [
                                'title' => Yii::t('app', 'View'),
                                'class' => 'btn btn-primary btn-sm',
                            ]);
                        } else {
                            return '';
                        }
                    },
                ]
            ],
        ],
    ]) ?>
</div>