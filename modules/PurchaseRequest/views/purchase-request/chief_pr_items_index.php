<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\bootstrap\Nav;
use yii\helpers\Url;

?>

<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [

            [
                'label' => 'LIST OF PR',
                'url' => ['purchase-request/chief-request-index'],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'LIST OF ITEMS',
                'url' => ['purchase-request/chief-request-items'],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
        ],
    ]) ?>
</div>

<br>
<div class="chief-request-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>PENDING REQUEST ITEM LISTS</h3>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <?= GridView::widget([
                'id' => 'grid-id',
                'dataProvider' => $dataProvider,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'striped' => true,
                'hover' => true,
                'export' => false,
                // 'pjax' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'showFooter' => true,
                'showPageSummary' => true,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width:2%;'],
                    ],
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'value' => function ($model, $key, $index, $column) {

                            return GridView::ROW_COLLAPSED;
                        },
                        'options' => ['style' => 'width:2%;'],
                        'detail' => function ($model, $key, $index, $column) {

                            $searchModel = new ItemSpecificationSearch();
                            $searchModel->item_id = $model->id;
                            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                            return Yii::$app->controller->renderPartial('pr_itemspecs_expand_view', [
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
                        'options' => ['style' => 'width:15%;'],
                        'value' => function ($model) {
                            return $model->purchase ? $model->purchase->temp_no : '';
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
                        'header' => 'ITEM DESCRIPTION',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:35%'],
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
                        'header' => 'ITEM STATUS',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                            if (isset($model->statusdisplay)) {
                                return $model->statusdisplay->status;
                            }
                            return 'No Bidder';
                        },
                        'options' => ['style' => 'width:15%'],
                        'contentOptions' => ['style' => 'text-align: center']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:10%'],
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{view} {status}',
                        'buttons' => [
                            'status' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id], ['title' => 'History Logs']);
                            },
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/chief-prview', 'id' => $model->pr_id], ['title' => 'View']);
                            },
                        ],
                    ]
                ],
            ]);
            ?>
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
        width: 200px;
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
        left: 200px;
        border-top: 35px solid transparent;
        bottom: 0;
    }
</style>