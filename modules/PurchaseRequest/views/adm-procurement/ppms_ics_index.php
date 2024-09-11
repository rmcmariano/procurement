<?php

use app\modules\PurchaseRequest\models\BiddingListSearch;
use app\modules\PurchaseRequest\models\InspectionAcceptanceReport;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\Supplier;
use app\modules\PurchaseRequest\models\PropertyAcknowledgement;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Url;


?>
<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [
            [
                'label' => 'PR DETAILS',
                'url' => ['purchase-request/procurement-prview', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'PURCHASE ORDER',
                'url' => ['bidding/ppms-biddingawardlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
                'visible' => (!in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19'])),
            ],
            [
                'label' => 'WORK ORDER',
                'url' => ['bidding/ppms-biddingawardlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
                'visible' => in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19']),
            ],
            [
                'label' => 'CONFORME',
                'url' => ['purchase-order/ppms-suppliersconforme-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'DELIVERY',
                'url' => ['purchase-order/ppms-delivery-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'INSPECTION',
                'url' => ['inspection-acceptance-report/ppms-iar-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'PAR/ICS',
                'url' => ['inspection-acceptance-report/ppms-ics-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
            [
                'label' => 'DISBURSEMENT VOUCHER',
                'url' => ['purchase-order/ppms-dv-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<br>
<div class="bidding-list-index">
    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>List of ICS/PAR:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>
                <?= GridView::widget([
                    'id' => 'grid-ics-id',
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

                                $searchModel = new ItemSpecificationSearch();
                                $searchModel->item_id = $model->item_id;
                                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_ics_item_specs_expand_view', [
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
                                // var_dump($model);die;
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
                            'header' => 'ICS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{icsview} {icscreate}',
                            'urlCreator' => function ($action, $model) {
                                if ($action == 'icsview') {
                                    return ['inspection-acceptance-report/ppms-ics-view', 'id' => $model->id,];
                                }
                                if ($action == 'icscreate') {
                                    return ['inspection-acceptance-report/ppms-ics-create', 'id' => $model->id,];
                                }
                            },
                            'buttons' => [
                                'icsview' => function ($url, $model, $key) {

                                    echo '<div class="modal fade" id="modalIcsItemView-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalIcsItemView-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalItemView-$model->id"]', function() {
                                            var modal = $('#modalIcsItemView-$model->id');
                                            var url = $(this).data('url');
        
                                            modal.find('.modal-body').load(url);
                                        });
                                    JS;

                                    // var_dump($model);die;
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span> ICS VIEW ', $url, [
                                        'class' => 'btn btn-success btn-xs',
                                        // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                        'title' => 'View',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalIcsItemView-' . $model->id,
                                        'data-url' => Url::to(['inspection-acceptance-report/ppms-ics-view', 'id' => $model->id,])

                                    ]);
                                },
                                'icscreate' => function ($url, $model, $key) {
                                    echo '<div class="modal fade" id="modalIcsCreate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalIcsCreate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalIcsCreate-$model->id"]', function() {
                                            var modal = $('#modalIcsCreate-$model->id');
                                            var url = $(this).data('url');
        
                                            modal.find('.modal-body').load(url);
                                        });
                                    JS;

                                    // var_dump($model);die;
                                    return Html::a('<span class="glyphicon glyphicon-plus-sign"></span> ICS CREATE ', $url, [
                                        'class' => 'btn btn-info btn-xs',
                                        // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                        'title' => 'Create IAR',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalIcsCreate-' . $model->id,
                                        'data-url' => Url::to(['inspection-acceptance-report/ppms-ics-create', 'id' => $model->id,])

                                    ]);
                                },
                            ],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'options' => ['style' => 'width:30%'],
                            'header' => 'PAR',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{parview} {parcreate}',
                            'urlCreator' => function ($action, $model) {
                                if ($action == 'parview') {
                                    return ['inspection-acceptance-report/ppms-par-view', 'id' => $model->id,];
                                }
                                if ($action == 'parcreate') {
                                    return ['inspection-acceptance-report/ppms-par-create', 'id' => $model->id,];
                                }
                            },
                            'buttons' => [
                                'parview' => function ($url, $model, $key) {

                                    echo '<div class="modal fade" id="modalParItemView-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalParItemView-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalParItemView-$model->id"]', function() {
                                            var modal = $('#modalParItemView-$model->id');
                                            var url = $(this).data('url');
        
                                            modal.find('.modal-body').load(url);
                                        });
                                    JS;

                                    // var_dump($model);die;
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span> PAR VIEW ', $url, [
                                        'class' => 'btn btn-success btn-xs',
                                        // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                        'title' => 'View',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalParItemView-' . $model->id,
                                        'data-url' => Url::to(['inspection-acceptance-report/ppms-par-view', 'id' => $model->id,])

                                    ]);
                                },
                                'parcreate' => function ($url, $model, $key) {
                                    echo '<div class="modal fade" id="modalParCreate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalParCreate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalParCreate-$model->id"]', function() {
                                            var modal = $('#modalParCreate-$model->id');
                                            var url = $(this).data('url');
        
                                            modal.find('.modal-body').load(url);
                                        });
                                    JS;

                                    // var_dump($model);die;
                                    return Html::a('<span class="glyphicon glyphicon-plus-sign"></span> PAR CREATE ', $url, [
                                        'class' => 'btn btn-info btn-xs',
                                        // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                        'title' => 'Create PAR',
                                        'data-toggle' => 'modal',
                                        'data-target' => '#modalParCreate-' . $model->id,
                                        'data-url' => Url::to(['inspection-acceptance-report/ppms-par-create', 'id' => $model->id,])

                                    ]);
                                },
                            ],
                        ],
                     
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => '',
                            'template' => '{ics} {par}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'PDF'
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'ics' => function ($url, $model) {
                                    return '<li>' . Html::a('ICS', ['inspection-acceptance-report/ppms-ics-pdf', 'id' => $model['id']], ['target' => '_blank']) . '</li>';
                                },
                                'par' => function ($url, $model) {
                                    return '<li>' . Html::a('PAR', ['inspection-acceptance-report/ppms-par-pdf', 'id' => $model['id']], ['target' => '_blank']) . '</li>';
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

    .modal-content {
        border-radius: 20px;
    }

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
        width: 300px;
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