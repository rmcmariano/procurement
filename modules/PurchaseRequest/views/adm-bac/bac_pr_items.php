<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;

?>

<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [

            [
                'label' => 'LIST OF PR',
                'url' => ['purchase-request/bac-request-index'],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'LIST OF ITEMS',
                'url' => ['purchase-request/bac-request-items'],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
        ],
    ]) ?>
</div>

<br>
<div class="bac-request-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>ON-PROCESS REQUEST ITEM LISTS</h3>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>
            <p>
                <center>
                    <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Generate Excel PR Items Report', ['generate-report/bac-pr-items-report', 'id' => $model->id,], ['class' => 'btn btn-warning']) . ' '; ?>
                </center>
            </p>

                <?= GridView::widget([
                    'id' => 'grid-id',
                    'dataProvider' => $dataProvider,
                    'options' => [
                        'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                    ],
                    'rowOptions' => function ($url) {
                        if (in_array($url->status, ['18'])) {
                            return ['class' => 'danger'];
                        }
                    },
                    'showFooter' => true,
                    'showPageSummary' => true,
                    'striped' => true,
                    'hover' => true,
                    'pjax' => true,
                    'panel' => ['type' => 'default',],
                    'export' => false,
                    'floatHeader' => true,
                    'floatHeaderOptions' => ['scrollingTop' => '5'],
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'class' => 'kartik\grid\SerialColumn',
                            'options' => ['style' => 'width:2%'],
                        ],
                        [
                            'class' => 'kartik\grid\ExpandRowColumn',
                            'value' => function ($model, $key, $index, $column) {
                                return GridView::ROW_COLLAPSED;
                            },
                            'detail' => function ($model, $key, $index, $column) {

                                $searchModel = new ItemSpecificationSearch();
                                $searchModel->item_id = $model->id;
                                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                                return Yii::$app->controller->renderPartial('/purchase-request/pr_itemspecs_expand_view', [
                                    'dataProvider' => $dataProvider,
                                    'searchModel' => $searchModel,
                                    'model' => $model
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'purchase',
                            'header' => 'PR #',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'value' => function ($model) {
                                return $model->purchase ? $model->purchase->pr_no : '';
                            },
                        ],
                        [
                            'attribute' => 'unit',
                            'header' => 'UNIT',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:5%'],
                            'hAlign' => 'center',
                            'filter' => false
                        ],
                        [
                            'attribute' => 'item_name',
                            'format' => 'ntext',
                            'header' => 'ITEM / EQUIPMENT NAME',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:40%'],
                            'filter' => false
                        ],
                        [
                            'attribute' => 'unit_cost',
                            'header' => 'UNIT COST',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'format' => [
                                'decimal', 2
                            ],
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'right',
                            'filter' => false
                        ],
                        [
                            'attribute' => 'quantity',
                            'header' => 'QUANTITY',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:5%'],
                            'hAlign' => 'center',
                            'filter' => false
                        ],
                        [
                            'class' => DataColumn::class,
                            'attribute' => 'total_cost',
                            'header' => 'TOTAL COST',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'format' => [
                                'decimal', 2
                            ],
                            'options' => ['style' => 'width:10%'],
                            'pageSummary' => true,
                            'hAlign' => 'right',
                            'filter' => false
                        ],
                        [
                            'attribute' => 'status',
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'label' => 'Item Status',
                            'value' => function ($model) {
                                if (isset($model->statusdisplay)) {
                                    return $model->statusdisplay->status;
                                }
                                return 'No Bidder';
                            },
                            'options' => ['style' => 'width:20%'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'filter' => false
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'options' => ['style' => 'width:20%'],
                            'header' => 'Actions',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{view} {status} ',
                            'buttons' => [
                                'status' => function ($url, $model, $key) {
                                    return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id], ['title' => 'History Logs']);
                                },
                                'view' => function ($url, $model, $key) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/bac-prview', 'id' => $model->pr_id],  ['title' => 'View']);
                                },
                            ],
                            'visibleButtons' => [
                                'status' => function ($model) {

                                    if ($model->status == 0) {
                                        return false;
                                    }
                                    return true;
                                },
                            ],
                        ]
                    ]
                ]);
                ?>
            </p>
        </div>
    </div>
</div>


<style>
    .nav-tabs li a {
        background-color: #5F9EA0;
        color: #000000;
        font-weight: bold;
        border-top-right-radius: 16px 16px;

    }

    .nav-tabs:after {
        content: "";
        clear: both;
        display: block;
        background: #000000;
    }


    .nav-tabs li.active {
        height: 40px;
        line-height: 40px;
        width: 150px;
        background: #5F9EA0;
        border-top-left-radius: 16px 16px;
        border-top-right-radius: 16px 16px;
        color: #5F9EA0;
        margin-right: 5px;
        font-weight: bold;

    }

    .nav-tabs li.active:after {
        content: "";
        display: block;
        position: absolute;
        border-left: 35px #5F9EA0;
        left: 145px;
        border-top: 35px solid transparent;
        bottom: 0;
    }
</style>