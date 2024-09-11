<?php

use app\modules\PurchaseRequest\models\BiddingListSearch;
use app\modules\PurchaseRequest\models\InspectionAcceptanceReport;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


Modal::begin([
    'header' => 'Create IAR',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-iar',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalIar'></div>";
Modal::end();


?>

<div class="bidding-list-index">
    <p>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>List of IAR:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>

                <!-- <= GridView::widget([
                    'id' => 'grid-iar',
                    'dataProvider' => $dataProvider,
                    'responsive' => false,
                    'tableOptions' => ['style' => 'overflow-y: visible !important;'],
                    'panel' => ['type' => 'info'],
                    'export' => false,
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
                            'options' => ['style' => 'width:2%'],
                            'detail' => function ($model, $key, $index, $column) {
                                // var_dump($model);die;
                                $searchModel = new ItemSpecificationSearch();
                                $searchModel->item_id = $model->item_id;
                                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_item_specs_expand', [
                                    'dataProvider' => $dataProvider,
                                    'searchModel' => $searchModel,
                                    'model' => $model
                                ]);
                            },
                        ],
                        [
                            'attribute' => 'unit',
                            'header' => 'UNIT',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:5%'],
                            'hAlign' => 'center',
                            'value' => function ($model) {
                                // $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
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
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{create_iar} ',
                            'options' => ['style' => 'width:10%'],
                            'buttons' => [
                                'create_iar' => function ($url, $model) {
                                    return Html::button('<span class="glyphicon glyphicon-plus-sign"></span>  Create IAR', ['value' => Url::to(['inspection-acceptance-report/ppms-iar-create', 'id' => $model->id,]),  'class' => 'btn btn-info btn-sm modalIarBtn']);
                                },
                            ],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => '',
                            'template' => '{iar}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'Generate PDF'
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'iar' => function ($url, $model) {
                                    return '<li>' . Html::a('IAR', ['purchase-order/ppms-purchaseorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }
                            ],
                        ],
                    ],
                ]); ?> -->

                <?= GridView::widget([
                    'id' => 'grid-iar-id',
                    'dataProvider' => $dataProvider2,
                    'responsive' => false,
                    'tableOptions' => ['style' => 'overflow-y: visible !important;'],
                    'panel' => ['type' => 'info'],
                    'export' => false,
                    'rowOptions' => function ($url) {
                        if (in_array($url->po_status, ['3'])) {
                            return ['class' => 'danger'];
                        }
                    },
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
                            'options' => ['style' => 'width:2%'],
                            'detail' => function ($model, $key, $index, $column) {

                                $searchModel = new BiddingListSearch();
                                $searchModel->po_id = $model->id;
                                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_po_items_expand', [
                                    'dataProvider' => $dataProvider,
                                    'searchModel' => $searchModel,
                                    'model' => $model
                                ]);
                            },
                        ],
                        [
                            'attribute' =>  'po_no',
                            'header' => 'P.O./W.O #',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                        ],
                    
                        [
                            'attribute' =>  'iar_number',
                            'header' => 'IAR #',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL){
                                    return '-';
                                }
                                return $iar->iar_number;
                            },
                        ],
                        [
                            'attribute' =>  'iar_number',
                            'header' => 'S.I. #',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                        ],
                        [
                            'attribute' =>  'iar_number',
                            'header' => 'S.I. DATE',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                        ],
                        [
                            'attribute' =>  'iar_date_created',
                            'header' => 'IAR CREATED',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:8%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                // var_dump($model);die;
                                return Yii::$app->formatter->asDatetime(strtotime($model->iar_date_created), 'php:M d, Y');
                            },
                        ],
                        [
                            'attribute' =>  'inspector_id',
                            'header' => 'INSPECTOR',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                        ],
                        [
                            'attribute' =>  'iar_date_created',
                            'header' => 'INSPECTION DATE',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:8%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime(strtotime($model->iar_date_created), 'php:M d, Y');
                            },
                        ],
                        [
                            'attribute' =>  'delivery_status',
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{view} {create} ',
                            'options' => ['style' => 'width:10%'],
                            'urlCreator' => function ($action, $model) {
                                
                                if ($action == 'create') {
                                    return ['purchase-order/purchaseorder-view', 'id' => $model->id,];
                                }
                                if ($action == 'view') {
                                    return ['inspection-acceptance-report/iar-view', 'id' => $model->id,];
                                }
                            },
                            'buttons' => [
                                'create' => function ($url, $model, $key) {
                                    echo '<div class="modal fade" id="modalPoView-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalPoView-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalPoView-$model->id"]', function() {
                                            var modal = $('#modalPoView-$model->id');
                                            var url = $(this).data('url');
        
                                            modal.find('.modal-body').load(url);
                                        });
                                    JS;

                                    // var_dump($model);die;
                                    return Html::a('<span class="glyphicon glyphicon-plus-sign"></span> ', $url, [
                                        'class' => 'btn btn-info btn-xs',
                                        // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                        'title' => 'Create IAR',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalPoView-' . $model->id,
                                        'data-url' => Url::to(['purchase-order/purchaseorder-view', 'id' => $model->id,])

                                    ]);
                                },
                                'view' => function ($url, $model, $key) {

                                    echo '<div class="modal fade" id="modalPoView-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalPoView-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalPoView-$model->id"]', function() {
                                            var modal = $('#modalPoView-$model->id');
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
                                        'data-target' => '#modalPoView-' . $model->id,
                                        'data-url' => Url::to(['inspection-acceptance-report/iar-view', 'id' => $model->id,])

                                    ]);
                                },
                            ]
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => '',
                            'template' => '{iar}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'Generate PDF'
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'iar' => function ($url, $model) {
                                    return '<li>' . Html::a('IAR', ['purchase-order/ppms-purchaseorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }
                            ],
                        ],
                    ],
                ]); ?>
            </p>
        </div>
    </div>
</div>


<?php
$this->registerJs(
    <<<JS

$('.modalIarBtn').on("click", function(){
        $('#modal-iar').modal("show");
        $.get($(this).val(), function(data){
            $('#modalIar').html(data);
        });
    });

JS
);
?>


<style>
    .print-button {
        border: none;
        outline: none;
        background-color: none;
        background: none;
        color: #808080;
        margin-left: 12px;
        text-align: left;
    }

    #print-bulletin {
        border: none;
        outline: none;
        background-color: none;
        background: none;
        color: #808080;
        margin-left: 12px;
    }

    .dropdown-menu>li {
        border: none;
        outline: none;
        background-color: none;
        background: none;
        margin-left: 12px;
        color: #000;
        color: #808080;
        margin-left: 12px;
    }

    .dropdown-menu>li:hover,
    .dropdown-menu>li:focus,
    .dropdown-menu>li:active,
    .dropdown-menu>li.active,
    .open>.dropdown-toggle.dropdown-menu>li {
        color: #fff;
        background-color: #e6e6e6;
        border-color: none;
        outline: none;
        /*set the color you want here*/
    }

    .modal-lg {
        max-width: 100% !important;
    }
</style>