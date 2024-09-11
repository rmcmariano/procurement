<?php

use app\modules\PurchaseRequest\models\BiddingListSearch;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseOrder;
use app\modules\PurchaseRequest\models\PurchaseOrderItemsSearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\bootstrap\Nav;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


Modal::begin([
    'header' => 'Work Order',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-wo',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalWo'></div>";
Modal::end();


Modal::begin([
    'header' => 'Purchase Order',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-order',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalOrder'></div>";
Modal::end();

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                'active' => true,
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
            ],
            [
                'label' => 'DISBURSEMENT VOUCHER',
                'url' => ['purchase-order/ppms-dv-index', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<div class="awarding-list-view">
    <p>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <left>
                <i>
                    <h5>Purchase Request Number:</h5>
                </i>
                <h1><?= $purchaserequest->pr_no ?></h1>
            </left>

            <i>
                <h3>Awarded Bidders:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <p>
                <?php
                // echo (in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19']) ? Html::button('<span class="glyphicon glyphicon-plus"></span> Create New Work Order', ['value' => Url::to(['purchase-order/ppms-purchaseorder-create?id=' . $_GET['id']]), 'data-id' => Yii::$app->request->get('id'), 'class' => 'btn btn-success modalOrder']) . ' ' : Html::button('<span class="glyphicon glyphicon-plus"></span> Create New Purchase Order', ['value' => Url::to(['purchase-order/ppms-purchaseorder-create?id=' . $_GET['id']]), 'data-id' => Yii::$app->request->get('id'), 'class' => 'btn btn-success modalOrder']) . ' ');
                echo (in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19'])
                    ? Html::button('<span class="glyphicon glyphicon-plus"></span> Create New Work Order', [
                        'value' => Url::to(['purchase-order/ppms-purchaseorder-create?id=' . $_GET['id']]),
                        'data-id' => Yii::$app->request->get('id'),
                        'class' => 'btn btn-success modalOrder'
                    ])
                    : Html::button('<span class="glyphicon glyphicon-plus"></span> Create New Purchase Order', [
                        'value' => Url::to(['purchase-order/ppms-purchaseorder-create?id=' . $_GET['id']]),
                        'data-id' => Yii::$app->request->get('id'),
                        'class' => 'btn btn-success modalOrder'
                    ]));
                ?>

            <h1><?= Html::encode($this->title) ?></h1>

            <?= GridView::widget([
                'id' => 'biddingTable',
                'dataProvider' => $dataProvider,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'export' => false,
                'striped' => true,
                'hover' => true,
                'pjax' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'rowOptions' => function ($model) {
                    $options = [
                        'class' => 'cb-rows',
                        'data-supplier' => $model->supplier_id
                    ];
                    return $options;
                },
                'columns' => [

                    [
                        'class' => 'kartik\grid\CheckboxColumn',
                        'header' => Html::checkbox('selection_all', false, ['class' => 'select-on-check-all', 'value' => 1, 'onclick' => '$(".kv-row-checkbox").prop("checked", $(this).is(":checked"));', 'disabled' => true]),
                        'contentOptions' => ['class' => 'kv-row-select test', 'id' => 'test'],
                        'content' => function ($model, $key) {
                            return Html::checkbox('selection[]', false, ['class' => 'kv-row-checkbox', 'value' => $model->item_id, 'onclick' => '$(this).closest("tr").toggleClass("danger");', 'disabled' => ($model['status'] == 44) ? true : false]);
                        },
                        'hAlign' => 'center',
                        'vAlign' => 'middle',
                        'hiddenFromExport' => true,
                        'mergeHeader' => true,
                        'options' => ['style' => 'width:3%'],
                    ],
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width:3%'],
                    ],
                    [
                        'attribute' => 'item_id',
                        'value' => function ($model) {
                            $item = PrItems::find()->where(['id' => $model->item_id])->one();
                            return $item->item_name;
                            // if ($model->item_remarks == $item->id) {
                            //     return $item->item_name;
                            // }
                            // return $model->item_remarks;
                        },
                        'format' => 'ntext',
                        'options' => ['style' => 'width:30%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'EQUIPMENT NAME',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'supplier_id',
                        'value' => 'supplierdisplay.supplier_name',
                        'options' => ['style' => 'width:15%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'BIDDERS NAME',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'supplier_price',
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'right',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'BIDDER PRICE',
                        'headerOptions' => ['style' => 'color:#337ab7;  text-align: center'],
                        'format' => [
                            'decimal', 2
                        ],
                    ],
                    [
                        'attribute' => 'quantity',
                        'options' => ['style' => 'width:5%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'QTY',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            $test = PrItems::find()->where(['id' => $model['item_id']])->one();
                            return $test->quantity;
                        }
                    ],
                    [
                        'attribute' => 'supplier_totalprice',
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'right',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'TOTAL',
                        'headerOptions' => ['style' => 'color:#337ab7;  text-align: center'],
                        'value' => function ($model) {
                            $test = PrItems::find()->where(['id' => $model['item_id']])->one();
                            return $test['quantity'] * $model['supplier_price'];
                        },
                        'format' => [
                            'decimal', 2
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
    </p>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>Purchase Order / Work Order Lists:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>

                <?= GridView::widget([
                    'id' => 'grid-id',
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
                            'value' => function ($model) {
                             
                                if ($model->project_type_series_id == 0 || $model->project_type_series_id === NULL) {
                                    return $model->po_no;
                                } else if ($model->project_type_series_id == 1) {
                                    if ($model->item_type_series_id == 1) {
                                        return 'GIA' . 'E-' . $model->po_no;
                                    } else if ($model->item_type_series_id == 2) {
                                        return 'GIA' . 'S-' . $model->po_no;
                                    } else if ($model->item_type_series_id == 3) {
                                        return 'GIA' . 'C-' . $model->po_no;
                                    } else if ($model->item_type_series_id == NULL) {
                                        return 'GIA-' . $model->po_no;
                                    }
        
                                } else if ($model->project_type_series_id == 2) {
                                    if ($model->item_type_series_id == 1) {
                                        return 'GAA' . 'E-' . $model->po_no;
                                    } else if ($model->item_type_series_id == 2) {
                                        return 'GAA' . 'S-' . $model->po_no;
                                    } else if ($model->item_type_series_id == 3) {
                                        return 'GAA' . 'C-' . $model->po_no;
                                    }  else if ($model->item_type_series_id == NULL) {
                                        return 'GAA-' . $model->po_no;
                                    }
                                }
                               

                            }
                        ],
                        [
                            'attribute' =>  'po_date_created',
                            'header' => 'DATE CREATED',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:8%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php:M d, Y');
                            },
                        ],
                        [
                            'attribute' =>  'ors_burs_num',
                            'header' => 'ORS/BURS #',
                            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                            'options' => ['style' => 'width:10%;'],
                            'contentOptions' => ['style' => 'text-align: center'],
                        ],
                        [
                            'attribute' => 'supplier_id',
                            'value' => function ($model) {
                                $supplier = Supplier::find()->where(['id' => $model->supplier_id])->one();
                                return $supplier->supplier_name;
                            },
                            'options' => ['style' => 'width:30%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: left'],
                            'header' => 'BIDDERS NAME',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],

                        [
                            'attribute' => 'po_status',
                            'value' => function ($model) {
                                if ($model->po_status == NULL) {
                                    return '-';
                                }
                                // created PO
                                if ($model->po_status == 1) {
                                    return 'CREATED';
                                }
                                // after the approval of PPMS and submitted to budget
                                if ($model->po_status == 2) {
                                    return 'FOR OBLIGATION';
                                }
                                // declined by PPMS
                                if ($model->po_status == 3) {
                                    return 'DECLINED';
                                }
                                // obligated by FMD-budget
                                if ($model->po_status == 4) {
                                    return 'OBLIGATED';
                                }
                                // validated by FMD-accounting
                                if ($model->po_status == 5) {
                                    return 'VALIDATED';
                                }
                                // for supplier's conforme
                                if ($model->po_status == 6) {
                                    return 'FOR CONFORME';
                                }
                                // for supplier's conforme
                                if ($model->po_status == 7) {
                                    return 'FOR DELIVERY';
                                }
                                // for delivery validated
                                if ($model->po_status == 8) {
                                    return 'DELIVERY VALIDATED';
                                }
                                // for delivery validated
                                if ($model->po_status == 9) {
                                    return 'Approval of End User for IAR Changes';
                                }
                            },
                            'options' => ['style' => 'width:15%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'Actions',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{poview} {accept} {cancel} {conforme} ',
                            'options' => ['style' => 'width:10%'],
                            'urlCreator' => function ($action, $model) {
                                if ($action == 'poview') {
                                    return ['purchase-order/purchaseorder-view', 'id' => $model->id,];
                                }
                            },
                            'buttons' => [
                                'poview' => function ($url, $model, $key) {

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
                                        'data-url' => Url::to(['purchase-order/purchaseorder-view', 'id' => $model->id,])
                                    ]);
                                },
                                'accept' => function ($url, $model) {
                                    return Html::button('<span class="glyphicon glyphicon-check"></span>',  ['value' => $model['id'], 'class' => 'btn btn-info btn-xs enableBtn', 'title' => 'Accept']);
                                },
                                'conforme' => function ($url, $model) {
                                    return Html::button('<span class="glyphicon glyphicon-ok "></span> For Conforme',  ['value' => $model['id'], 'class' => 'btn btn-warning btn-xs conformBtn']);
                                },
                                'cancel' => function ($url, $model) {
                                    return Html::button('<span class="glyphicon glyphicon-remove"></span>',  ['value' => $model['id'], 'class' => 'btn btn-danger btn-xs cancelPo', 'title' => 'Cancel']);
                                    // return Html::button('<span class="glyphicon glyphicon-remove "></span>',  ['class' => 'btn btn-danger btn-sm cancelPo', 'value' => $model->id,  'title' => 'Cancel']);
                                },
                            ],
                            'visibleButtons' => [
                                'accept' => function ($model) {
                                    if (in_array($model->po_status, ['1'])) {
                                        return true;
                                    }
                                    return false;
                                },
                                'cancel' => function ($model) {
                                    if (in_array($model->po_status, ['3', '6', '7'])) {
                                        return false;
                                    }
                                    return true;
                                },
                                'conforme' => function ($model) {
                                    if ($model->po_status == 5) {
                                        return true;
                                    }
                                    return false;
                                }
                            ],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => '',
                            'template' => '{po}{wo}{ors}{burs}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'Generate PDF'
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'po' => function ($url, $model) {
                                    return '<li>' . Html::a('Purchase Order', ['purchase-order/ppms-purchaseorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'wo' => function ($url, $model) {
                                    return '<li>' . Html::a('Work Order', ['purchase-order/ppms-workorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'ors' => function ($url, $model) {
                                    return '<li>' . Html::a('ORS', ['purchase-order/ppms-ors-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'burs' => function ($url, $model) {
                                    return '<li>' . Html::a('BURS', ['purchase-order/ppms-burs-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                            ],
                        ],
                    ],
                ]); ?>
            </p>
        </div>
    </div>
</div>


<?php

$this->registerJsVar('Cancel', Url::to(['https://procurement.itdi.ph/PurchaseRequest/purchase-order/purchaseorder-cancel']));
$this->registerJs(
    <<<JS

var selectedSupplier = null;

    $('.modalPorderBtn').on("click", function(){
        $('#modal-porder').modal("show");
        $.get($(this).val(), function(data){
            $('#modalPurchaseOrder').html(data);
        });
    });

    // $('.modalWo').on("click", function(){
    // $('#modal-wo').modal("show");
    //     var selectedKeys = $('#biddingTable').yiiGridView('getSelectedRows');
    //     var currentId = $(this).data('id');
    //     $.get(
    //         $(this).val(),
    //         {
    //             id: currentId,
    //             keys: selectedKeys
    //         }, 
    //         function(data){
    //             $('#modalWo').html(data);
    //         });
    //     });
        
    //     $('.kv-row-checkbox').on("click", function(){
    //         var selectedKeys = $('#biddingTable').yiiGridView('getSelectedRows');

    //         if (selectedKeys.length > 0) {
    //             selectedKeys.forEach(function (item) {
    //             selectedSupplier = $('*[data-key="'+item+'"]').data('supplier');
    //         });

    //         modifyCheckbox();
    //         } else {
    //             $('.kv-row-checkbox').removeAttr('disabled');
    //             $('.select-on-check-all').removeAttr('disabled');
    //         }
    // });

    // $('.modalOrder').on("click", function(){
    // $('#modal-order').modal("show");
    //     var selectedKeys = $('#biddingTable').yiiGridView('getSelectedRows');
    //     var currentId = $(this).data('id');
    //     $.get(
    //         $(this).val(),
    //         {
    //             id: currentId,
    //             keys: selectedKeys
    //         }, 
    //         function(data){
    //             $('#modalOrder').html(data);
    //         });
    //     });
        
    //     $('.kv-row-checkbox').on("click", function(){
    //         var selectedKeys = $('#biddingTable').yiiGridView('getSelectedRows');

    //         if (selectedKeys.length > 0) {
    //             selectedKeys.forEach(function (item) {
    //             selectedSupplier = $('*[data-key="'+item+'"]').data('supplier');
    //         });

    //         modifyCheckbox();
    //         } else {
    //             $('.kv-row-checkbox').removeAttr('disabled');
    //         }
    // });

    // var modifyCheckbox = function () {
    //     $('.cb-rows').each(function (index) {
    //             var supplier_id = $(this).data('supplier');
                
    //             if (selectedSupplier != supplier_id) {
    //                 $(this).find('.kv-row-checkbox').attr('disabled', true);
    //                 // console.log($(this).find('.kv-row-checkbox'));
    //             } else {
    //                 $(this).find('.kv-row-checkbox').removeAttr('disabled');
    //             }
    //         });
    // }

        $('.modalWo, .modalOrder').on("click", function(){
            var modalId = $(this).hasClass('modalWo') ? '#modal-wo' : '#modal-order';
            var targetId = $(this).hasClass('modalWo') ? '#modalWo' : '#modalOrder';

            $(modalId).modal("show");

            var selectedKeys = $('#biddingTable').yiiGridView('getSelectedRows');
            var currentId = $(this).data('id');

            $.get(
                $(this).val(),
                {
                    id: currentId,
                    keys: selectedKeys
                }, 
                function(data){
                    $(targetId).html(data);
                }
            );
        });

        $('.kv-row-checkbox').on("click", function(){
            var selectedKeys = $('#biddingTable').yiiGridView('getSelectedRows');

            if (selectedKeys.length > 0) {
                var selectedSupplier;

                selectedKeys.forEach(function (item) {
                    selectedSupplier = $('*[data-key="'+item+'"]').data('supplier');
                });

                modifyCheckbox(selectedSupplier);
            } else {
                $('.kv-row-checkbox').removeAttr('disabled');
            }
        });

        var modifyCheckbox = function (selectedSupplier) {
            $('.cb-rows').each(function (index) {
                var supplier_id = $(this).data('supplier');
                
                if (selectedSupplier != supplier_id) {
                    $(this).find('.kv-row-checkbox').attr('disabled', true);
                } else {
                    $(this).find('.kv-row-checkbox').removeAttr('disabled');
                }
            });
        }

    $('.enableBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to Approved?",
        text: "Note: This will be forwarded to FMD-Budget",
        icon: "warning",
        buttons: true,
        safeMode: true,
        buttons: ["Cancel", "Proceed to Submit"],
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Success", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/ppms-purchaseorder-accept",
            type: "get",
            data: {
              acpt: $(this).val()
            },
            
          }); console.log(data);
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            location.reload();
          });
        }
      });
  });


$('.conformBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to Proceed?",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Accepted", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/ppms-po-submitforconforme",
            type: "get",
            data: {
              con: $(this).val()
            },
            
          }); console.log(data);
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            // location.reload();
          });
        }
      });
  });

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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/ppms-purchaseorder-cancel",
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