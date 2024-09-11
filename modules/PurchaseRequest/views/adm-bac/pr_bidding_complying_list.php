<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Resolution;
use app\modules\user\models\Profile;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

Modal::begin([
    'header' => 'Generate Resolution No.',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-reso',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalReso'></div>";
Modal::end();

$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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
                'active' => true,
            ],
            [
                'label' => 'RESOLUTION',
                'url' => ['bidding/bac-resolutionlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<div class="awarding-list-index">
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
                <h3>Complying Bidder Lists:</h3>
            </i>

            <center>
                <?= Html::button('<span class="glyphicon glyphicon-print"></span> Generate Resolution No. ', ['value' => Url::to(['bidding/bac-resolution-generatenum?id=', 'id' => $_GET['id']]), 'class' => 'btn btn-warning resoBtn']); ?> &nbsp;
            </center>
            <p>

                <?= GridView::widget([
                    'id' => 'resolutionTable',
                    'dataProvider' => $dataProvider,
                    'responsive' => false,
                    'tableOptions' => ['style' => 'overflow-y: visible !important;'],
                    'panel' => ['type' => 'info'],
                    'export' => false,
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
                        // [
                        //     'class' => 'kartik\grid\CheckboxColumn',
                        //     'header' => Html::checkbox('selection_all', false, [
                        //         'class' => 'select-on-check-all',
                        //         'value' => 1,
                        //         'onclick' => '$(".kv-row-checkbox").prop("checked", $(this).is(":checked"));',
                        //         'disabled' => ($biddingNew['resolution_no'] != NULL) ? true : false // Set to true to disable
                        //     ]),
                        //     'contentOptions' => ['class' => 'kv-row-select test'],
                        //     'content' => function ($model, $key) {
                        //         return Html::checkbox('selection[]', false, [
                        //             'class' => 'kv-row-checkbox',
                        //             'value' => $model->id,
                        //             'onclick' => '$(this).closest("tr").toggleClass("danger");',
                        //             'disabled' => ($model['resolution_no'] != NULL) ? true : false // Set to true to disable
                        //         ]);
                        //     },
                        //     'hAlign' => 'center',
                        //     'vAlign' => 'middle',
                        //     'hiddenFromExport' => true,
                        //     'mergeHeader' => true,
                        //     'options' => ['style' => 'width:2%'],
                        // ],
                        [
                            'class' => 'kartik\grid\CheckboxColumn',
                            'header' => Html::checkbox('selection_all', false, ['class' => 'select-on-check-all', 'value' => 1, 'onclick' => '$(".kv-row-checkbox").prop("checked", $(this).is(":checked"));', 'disabled' => ($biddingNew['resolution_no'] != NULL) ? false : false]),
                            'contentOptions' => ['class' => 'kv-row-select test'],
                            'content' => function ($model, $key) {
                                return Html::checkbox('selection[]', false, ['class' => 'kv-row-checkbox', 'value' => $model->id, 'onclick' => '$(this).closest("tr").toggleClass("danger");', 'disabled' => ($model['resolution_no'] != NULL) ? false : false]);
                            },
                            'hAlign' => 'center',
                            'vAlign' => 'middle',
                            'hiddenFromExport' => true,
                            'mergeHeader' => true,
                            'options' => ['style' => 'width:2%'],
                        ],

                        [
                            'attribute' => 'resolution_date',
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'RESOLUTION DATE',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($model) {
                                $resolution = Resolution::find()->where(['id' => $model->resolution_no])->one();

                                if ($resolution == NULL) {
                                    return '-';
                                }
                                return $resolution->resolution_date;
                            },
                        ],
                        [
                            'attribute' => 'resolution_no',
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'RESOLUTION NO.',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($model) {
                                $resolution = Resolution::find()->where(['id' => $model->resolution_no])->one();

                                if ($resolution == NULL) {
                                    return '-';
                                }
                                return $resolution->resolution_no;
                            },
                        ],
                        [
                            'attribute' => 'item_id',
                            'format' => 'ntext',
                            'value' => function ($model) {

                                $item = PrItems::find()->where(['id' => $model->item_id])->one();
                                return $item->item_name;
                            },
                            'options' => ['style' => 'width:25%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: left'],
                            'header' => 'ITEM DESCRIPTION',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'attribute' => 'supplier_id',
                            'value' => 'supplierdisplay.supplier_name',
                            'options' => ['style' => 'width:13%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'BIDDERS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'attribute' => 'quantity',
                            'options' => ['style' => 'width:5%'],
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
                            'attribute' => 'supplier_price',
                            'format' => [
                                'decimal', 2
                            ],
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: right'],
                            'header' => 'BID PRICE',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'attribute' => 'asssign_twg',
                            'value' => function ($model) {
                                $name = Profile::find()->where(['user_id' => $model->assign_twg])->one();
                                if (isset($model->userdisplay)) {
                                    return $name->fname . ' ' .  $name->lname;
                                }
                                return 'No TWG Assigned';
                            },
                            'options' => ['style' => 'width:13%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'ASSIGN-TWG',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],

                        [
                            'attribute' => 'status',
                            'value' => 'statusdisplay.status',
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        // [
                        //     'attribute' => 'item_remarks',
                        //     'format' => 'raw',
                        //     'header' => 'BID OFFER',
                        //     'value' => function ($model) {
                        //         return $model->item_remarks;
                        //     },
                        //     'options' => ['style' => 'width:10%'],
                        //     'hAlign' => 'center',
                        //     'contentOptions' => ['style' => 'text-align: center'],
                        //     'headerOptions' => ['style' => 'color:#337ab7'],
                        // ],
                        // [
                        //     'class' => 'kartik\grid\ActionColumn',
                        //     'header' => 'ACTIONS',
                        //     'headerOptions' => ['style' => 'color:#337ab7'],
                        //     'template' => '{genNum} {submit} ',
                        //     'buttons' => [
                        //         'submit' => function ($url, $model, $key) {

                        //             $test = PrItems::find()->where(['id' => $model->item_id])->one();

                        //             $enable = Html::button('<span class="glyphicon glyphicon-check"></span> Submit', ['class' => 'btn btn-info btn-sm submitBtn', 'value' => $model['id']]);

                        //             $disable = Html::a('<span class="glyphicon glyphicon-check"></span> Submit', ['bac-biddingreso-submit', 'id' => $model->id], ['<span class' => 'glyphicon glyphicon-check </span>', 'name' => 'submit', 'class' => 'btn btn-info btn-sm submitBtn', 'disabled' => true]);

                        //             if ($model->resolution_no == NULL) {
                        //                     return $disable;
                        //                 }
                        //             // if ($model->status != 16 || $test->status == 17) {
                        //             //     return $disable;
                        //             // }
                        //             // if ($test->status == 56) {
                        //             //     return $enable;
                        //             // }
                        //             return $enable;
                        //         },
                        //         'genNum' => function ($url, $model) {
                        //             $test = PrItems::find()->where(['id' => $model->item_id])->one();

                        //             return Html::button('Generate Resolution No.', ['value' => Url::to(['bidding/bac-resolution-generatenum', 'id' => $model->id]), 'class' => 'btn btn-warning btn-sm resoBtn']);
                        //         },
                        //     ],
                        //     'visibleButtons' => [
                        //         'genNum' => function ($model) {
                        //             if ($model['resolution_no'] != NULL) {
                        //                 return false;
                        //             }
                        //             return true;
                        //         },
                        //         'submit' => function ($model) {
                        //             if ($model['resolution_no'] != NULL) {
                        //                 return true;
                        //             }
                        //             return false;
                        //         },
                        //     ],
                        // ],


                        // ],
                        // 'visibleButtons' => [
                        // 'resopb' => function ($model) {
                        //     $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //     if (in_array($pr->mode_of_pr, ['4', '5'])) {
                        //         return false;
                        //     }
                        //     return true;
                        // },
                        //  'all' => function ($model) {
                        //     $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //     if (in_array($pr->mode_pr_id, ['4', '5'])) {
                        //         return false;
                        //     }
                        //     return true;
                        // },
                        // 'reso' => function ($model) {
                        //     $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        //     $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //     if ($pr['mode_of_pr'] == '1' || $pr['mode_of_pr'] == '2' || $model['resolution_no'] ==  NULL || $model['status'] != '16') {
                        //         return false;
                        //     }
                        //     return true;
                        // },
                        // 'allresosvp' => function ($model) {
                        //     $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        //     $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //     if ($pr['mode_pr_id'] == '1' || $pr['mode_pr_id'] == '2' || $model['resolution_no'] ==  NULL || $model['status'] != '16') {
                        //         return false;
                        //     }
                        //     return true;
                        // },

                        // ],

                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'RESOLUTION',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{lcrb}{wb}{reso}{reso-pdea}{reso-canvass}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'Generate PDF',
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'lcrb' => function ($url, $model) {
                                    return '<li>' . Html::a('LCRB Resolution', ['bidding/bac-biddingresolutionlcrb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'wb' => function ($url, $model) {
                                    return '<li>' . Html::a('Winning Bidder Resolution', ['bidding/bac-biddingresolutionpb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'reso' => function ($url, $model) {
                                    return '<li>' . Html::a('Resolution', ['bidding/bac-biddingresolution-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'reso-pdea' => function ($url, $model) {
                                    return '<li>' . Html::a('Resolution (PDEA)', ['bidding/bac-biddingresopdeapermit-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'reso-canvass' => function ($url, $model) {
                                    return '<li>' . Html::a('Resolution (CANVASS)', ['bidding/bac-biddingresocanvass-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                            ],
                            'visibleButtons' => [
                                'lcrb' => function ($model) {
                                    $item = PrItems::find()->where(['id' => $model->item_id])->one();
                                    $pr = PurchaseRequest::find()->where(['id' => $item['pr_id']])->one();

                                    if (in_array($pr->mode_pr_id, ['1', '3'])) {
                                        return '<li>' . Html::a('LCRB Resolution', ['bidding/bac-biddingresolutionlcrb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                    }
                                    if ($pr->mode_pr_id == 2) {
                                        return '<li>' . Html::a('LCRB Resolution', ['bidding/bac-biddingresolutionlcrbinfra-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                    }
                                },
                                'wb' => function ($model) {
                                    $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                                    if (in_array($pr->mode_pr_id, ['4', '5'])) {
                                        return false;
                                    }
                                    return true;
                                },
                                'reso' => function ($model) {
                                    $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                                    if (in_array($pr->mode_pr_id, ['4', '5'])) {
                                        return true;
                                    }
                                    return false;
                                },
                                'reso-pdea' => function ($model) {
                                    $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                                    if (in_array($pr->mode_pr_id, ['4'])) {
                                        return true;
                                    }
                                    return false;
                                },
                                'reso-canvass' => function ($model) {
                                    $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                                    if (in_array($pr->mode_pr_id, ['4', '5'])) {
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

$this->registerJs(
    <<<JS

//sweetalert 
  $('.submitBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to confirm?",
        text: "Note: This will be submit to Procurement.",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Awarded", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "https://procurement.itdi.ph/PurchaseRequest/bidding/bac-biddingreso-submit",
            type: "get",
            data: {
              sub: $(this).val()
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

    // $('.resoBtn').on("click", function(){
    //     $('#modal-reso').modal("show");

    //     $.get($(this).val(), function(data){
    //         $('#modalReso').html(data);
    //     });

    //     var selectedKeys = $('#resolutionTable').yiiGridView('getSelectedRows');
    //     var currentId = $(this).data('id');

    //     // Make another AJAX request with additional data
    //     $.get(
    //         $(this).val(),
    //         {
    //             id: currentId,
    //             keys: selectedKeys
    //         }, 
    //         function(data){
    //             // Update the content of the same modal ('modal-reso') or any other action needed
    //             $('#modalReso').html(data);
    //         }
    //     );
    // });

//     $('.resoBtn').on("click", function(){
//     // Disable the button to prevent multiple clicks
//     $(this).prop('disabled', true);

//     // Show the modal with id 'modal-reso'
//     $('#modal-reso').modal("show");

//     // Make an AJAX request using the URL from the 'value' attribute of the clicked button
//     $.get($(this).val(), function(data){
//         // Update the content of the modal with the received data
//         $('#modalReso').html(data);
//     })
 
//     // Retrieve additional data if needed (e.g., selected keys in the GridView)
//     var selectedKeys = $('#resolutionTable').yiiGridView('getSelectedRows');
//     var currentId = $(this).data('id');

//     // Make another AJAX request with additional data
//     $.get(
//         $(this).val(),
//         {
//             id: currentId,
//             keys: selectedKeys
//         }, 
//         function(data){
//             // Update the content of the same modal ('modal-reso') or any other action needed
//             $('#modalReso').html(data);
//         }
//     );
// });


$('.resoBtn').on("click", function(){
    $('#modal-reso').modal("show");
        var selectedKeys = $('#resolutionTable').yiiGridView('getSelectedRows');
        var currentId = $(this).data('id');
        $.get(
            $(this).val(),
        {
            id: currentId,
            keys: selectedKeys
        },
        function(data){
            $('#modalReso').html(data);
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