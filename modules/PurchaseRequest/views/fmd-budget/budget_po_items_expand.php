<?php

use app\modules\PurchaseRequest\models\PrItems;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\PurchaseRequest\models\PrSubdataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Request';
// $this->params['breadcrumbs'][] = $this->title;


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
                    'value' => function ($model) {
                        // $bidding = BiddingList::find()->where(['id' => $model->id])->one();
                        $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        return $item->unit;
                    }
                ],
                [
                    'attribute' => 'bid_id',
                    'header' => 'ITEM / EQUIPMENT NAME',
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:30%; white-space: pre-line'],
                    'value' => function ($model) {
                        // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                        $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        return $item->item_name;
                    }
                ],
                [
                    'attribute' => 'bid_id',
                    'header' => 'BID OFFER',
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:40%; white-space: pre-line'],
                    'value' => function ($model) {
                        // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                        $items = PrItems::find()->where(['id' => $model->item_id])->one();

                        if ($model->item_remarks == $items->id) {
                            return $items->item_name;
                        }
                        return $model->item_remarks;
                    }
                ],
                [
                    'attribute' => 'quantity',
                    'options' => ['style' => 'width:5%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: center'],
                    'header' => 'QTY',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'value' => function ($model) {
                        // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                        $test = PrItems::find()->where(['id' => $model->item_id])->one();
                        return $test->quantity;
                    }
                ],
                [
                    'attribute' => 'bid_id',
                    'header' => 'BID PRICE',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:10%'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'value' => function ($model) {
                        // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                        return $model->supplier_price;
                    },
                    'format' => [
                        'decimal', 2
                    ],
                ],
                [
                    'attribute' => 'bid_id',
                    'header' => 'TOTAL PRICE',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:20%'],
                    'contentOptions' => ['style' => 'text-align: right'],
                    'value' => function ($model) {
                        // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                        $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        $total =  $item['quantity'] * $model['supplier_price'];
                        return $total;
                    },
                    'format' => [
                        'decimal', 2
                    ],
                    'pageSummary' => true,
                    'pageSummaryOptions' => ['style' => 'text-align: right'],
                ],

            ]
        ]);
        ?>
    </div>
</div>