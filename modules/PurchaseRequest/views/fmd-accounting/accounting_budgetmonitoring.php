<?php

use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [
            [
                'label' => 'PR DETAILS',
                'url' => ['purchase-request/accounting-prview', 'id' => $model->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'ITEM DETAILS',
                'url' => ['purchase-request/pr-accountingmonitoring', 'id' => $model->id],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
            [
                'label' => 'PURCHASE ORDER / WORKING ORDER',
                'url' => ['purchase-order/accounting-po-index', 'id' => $model->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<br>
<div class="purchase-request-index">
    <div class="panel panel-default">
        <div style="padding: 20px">
            <left>
                <i>
                    <h5>Purchase Request Number:</h5>
                </i>
                <h1><?= $model->pr_no ?></h1>
            </left>
            <div style="text-align:center">
                <?php
                if ($model->status == 2) {
                    echo $approve = Html::button('<span class="glyphicon glyphicon-ok-sign"></span> Approve', ['value' => Url::to(['purchase-request/accounting-prapproved', 'id' => $model->id,]), 'value' => $_GET['id'], 'class' => 'btn btn-success btn-lg approvedBtn']) . ' ';
                    echo ($disapprove = Html::button('<span class="glyphicon glyphicon-remove"></span> Disapproved',  ['class' => 'btn btn-danger btn-lg disapproveBtn', 'value' =>  $model["id"]]) . ' ');
                }
                ?>
            </div>
            <p>

            <?= GridView::widget([
                'id' => 'grid-id',
                'dataProvider' => $dataProvider,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'striped' => true,
                'hover' => true,
                'export' => false,
                'showPageSummary' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'rowOptions' => function ($url) {
                    if (in_array($url->status, ['18'])) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width:2%'],
                        'header' => '#',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    ],
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
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
                        'options' => ['style' => 'width:3%'],
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
                        'format' => 'ntext',
                        'header' => 'ITEM/EQUIPMENT NAME',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:30%'],
                    ],
                    [
                        'attribute' => 'unit_cost',
                        'header' => 'UNIT COST',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                            if (in_array($model->status, ['2', '17', '13', '14'])) {
                                return $model['unit_cost'];
                            }
                            return $model->unit_cost;
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
                            if (in_array($model->status, ['2', '17', '13', '14'])) {
                                return $model['total_cost'];
                            }
                            return $model['quantity'] * $model['unit_cost'];
                        },
                        'format' => [
                            'decimal', 2
                        ],
                        'hAlign' => 'right',
                        'options' => ['style' => 'width:10%'],
                        'pageSummary' => true
                    ],
                    [
                        'attribute' => 'status',
                        'header' => 'STATUS',
                        'value' => function ($model) {
                            if (isset($model->statusdisplay)) {
                                return $model->statusdisplay->status;
                            }
                            return 'No Bidder';
                        },
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:20%'],
                        'contentOptions' => ['style' => 'text-align: center']
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:10%'],
                        'header' => 'Actions',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{status}',
                        'buttons' => [
                            'status' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-list-alt"></span> Logs', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id],  ['class' => 'btn btn-info btn-sm']);
                            },
                        ],
                    ],
                ],
            ]);
            ?>
        </div>
    </div>
    </p>
</div>



<?php

$this->registerJsVar('Cancel', Url::to(['https://procurement.itdi.ph/PurchaseRequest/purchase-request/budget-prdisapproved']));

$this->registerJs(
    <<<JS

        $('.disapproveBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Do you want to decline the request?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Remarks:',
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/budget-prdisapproved",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                id: $(this).val()
                            }
                        }); 
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

    $('.modalStatusbtn').on("click", function(){
        $('#modal-status').modal("show");
        $.get($(this).val(), function(data){
            $('#modalStatus').html(data);
        });
    });

    
    //sweetalert 
    $('.approvedBtn').on('click', function() {
        swal({
            title: "Do you want to confirm the approval of request?",
            icon: "warning",
            buttons: true,
            safeMode: true,
        })
        
        .then((willSubmit) => {
            if (willSubmit) {
            swal("Approved", {
                icon: "success",
            }).then((value) => {
                location.reload();
            });

            $.ajax({
                url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/accounting-prapproved",
                type: "get",
                data: {
                    accntngId: $(this).val()
                },
            }); 
            } else {
            swal("Canceled", {
                icon: "error",
            }).then((value) => {
                location.reload();
            });
            
            }
        });
    });

JS
);
?>

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
        width: 400px;
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