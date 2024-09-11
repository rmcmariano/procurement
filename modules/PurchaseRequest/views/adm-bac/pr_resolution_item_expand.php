<?php


use app\modules\PurchaseRequest\models\PrItems;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>

<div class="pr-subdata-index">
    <?= GridView::widget([
        'id' => 'itemspecs-create-grid',
        'dataProvider' => $dataProvider,
        'showFooter' => true,
        'options' => [
            'style' => 'overflow: auto; word-wrap: break-word; width: 80%'
        ],

        'showPageSummary' => true,
        'columns' => [
            [
                'class' => 'kartik\grid\SerialColumn',
                'options' => ['style' => 'width:2%'],
            ],
            [
                'attribute' => 'resolution_no',
                'header' => 'ITEMS',
                'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                'options' => ['style' => 'width:30%'],
                'hAlign' => 'left',
                'value' => function ($model) {
                    $item = PrItems::find()->where(['id' => $model->item_id])->one();
                    
                    return $item->item_name;
                }
            ],
            [
                'attribute' => 'resolution_no',
                'value' => 'supplierdisplay.supplier_name',
                'options' => ['style' => 'width:30%'],
                'hAlign' => 'center',
                'contentOptions' => ['style' => 'text-align: center'],
                'header' => 'BIDDERS',
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
                'attribute' => 'resolution_no',
                'options' => ['style' => 'width:10%'],
                'hAlign' => 'center',
                'contentOptions' => ['style' => 'text-align: center'],
                'header' => 'QTY',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'value' => function ($model) {

                    $item = PrItems::find()->where(['id' => $model->item_id])->one();
                    return $item->quantity;
                },
            ],
            [
                'attribute' => 'resolution_no',
                'format' => [
                    'decimal', 2
                ],
                'options' => ['style' => 'width:10%'],
                'hAlign' => 'center',
                'contentOptions' => ['style' => 'text-align: right'],
                'header' => 'BID PRICE',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'value' => function ($model) {

                    return $model->supplier_price;
                },
            ],

        ]
    ]);
    ?>
</div>