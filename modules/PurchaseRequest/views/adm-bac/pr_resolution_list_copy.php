<?php

use app\modules\PurchaseRequest\models\PrItems;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

Modal::begin([
    'header' => 'Generate Resolution No.',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-reso',
    'size' => 'modal-sm',
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
                // 'visible' => in_array($purchaserequest->mode_pr_id, ['4', '5', '6', '7', '8', '9', '10']),
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
                'active' => true,
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
                <h3>For Resolution Lists:</h3>
            </i>

            <!-- <php
            echo (in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19']) ? Html::button('<span class="glyphicon glyphicon-plus"></span> Submit', ['value' => Url::to(['purchase-order/ppms-purchaseorder-create?id=' . $_GET['id']]), 'data-id' => Yii::$app->request->get('id'), 'class' => 'btn btn-success modalOrder']) . ' ' : Html::button('<span class="glyphicon glyphicon-plus"></span> Create New Purchase Order', ['value' => Url::to(['purchase-order/ppms-purchaseorder-create?id=' . $_GET['id']]), 'data-id' => Yii::$app->request->get('id'), 'class' => 'btn btn-success modalOrder']) . ' ');
            ?> -->

            <!-- <left>
                <= Html::button(' Submit', ['value' => Url::to(['bac-biddingreso-submit', 'id' => $_GET['id']]), 'class' => 'btn btn-info submitBtn']); ?> &nbsp;
            </left> -->
            <p>

                <?= GridView::widget([
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
                            'class' => 'kartik\grid\CheckboxColumn',
                            'headerOptions' => ['class' => 'kartik-sheet-style'],
                            'header' => Html::checkBox('selection_all', false, [
                                'class' => 'select-on-check-all',
                                'label' => false,
                            ]),
                            'options' => ['style' => 'width:3%'],
                        ],
                        [
                            'attribute' => 'resolution_no',
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'RESOLUTION NO.',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'attribute' => 'item_id',
                            'format' => 'ntext',
                            'value' => function ($model) {

                                $item = PrItems::find()->where(['id' => $model->item_id])->one();

                                // if ($model->item_remarks == $item->id) {
                                //     return $item->item_name;
                                // }
                                // return $model->item_remarks;
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
                            'attribute' => 'status',
                            'value' => 'statusdisplay.status',
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'attribute' => 'item_remarks',
                            'format' => 'raw',
                            'header' => 'BID OFFER',
                            'value' => function ($model) {
                                return $model->item_remarks;
                            },
                            // 'value' => function ($model) {
                            //     $bidOffer = Html::a('Bid Offer', 'javascript:void(0);', [
                            //         'title' => Yii::t('app', 'Bid Offer'),
                            //         'class' => 'btn btn-warning btn-sm',
                            //         'data-toggle' => 'modal',
                            //         // 'data-target' => '#viewModal',
                            //         // 'data-url' => Url::to(['view', 'id' => $model->id]),
                            //     ]);
                            //     if ($model->item_remarks == NULL) {
                            //         if (Yii::$app->user->identity->id == 553) {
                            //             return $bidOffer;
                            //         }
                            //         return '';
                            //     }
                            // },
                            'options' => ['style' => 'width:10%'],
                            'hAlign' => 'center',
                            'contentOptions' => ['style' => 'text-align: center'],
                            'headerOptions' => ['style' => 'color:#337ab7'],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'ACTIONS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{submit} ',
                            'buttons' => [
                                'submit' => function ($url, $model, $key) {

                                    $test = PrItems::find()->where(['id' => $model->item_id])->one();

                                    $enable = Html::button('<span class="glyphicon glyphicon-check"></span> Submit', ['class' => 'btn btn-info btn-sm submitBtn', 'value' => $model['id']]);

                                    $disable = Html::a('<span class="glyphicon glyphicon-check"></span> Submit', ['bac-biddingreso-submit', 'id' => $model->id], ['<span class' => 'glyphicon glyphicon-check </span>', 'name' => 'submit', 'class' => 'btn btn-info btn-sm submitBtn', 'disabled' => true]);

                                    if ($model->resolution_no == NULL) {
                                        return $disable;
                                    }
                                    // if ($model->status != 16 || $test->status == 17) {
                                    //     return $disable;
                                    // }
                                    // if ($test->status == 56) {
                                    //     return $enable;
                                    // }
                                    return $enable;
                                },
                                // 'genNum' => function ($url, $model) {
                                //     $test = PrItems::find()->where(['id' => $model->item_id])->one();

                                //     return Html::button('Generate Resolution No.', ['value' => Url::to(['bidding/bac-resolution-generatenum', 'id' => $model->id]), 'class' => 'btn btn-warning btn-sm resoBtn']);
                                // },
                                // // 'reso' => function ($url, $model) {
                                // //     $test = PrItems::find()->where(['id' => $model->item_id])->one();

                                // //     return Html::a('<span class="glyphicon glyphicon-print"></span> Resolution',  ['bidding/bac-biddingresolution-pdf', 'id' => $model->id], ['target' => '_blank', 'class' => 'btn btn-sm btn-default']);
                                // // },
                            ],
                            'visibleButtons' => [
                                'genNum' => function ($model) {
                                    if ($model['resolution_no'] != NULL) {
                                        return false;
                                    }
                                    return true;
                                },
                                'submit' => function ($model) {
                                    if ($model['resolution_no'] != NULL) {
                                        return true;
                                    }
                                    return false;
                                },
                            ],
                        ],

                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'NOTICES',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{ntp}{noa}{nopq}{nolcb}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'Generate PDF',
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'ntp' => function ($url, $model) {

                                    return '<li>' . Html::a('Notice to Proceed', ['bidding/bac-ntp-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'noa' => function ($url, $model) {
                                    return '<li>' . Html::a('Notice of Award', ['bidding/bac-noa-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'nopq' => function ($url, $model) {
                                    return '<li>' . Html::a('Notce of Post Qualification', ['bidding/bac-nopq-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                                'nolcb' => function ($url, $model) {
                                    return '<li>' . Html::a('Notice of LCB', ['bidding/bac-nolcb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                },
                            ],
                        ],

                        // [
                        //     'class' => 'kartik\grid\ActionColumn',
                        //     'header' => 'RESOLUTION',
                        //     'headerOptions' => ['style' => 'color:#337ab7'],
                        //     'template' => '{lcrb}{wb}{reso}{reso-pdea}{reso-canvass}',
                        //     'dropdown' => true,
                        //     'dropdownButton' => [
                        //         'label' =>  'Generate PDF',
                        //     ],
                        //     'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                        //     'buttons' => [
                        //         'lcrb' => function ($url, $model) {
                        //             return '<li>' . Html::a('LCRB Resolution', ['bidding/bac-biddingresolutionlcrb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //         },
                        //         'wb' => function ($url, $model) {
                        //             return '<li>' . Html::a('Winning Bidder Resolution', ['bidding/bac-biddingresolutionpb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //         },
                        //         'reso' => function ($url, $model) {
                        //             return '<li>' . Html::a('Resolution', ['bidding/bac-biddingresolution-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //         },
                        //         'reso-pdea' => function ($url, $model) {
                        //             return '<li>' . Html::a('Resolution (PDEA)', ['bidding/bac-biddingresopdeapermit-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //         },
                        //         'reso-canvass' => function ($url, $model) {
                        //             return '<li>' . Html::a('Resolution (CANVASS)', ['bidding/bac-biddingresocanvass-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //         },
                        //     ],
                        //     'visibleButtons' => [
                        //         'lcrb' => function ($model) {
                        //             $item = PrItems::find()->where(['id' => $model->item_id])->one();
                        //             $pr = PurchaseRequest::find()->where(['id' => $item['pr_id']])->one();

                        //             if (in_array($pr->mode_pr_id, ['1', '3'])) {
                        //                 return '<li>' . Html::a('LCRB Resolution', ['bidding/bac-biddingresolutionlcrb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //             }
                        //             if ($pr->mode_pr_id == 2) {
                        //                 return '<li>' . Html::a('LCRB Resolution', ['bidding/bac-biddingresolutionlcrbinfra-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                        //             }
                        //         },
                        //         'wb' => function ($model) {
                        //             $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //             if (in_array($pr->mode_pr_id, ['4', '5'])) {
                        //                 return false;
                        //             }
                        //             return true;
                        //         },
                        //         'reso' => function ($model) {
                        //             $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //             if (in_array($pr->mode_pr_id, ['4', '5'])) {
                        //                 return true;
                        //             }
                        //             return false;
                        //         },
                        //         'reso-pdea' => function ($model) {
                        //             $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //             if (in_array($pr->mode_pr_id, ['4', '5'])) {
                        //                 return true;
                        //             }
                        //             return false;
                        //         },
                        //         'reso-canvass' => function ($model) {
                        //             $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                        //             if (in_array($pr->mode_pr_id, ['4', '5'])) {
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

  $('.resoBtn').on("click", function(){
    $('#modal-reso').modal("show");
        $.get($(this).val(), function(data){
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