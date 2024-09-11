<?php


use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */


Modal::begin([
    'header' => 'Create Quotation',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-create',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);
echo "<div id = 'modalCreate'></div>";
Modal::end();

Modal::begin([
    'header' => 'Add Schedule Details',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-input',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);
echo "<div id = 'modalInput'></div>";
Modal::end();

Modal::begin([
    'header' => 'Request For Quotation',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-rfq',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);
echo "<div id = 'modalRequest'></div>";
Modal::end();


?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="quotation-svp-index">
    <p>
    </p>
    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>Scheduling Details:</h3>
            </i>

            <left>
                <h5><i> Quotation Number:</i></h5>
                <h1> <?= (isset($quotation->quotation_no) ? $quotation->quotation_no : '') ?></h1>
            </left>

            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <?php
            echo ($quotation->quotation_no == NULL ? Html::button('<span class="glyphicon glyphicon-plus"></span> Create Quotation #', ['value' => Url::to(['bac-quotationcreate', 'id' => $purchaserequest->id,]),  'class' => 'btn btn-success modalCreatebtn']) . ' ' : Html::button('<span class="glyphicon glyphicon-plus"></span> Add Scheduling Details', ['value' => Url::to(['bac-quotationcreate', 'id' => $purchaserequest->id,]),  'class' => 'btn btn-success modalInputbtn']) . ' ');

            echo ($purchaserequest->status != 7 ?  Html::button('Request revision', ['class' => 'btn btn-warning revisionBtn', 'value' =>  $purchaserequest["id"]]) . ' ' : ' ');
            ?>

            <p>&nbsp;

                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'options' => ['style' => 'width: 100%'],
                    'export' => false,
                    'striped' => true,
                    'hover' => true,
                    'pjax' => true,
                    'panel' => ['type' => 'info',],
                    'floatHeader' => true,
                    'floatHeaderOptions' => ['scrollingTop' => '5'],
                    'columns' => [
                        [
                            'attribute' => 'option_id',
                            'value' => 'optionsdisplay.options',
                            'options' => ['style' => 'width:20%'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'DETAILS',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        ],
                        [
                            'attribute' => 'option_date',
                            'format' => 'raw',
                            'value' => function ($model) {
                              
                                return Yii::$app->formatter->asDatetime(($model->option_date), 'yyyy-mm-dd| H:i A');
                            },
                            'options' => ['style' => 'width:20%'],
                            'hAlign' => 'left',
                            'contentOptions' => ['style' => 'text-align: left'],
                            'header' => 'DATE',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'attribute' => 'remarks',
                            'options' => ['style' => 'width:20%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'REMARKS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],

                        [
                            'attribute' => 'reference_no',
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'REFERENCE #',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($model) {
                                if (isset($model->reference_no)) {
                                    return $model->reference_no;
                                }
                                return '-';
                            },
                        ],

                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'options' => ['style' => 'width:30%'],
                            'header' => 'ACTIONS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{rfq} {post} {view} {repost} ',
                            'buttons' => [
                                'rfq' => function ($url, $model, $key) {
                                    return Html::button('<span class="glyphicon glyphicon-print"></span> Create Canvass Form', ['value' => Url::to(['purchase-request/bac-quotation-rfqcreate', 'id' => $model->id,]),  'class' => 'btn btn-default btn-sm modalRfqbutton']);
                                },

                                'view' => function ($url, $model, $key) {
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Canvass Form PDF', ['purchase-request/bac-quotation-rfqpdf', 'id' => $model->id], ['class' => 'btn btn-default btn-sm', 'target' => '_blank']);
                                },

                                'post' => function ($url, $model, $key) {
                                    return Html::button('Input Reference #', ['class' => 'btn btn-info btn-sm refBtn', 'value' => $model->id]);
                                },

                                'repost' => function ($url, $model, $key) {
                                    return Html::button('Input Reference #', ['class' => 'btn btn-info btn-sm repostrefBtn', 'value' => $model->id]);
                                },

                            ],

                            'visibleButtons' => [
                                'rfq' => function ($model) {
                                    // var_dump($model);die;
                                    if ($model['option_id'] == 4 && $model['status'] == NULL) {
                                        return true;
                                    }
                                    return false;
                                },
                                'post' => function ($model) {
                                    if ($model['option_id'] == 2 && $model['reference_no'] == NULL) {
                                        return true;
                                    }
                                    return false;
                                },
                                'repost' => function ($model) {
                                    if ($model['option_id'] == 7 && $model['reference_no'] == NULL) {
                                        return true;
                                    }
                                    return false;
                                },
                                'view' => function ($model) {
                                    if ($model['status'] == 1 && $model['option_id'] == 4) {
                                        return true;
                                    }
                                    return false;
                                },
                            ],
                        ],
                    ],
                ]); ?>
        </div>
    </div>
    </p>
</div>




<?php
$this->registerJsVar('Revision', Url::to(['purchase-request/bac-revisionrequest']));
$this->registerJsVar('Reference', Url::to(['https://procurement.itdi.ph/PurchaseRequest/purchase-request/bac-quotation-philgepscreate']));

$this->registerJs(
    <<<JS

    // request revision btn
      $('.revisionBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Request Revision of PR?",
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
            })
            .then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/bac-revisionrequest",
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

        // create btn posted philgeps reference number
        $('.refBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Input Reference Number in PHILGEPS",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Reference Number:',
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/bac-quotation-philgepscreate",
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

      // create btn posted philgeps reference number
        $('.repostrefBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Input Reference Number in PHILGEPS",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Reference Number:',
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/bac-repostrefnumcreate",
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

    $('.modalCreatebtn').on("click", function(){
        $('#modal-create').modal("show");
        $.get($(this).val(), function(data){
            $('#modalCreate').html(data);
        });
    });

    $('.modalInputbtn').on("click", function(){
        $('#modal-input').modal("show");
        $.get($(this).val(), function(data){
            $('#modalInput').html(data);
        });
    });

    $('.modalRfqbutton').on("click", function(){
        $('#modal-rfq').modal("show");
        $.get($(this).val(), function(data){
            $('#modalRequest').html(data);
        });
    });


    
JS
);
?>