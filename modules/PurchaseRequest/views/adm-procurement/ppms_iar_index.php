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
use yii\bootstrap\Nav;


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
                'active' => true,
            ],
            [
                'label' => 'PAR/ICS',
                'url' => ['inspection-acceptance-report/ppms-ics-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
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
<div class="iar-list-index">
    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>List of IAR:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>

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

                                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_iar_items_expand', [
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
                                if ($iar == NULL) {
                                    return '-';
                                }
                                return $iar->iar_number;
                            },
                        ],
                        [
                            'attribute' =>  'iar_date',
                            'header' => 'IAR CREATED',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:8%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL) {
                                    return '-';
                                }
                                return Yii::$app->formatter->asDatetime(strtotime($iar->iar_date), 'php:M d, Y');
                            },
                        ],
                        [
                            'attribute' =>  'sales_invoice_number',
                            'header' => 'DELIVERY #',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL) {
                                    return '-';
                                }
                                return $iar->sales_invoice_number;
                            },
                        ],
                        [
                            'attribute' =>  'sales_invoice_date',
                            'header' => 'DELIVERY DATE',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL) {
                                    return '-';
                                }
                                return Yii::$app->formatter->asDatetime(strtotime($iar->sales_invoice_date), 'php:M d, Y');
                            },
                        ],

                        [
                            'attribute' =>  'inspector_id',
                            'header' => 'INSPECTOR',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL) {
                                    return '-';
                                }
                                return $iar->inspector_id;
                            },
                        ],
                        [
                            'attribute' =>  'inspection_date',
                            'header' => 'INSPECTION DATE',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:8%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL) {
                                    return '-';
                                }
                                return Yii::$app->formatter->asDatetime(strtotime($iar->inspection_date), 'php:M d, Y');
                            },
                        ],
                        [
                            'attribute' =>  'iar_status',
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                $iar = InspectionAcceptanceReport::find()->where(['po_id' => $model->id])->one();
                                if ($iar == NULL) {
                                    return '-';
                                }
                                if ($iar->iar_status == '1') {
                                    return 'Pending';
                                }
                            },
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{view} {create} ',
                            'options' => ['style' => 'width:10%'],
                            'urlCreator' => function ($action, $model) {

                                if ($action == 'create') {
                                    return ['inspection-acceptance-report/ppms-iar-create', 'id' => $model->id,];
                                }
                                if ($action == 'view') {
                                    return ['inspection-acceptance-report/iar-view', 'id' => $model->id,];
                                }
                            },
                            'buttons' => [
                                'create' => function ($url, $model, $key) {
                                    echo '<div class="modal fade" id="modalIarCreate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalIarCreate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalIarCreate-$model->id"]', function() {
                                            var modal = $('#modalIarCreate-$model->id');
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
                                        'data-target' => '#modalIarCreate-' . $model->id,
                                        'data-url' => Url::to(['inspection-acceptance-report/ppms-iar-create', 'id' => $model->id,])

                                    ]);
                                },
                                'view' => function ($url, $model, $key) {

                                    echo '<div class="modal fade" id="modalIarView-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalIarView-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                    $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalIarView-$model->id"]', function() {
                                            var modal = $('#modalIarView-$model->id');
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
                                        'data-target' => '#modalIarView-' . $model->id,
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
                                'label' =>  'IAR PDF'
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'iar' => function ($url, $model) {
                                    return '<li>' . Html::a('IAR', ['inspection-acceptance-report/ppms-iar-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
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