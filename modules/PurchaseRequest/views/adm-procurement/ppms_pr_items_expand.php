<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\TrackStatus;
use kartik\grid\GridView;
use kartik\grid\DataColumn;

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
            'rowOptions' => function ($url) {
                if (in_array($url->status, ['18', '46'])) {
                    return ['class' => 'danger'];
                }
            },
            'showPageSummary' => true,
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:2%'],
                ],
                // [
                //     'attribute' => 'stock',
                //     'header' => 'STOCK',
                //     'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                //     'options' => ['style' => 'width:5%'],
                //     'hAlign' => 'center',
                // ],
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
                        return $model->item_name;
                        // $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::PPMS_STATUS])->one();
                        // if ($bidding->item_remarks == $model->id) {
                        //     return $model->item_name;
                        // }
                        // return $bidding->item_remarks;
                    }
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
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::PPMS_STATUS])->one();
                        return $bidding->supplier_price;
                    }
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
                    'format' => [
                        'decimal', 2
                    ],
                    'options' => ['style' => 'width:10%'],
                    'pageSummary' => true,
                    'hAlign' => 'right',
                    'value' => function ($model) {
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::PPMS_STATUS])->one();
                        $total = $model['quantity'] * $bidding['supplier_price'];
                        // var_dump($bidding);die;
                        return $total;
                    },
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