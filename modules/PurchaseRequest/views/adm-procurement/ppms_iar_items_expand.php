<?php

use app\modules\PurchaseRequest\models\PrItems;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

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
                        // var_dump($model);die;
                        // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                        $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        return $item->unit;
                    }
                ],
                // [
                //     'attribute' => 'bid_id',
                //     'header' => 'ITEM / EQUIPMENT NAME',
                //     'format' => 'ntext',
                //     'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                //     'options' => ['style' => 'width:30%; white-space: pre-line'],
                //     'value' => function ($model) {
                //         // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                //         $item = PrItems::find()->where(['id' => $model->item_id])->one();
                //         return $item->item_name;
                //     }
                // ],
                [
                    'attribute' => 'bid_id',
                    'header' => 'ITEM / EQUIPMENT NAME',
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

                        //  $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
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
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'options' => ['style' => 'width:20%'],
                    'header' => 'ACTIONS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => ' {poview}',
                    'urlCreator' => function ($action, $model) {
                        if ($action == 'poview') {
                            return ['inspection-acceptance-report/iar-items-view', 'id' => $model->id,];
                        }
                    },
                    'buttons' => [
                        'poview' => function ($url, $model, $key) {

                            echo '<div class="modal fade" id="modalItemView-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalItemView-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                    </div>
                                </div>
                            </div>
                        </div>';

                            $script = <<< JS
                                $(document).on('click', '[data-toggle="modal"][data-target="#modalItemView-$model->id"]', function() {
                                    var modal = $('#mmodalItemView-$model->id');
                                    var url = $(this).data('url');

                                    modal.find('.modal-body').load(url);
                                });
                            JS;

                            // var_dump($model);die;
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', $url, [
                                'class' => 'btn btn-success btn-xs',
                                // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                'title' => 'View',
                                'data-toggle' => 'modal',
                                'data-target' => '#modalItemView-' . $model->id,
                                'data-url' => Url::to(['inspection-acceptance-report/iar-items-view', 'id' => $model->id,])

                            ]);
                        },
                    ],
                ],
            ]
        ]);
        ?>
    </div>
</div>


<?php
$this->registerJsVar('Cancel', Url::to(['purchase-order/purchaseorder-cancel']));
$this->registerJs(
    <<<JS

$('.cancelPo').on('click', function() {
            var remarks = "";
            swal({
                title: "Cancel Purchase Order?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Input remarks:',
                content: "input",
                closeOnClickOutside: false,
                closeOnEsc: false,
            })
            .then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "/itdi-purchase-request/web/PurchaseRequest/purchase-order/ppms-purchaseorder-cancel",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                cancelid: $(this).val()
                            }
                        }); 
                        console.log(willDisapprove);
                        location.reload();
                    });       
                }
                else{
                    swal("Canceled", {
                        icon: "warning",
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });
                }
            });
        });

JS
);
?>

<style>
    .modal-content {
        border-radius: 20px;
    }
</style>