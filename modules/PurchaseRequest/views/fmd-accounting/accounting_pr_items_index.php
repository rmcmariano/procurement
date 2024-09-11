<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\TrackStatus;
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
                'url' => ['purchase-request/accounting-request-index'],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'LIST OF ITEMS',
                'url' => ['purchase-request/accounting-request-items'],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
        ],
    ]) ?>
</div>

<br>
<div class="purchase-request-index">
    <p>
        <?= GridView::widget([
            'id' => 'grid-id',
            'dataProvider' => $dataProvider,
            'showFooter' => true,
            'options' => ['style' => 'width:100%'],
            'showPageSummary' => true,
            'striped' => true,
            'hover' => true,
            'export' => false,
            'pjax' => true,
            'panel' => ['type' => 'info',],
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
                    'options' => ['style' => 'width:2%'],
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
                    'attribute' => 'pr_id',
                    'header' => 'PR #',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'center',
                    'value' => 'purchase.pr_no',
                ],
                [
                    'attribute' => 'unit',
                    'header' => 'UNIT',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:5%'],
                    'hAlign' => 'center',
                ],
                [
                    'attribute' => 'item_name',
                    'header' => 'ITEM DESCRIPTION',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:35%'],
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::ACCOUNTING_STATUS])->one();
                        if (in_array($model->status, ['2'])) {
                            return $model['item_name'];
                        }
                        if ($bidding->item_remarks == $model->id) {
                            return $model['item_name'];
                        }
                        return $bidding->item_remarks;
                    },
                ],
                [
                    'attribute' => 'unit_cost',
                    'header' => 'UNIT COST',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::ACCOUNTING_STATUS])->one();
                        if (in_array($model->status, ['2'])) {
                            return $model['unit_cost'];
                        }
                        return $bidding->supplier_price;
                    },
                    'format' => [
                        'decimal', 2
                    ],
                    'hAlign' => 'right',
                    'options' => ['style' => 'width:10%'],
                ],
                [
                    'attribute' => 'quantity',
                    'header' => 'QUANTITY',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:5%'],
                    'hAlign' => 'center',
                ],
                [
                    'class' => DataColumn::class,
                    'attribute' => 'supplier_totalprice',
                    'header' => 'TOTAL PRICE',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::ACCOUNTING_STATUS])->one();

                        if (in_array($model->status, ['2'])) {
                            return $model['quantity'] * $model['unit_cost'];
                        }
                        return $model['quantity'] * $bidding['supplier_price'];
                    },
                    'format' => [
                        'decimal', 2
                    ],
                    'hAlign' => 'right',
                    'options' => ['style' => 'width:10%'],
                    'pageSummary' => true,
                ],
                [
                    'attribute' => 'status',
                    'header' => 'STATUS',
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
                    'header' => 'Actions',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => '{view} {status}',
                    'buttons' => [
                        'status' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id], ['title' => 'History Logs']);
                        },
                        'view' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/accounting-prview', 'id' => $model->pr_id], ['title' => 'View']);
                        },
                    ],
                ]
            ],
        ]);
        ?>
    </p>
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
        left: 145px;
        border-top: 35px solid transparent;
        bottom: 0;
    }
</style>