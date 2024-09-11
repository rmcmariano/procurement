<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\user\models\Profile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\grid\DataColumn;
use yii\bootstrap\Nav;

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php
Modal::begin([
    'header' => 'Bidding with Supplier',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-bid',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalBid'></div>";
Modal::end();

Modal::begin([
    'header' => 'Bidding with Supplier',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-nonbid',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalNonBid'></div>";
Modal::end();

Modal::begin([
    'header' => 'Add Schedule for Reposting',
    'id' => 'modal-reposting',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalReposting'></div>";
Modal::end();

Modal::begin([
    'header' => 'Comply Bidder',
    'id' => 'modal-comply',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalComply'></div>";
Modal::end();


Modal::begin([
    'header' => 'Create Signatories',
    'id' => 'modal-signatories',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalSignatories'></div>";
Modal::end();

?>
<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [

            [
                'label' => 'PR DETAILS',
                'url' => ['purchase-request/bac-prview', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'SCHEDULING DETAILS',
                'url' => ['purchase-request/bac-quotationindex', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'BID BULLETIN',
                'url' => ['purchase-request/pr-itemsbidbulletinlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
                'visible' => in_array($purchaserequest->mode_pr_id, ['1', '2', '3']),
            ],
            [
                'label' => 'SUBMISSION & OPENING OF BIDS',
                'url' => ['bidding/bac-biddingitemlist-smv', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
                'visible' => !in_array($purchaserequest->mode_pr_id, ['1', '2', '3']),
                'active' => true,
            ],
            [
                'label' => 'SUBMISSION & OPENING OF BIDS',
                'url' => ['bidding/bac-biddingitemlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
                'visible' => in_array($purchaserequest->mode_pr_id, ['1', '2', '3']),
            ],
            [
                'label' => 'WINNING BIDDERS',
                'url' => ['bidding/bac-bidding-complyinglist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
            [
                'label' => 'RESOLUTION',
                'url' => ['bidding/bac-resolutionlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<div class="bidding">
    <?php $form = ActiveForm::begin(); ?>
    <p>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <left>
                <i>
                    <h5>Purchase Request Number:</h5>
                </i>
                <h1><?= $purchaserequest->pr_no ?></h1>
            </left>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <i>
                <h3>Bidding:</h3>
            </i>
            <p>
                <?= (in_array($purchaserequest->mode_pr_id, ['1', '2', '3'])) ?  Html::button('<span class="glyphicon glyphicon-plus"></span> Add New Bid', ['value' => Url::to(['bidding/bac-biddingcreate?id=' . $_GET['id']]), 'data-id' => Yii::$app->request->get('id'), 'class' => 'btn btn-success modalBid']) :  Html::button('<span class="glyphicon glyphicon-plus"></span> Add New Bid', ['value' => Url::to(['bidding/bac-nonbidding-create?id=' . $_GET['id']]), 'data-id' => Yii::$app->request->get('id'), 'class' => 'btn btn-success modalNonBid']); ?> &nbsp;
            </p>

            <?= GridView::widget([
                'id' => 'quotationTable',
                'dataProvider' => $dataProvider,
                'showPageSummary' => true,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'export' => false,
                'striped' => true,
                'hover' => true,
                'pjax' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '10'],
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width: 3%'],
                    ],
                    [
                        'attribute' => 'unit',
                        'options' => ['style' => 'width:5%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'UNIT',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'item_name',
                        'format' => 'ntext',
                        'options' => ['style' => 'width:40%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'ITEM DESCRIPTION',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'unit_cost',
                        'format' => [
                            'decimal', 2
                        ],
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'UNIT COST',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'quantity',
                        'options' => ['style' => 'width:5%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'QUANTITY',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'class' => DataColumn::class,
                        'attribute' => 'total_cost',
                        'format' => [
                            'decimal', 2
                        ],
                        'options' => ['style' => 'width:10%'],
                        'pageSummary' => true,
                        'pageSummaryOptions' => ['style' => 'text-align: right'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'TOTAL COST',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            if (isset($model->statusdisplay)) {
                                return $model->statusdisplay->status;
                            }

                            return 'No Bidder';
                        },
                        'options' => ['style' => 'width:15%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'STATUS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'header' => '',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{non-bidder} {evaluate}',
                        'options' => ['style' => 'width:5%'],
                        'buttons' => [
                            'non-bidder' => function ($url, $model, $key) {
                                return Html::button('Insufficient bidder?', ['value' => Url::to(['purchase-request/bac-reposting-create', 'id' => $model->id]), 'class' => 'btn btn-warning btn-xs modalRepostingbtn']);
                            },
                            'evaluate' => function ($url, $model, $key) {

                                return Html::button('Evaluate Bidders',  ['class' => 'btn btn-info btn-xs evaluateBtn', 'value' =>  $model["id"]]);
                            },
                        ],
                        'visibleButtons' => [
                            'non-bidder' => function ($model) {

                                if (in_array($model->status, ['14', '16'])) {
                                    return false;
                                }
                                return true;
                            },
                        ],
                    ],
                ]
            ]);
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>List of Bidders:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>
            <p>
                <?= Html::a('<span class="glyphicon glyphicon-print"></span> Print Abstract', ['bidding/bac-biddingabstract-pdf', 'id' => $purchaserequest['id']], ['target' => '_blank', 'class' => 'btn btn-default']) ?>

            </p>
            <?= GridView::widget([
                'id' => 'biddingTable',
                'dataProvider' => $dataProvider2,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'layout' => "{summary}\n{items}\n{pager}",
                'tableOptions' => ['class' => 'table  table-bordered table-hover'],
                'export' => false,
                'striped' => true,
                'hover' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width:3%'],
                    ],
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail' => function ($model, $key, $index, $column) {
                            $items = PrItems::find()->where(['id' => $model->item_id])->one();

                            $searchModel = new ItemSpecificationSearch();
                            $searchModel->item_id = $items->id;
                            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                            return Yii::$app->controller->renderPartial('/purchase-request/pr_itemspecs_expand_view', [
                                'dataProvider' => $dataProvider,
                                'searchModel' => $searchModel,
                                'model' => $model
                            ]);
                        },
                    ],
                    [
                        'attribute' => 'time_stamp',
                        'header' => 'DATE',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime(strtotime($model->time_stamp), 'php:d-M-Y');
                        },
                        'options' => ['style' => 'width:10%'],
                        'contentOptions' => ['style' => 'text-align: center'],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'item_id',
                        'format' => 'ntext',
                        'header' => 'ITEM DESCRIPTION',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            $item = PrItems::find()->where(['id' => $model->item_id])->one();

                            return $item->item_name;
                        },
                        'options' => ['style' => 'width:30%'],
                        'contentOptions' => ['style' => 'text-align: left'],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'supplier_id',
                        'header' => 'BIDDERS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => 'supplierdisplay.supplier_name',
                        'options' => ['style' => 'width:13%'],
                        'contentOptions' => ['style' => 'text-align: center'],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'supplier_price',
                        'header' => 'BID PRICE',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'format' => [
                            'decimal', 2
                        ],
                        'options' => ['style' => 'width:7%'],
                        'contentOptions' => ['style' => 'text-align: right'],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'asssign_twg',
                        'header' => 'ASSIGN-TWG',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            $name = Profile::find()->where(['user_id' => $model->assign_twg])->one();
                            if (isset($model->userdisplay)) {
                                return $name->fname . ' ' .  $name->lname;
                            }
                            return 'No TWG Assigned';
                        },
                        'options' => ['style' => 'width:10%'],
                        'contentOptions' => ['style' => 'text-align: center'],
                        'hAlign' => 'center',
                    ],
                    [
                        'attribute' => 'status',
                        'header' => 'STATUS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            if (isset($model->statusdisplay)) {
                                return $model->statusdisplay->status;
                            }
                            return 'No Bidders';
                        },
                        'options' => ['style' => 'width:10%'],
                        'contentOptions' => ['style' => 'text-align: center'],
                        'hAlign' => 'center',
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{comply} {non-comply}',
                        'buttons' => [
                            'non-comply' => function ($url, $model, $key) {
                                $status = PrItems::find()->where(['id' => $model->item_id])->one();

                                $enable = Html::button('<span class="glyphicon glyphicon-remove"></span> Non-comply',  ['class' => 'btn btn-danger btn-sm noncomply', 'value' =>  $model["id"]]);
                                $disable =  Html::button('<span class="glyphicon glyphicon-remove"></span> Non-Comply', ['value' => Url::to(['bidding/bac-biddingnoncomply', 'id' => $model->id,]),  'class' => 'btn btn-danger btn-sm modalNoncomplybtn', 'disabled' => true]);

                                if ($status['status'] == 16 || $status['status'] == 17) {
                                    return $disable;
                                }
                                return $enable;
                            },

                            'comply' => function ($url, $model, $key) {
                                $status = PrItems::find()->where(['id' => $model->item_id])->one();

                                $enable = Html::button('<span class="glyphicon glyphicon-check"></span> Comply', ['value' => Url::to(['bidding/bac-biddingcomply-modal', 'id' => $model->id]), 'class' => 'btn btn-success btn-sm modalComplybtn']);
                                $disable = Html::button('<span class="glyphicon glyphicon-check"></span> Comply', ['value' => Url::to(['bidding/bac-biddingcomply-modal', 'id' => $model->id]), 'class' => 'btn btn-success btn-sm modalComplybtn', 'disabled' => true]);

                                if ($status['status'] == 16 || $status['status'] == 17 || $status['status'] == 14 || $status['status'] == 56) {
                                    return $disable;
                                }
                                return $enable;
                            },
                        ],
                        'visibleButtons' => [
                            'non-comply' => function ($model) {

                                if (in_array($model->status, ['15'])) {
                                    return false;
                                }
                                return true;
                            },
                            'comply' => function ($model) {
                                if (in_array($model->status, ['15'])) {
                                    return false;
                                }
                                return true;
                            }
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
    </p>
    <?php ActiveForm::end(); ?>
</div>



<?php
$this->registerJsVar('Cancel', Url::to(['bidding/bac-biddingnoncomply']));
$this->registerJs(
    <<<JS

    $('.noncomply').on('click', function() {
            var remarks = "";
            swal({
                title: "Non-complying Bidder?",
                icon: "info",
                input: "textarea",
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
                            url: "/bidding/bac-biddingnoncomply",
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

        $('.evaluateBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Do you want to submit this to End-users for Evaluation of Bidders?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Add Remarks:',
                content: "input",
                closeOnClickOutside: false,
                closeOnEsc: false,
            }).then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "/bidding/bac-evaluation-remarks",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                id: $(this).val()
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr, status, error) {
                                // Handle errors if needed
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


    $('.modalBid').on("click", function(){
    $('#modal-bid').modal("show");
        var selectedKeys = $('#quotationTable').yiiGridView('getSelectedRows');
        var currentId = $(this).data('id');

        $.get(
            $(this).val(),
        {
            id: currentId,
            keys: selectedKeys
        }, 
        function(data){
            $('#modalBid').html(data);
        });
    });

    $('.modalNonBid').on("click", function(){
    $('#modal-nonbid').modal("show");
        var selectedKeys = $('#quotationTable').yiiGridView('getSelectedRows');
        var currentId = $(this).data('id');

        $.get(
            $(this).val(),
        {
            id: currentId,
            keys: selectedKeys
        }, 
        function(data){
            $('#modalNonBid').html(data);
        });
    });

    $('.modalComplybtn').on("click", function(){
    $('#modal-comply').modal("show");
        $.get($(this).val(), function(data){
            $('#modalComply').html(data);
        });
    });

    $('.modalSignatoriesbtn').on("click", function(){
        $('#modal-signatories').modal("show");
        $.get($(this).val(), function(data){
            $('#modalSignatories').html(data);
        });
    });

    $('.modalRepostingbtn').on("click", function(){
        $('#modal-reposting').modal("show");
        $.get($(this).val(), function(data){
            $('#modalReposting').html(data);
        });
    });

JS
);
?>

<style>
    .btnPdf {
        border: none;
        outline: none;
        background-color: none;
        background: none;
        color: #808080;
        margin-left: 12px;
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

    .nav-tabs li a {
        background-color: #5F9EA0;
        color: #000000;
        font-weight: bold;
        border-top-right-radius: 16px 16px;
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