<?php

use kartik\grid\GridView;
use kartik\grid\DataColumn;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\TrackStatus;

?>
<div class="pr-subdata-index">
    <div id="ajaxCrudDatatable">

        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'showFooter' => true,
            'options' => [
                'style' => 'overflow: auto; word-wrap: break-word;'
            ],
            'rowOptions' => function ($url) {
                if (in_array($url->status, ['18'])) {
                    return ['class' => 'danger'];
                }
            },
            'showPageSummary' => true,
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:2%'],
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
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:40%; white-space: pre-line'],
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::ACCOUNTING_STATUS])->one();
                        if($bidding == NULL) {
                            return '';
                        }
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
                    'format' => [
                        'decimal', 2
                    ],
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::ACCOUNTING_STATUS])->one();
                        if($bidding == NULL) {
                            return '';
                        }
                        if (in_array($model->status, ['2'])) {
                            return $model['unit_cost'];
                        }
                        return $bidding->supplier_price;
                    },
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'right',
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
                    'attribute' => 'total_cost',
                    'header' => 'TOTAL COST',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:10%'],
                    'pageSummary' => true,
                    'hAlign' => 'right',
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::ACCOUNTING_STATUS])->one();
                        if($bidding == NULL) {
                            return '';
                        }
                        if (in_array($model->status, ['2'])) {
                            return $model['quantity'] * $model['unit_cost'];
                        }
                        return $model['quantity'] * $bidding['supplier_price'];
                    },
                    'format' => [
                        'decimal', 2
                    ],
                ],
                [
                    'attribute' => 'status',
                    'header' => 'STATUS',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'value' => 'statusdisplay.status',
                    'options' => ['style' => 'width:25%'],
                    'contentOptions' => ['style' => 'text-align:center']
                ],
            ]
        ]);
        ?>
    </div>
</div>