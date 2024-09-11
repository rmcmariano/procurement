<?php

use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\bootstrap\Html;
use yii\helpers\Url;
use yii\bootstrap\Nav;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\TrackStatus;

?>

<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [

            [
                'label' => 'PR DETAILS',
                'url' => ['purchase-request/budget-prview', 'id' => $model->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'BUDGET MONITORING',
                'url' => ['purchase-request/pr-budgetmonitoring', 'id' => $model->id],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
            [
                'label' => 'PURCHASE ORDER / WORKING ORDER',
                'url' => ['purchase-order/powo-budgetview', 'id' => $model->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="purchase-request-budget">
    <p>
    <div class="box box-primary">
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
                    echo $approve = Html::button('<span class="glyphicon glyphicon-ok-sign"></span> Approve', ['value' => Url::to(['purchase-request/budget-prapproved', 'id' => $model->id,]), 'value' => $_GET['id'], 'class' => 'btn btn-success btn-lg approvedBtn']) . ' ';
                    echo ($disapprove = Html::button('<span class="glyphicon glyphicon-remove"></span> Disapproved',  ['class' => 'btn btn-danger btn-lg disapproveBtn', 'value' =>  $model["id"]]) . ' ');
                    echo (Html::button('Return to End-User', ['class' => 'btn btn-warning btn-lg revisionBtn', 'value' =>  $model["id"]]) . ' ');
                }
                ?>
            </div>
            <br>

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
                'pjax' => true,
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
                        'value' => function ($model) {
                            $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::BUDGET_STATUS])->one();
                            if ($bidding == NULL) {
                                return $model['item_name'];
                            }
                            if (in_array($model->status, ['2', '17', '13', '14', '18'])) {
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
                            $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::BUDGET_STATUS])->one();
                            if ($bidding == NULL) {
                                return $model['unit_cost'];
                            }
                            if (in_array($model->status, ['2', '17', '13', '14', '18'])) {
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
                            $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::BUDGET_STATUS])->one();

                            if ($bidding == NULL) {
                                return $model['total_cost'];
                            }
                            if (in_array($model->status, ['2', '17', '13', '14', '18'])) {
                                return $model['total_cost'];
                            }
                            return $model['quantity'] * $bidding['supplier_price'];
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
                                return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id],  ['class' => 'btn btn-info btn-xs', 'title' => 'History Logs']);
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
$this->registerJsVar('Cancel', Url::to(['purchase-request/budget-prdisapproved']));
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

    $('[data-toggle="tabajax"]').click(function(e) {
    var elem = $(this),
        loadurl = elem.attr('href'),
        targ = elem.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    elem.tab('show');
    return false;
    });


    $('.modalStatusbtn').on("click", function(){
        $('#modal-status').modal("show");
        $.get($(this).val(), function(data){
            $('#modalStatus').html(data);
        });
    });

    
    $('.revisionBtn').on('click', function() {
    var remarks = "";
    var id = $(this).val(); // Store the value of $(this).val() in a variable

            swal({
                title: "Return to End-user?",
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
            }).then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "/procurement/web/PurchaseRequest/purchase-request/fmd-request-btn",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                "id": id // Use the stored id variable here
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                console.error(xhr.responseText);
                            }
                        });
                    });
                } else {
                    swal("Canceled", {
                        icon: "warning",
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });
                }
            });
        });
        

  $('.approvedBtn').on('click', function() {
    var idToSubmit = $(this).val();
    swal({
        title: "Do you want to confirm the approval of request?",
        text: "Note: PR will be submit to BAC for processing.",
        icon: "warning",
        buttons: true,
    })
    .then((willSubmit) => {
        if (willSubmit) {
            swal("Approved", {
                icon: "success",
            }).then((value) => {
                $.ajax({
                    url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/budget-prapproved",
                    type: "get",
                    data: {
                        rtn: $(this).val()
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                    }
                });
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