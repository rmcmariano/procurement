<?php

use app\modules\PurchaseRequest\models\PurchaseRequest;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;
use kartik\form\ActiveForm;

$this->params['breadcrumbs'][] = ['label' => 'Home', 'url' => ['bac-prindex']];

Modal::begin([
    'header' => 'SCHEDULING DETAILS',
    'id' => 'modal-create',
    'headerOptions' => ['class' => 'bg-info'],
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);
echo "<div id = 'modalCreate'></div>";
Modal::end();

Modal::begin([
    'header' => 'ADD SCHEDULE DETAILS',
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
    'id' => 'modal-rfq',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);
echo "<div id = 'modalRequest'></div>";
Modal::end();



Modal::begin([
    'header' => 'FILL UP DETAILS FOR INVITATION TO BID FORMS',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-bid2',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalBid2'></div>";
Modal::end();

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
                'active' => true,
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
            ],
            [
                'label' => 'RESOLUTION',
                'url' => ['bidding/bac-resolutionlist', 'id' => $purchaserequest->id],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<div class="quotation-index">
    <?php $form = ActiveForm::begin(); ?>
    </p>
    <div class="panel panel-default">
        <div style="padding: 20px">
            <left>
                <i>
                    <h5>Purchase Request Number:</h5>
                </i>
                <h1><?= $purchaserequest->pr_no ?></h1>
            </left>
            <left>
                <h5><i> Solicitation Number:</i></h5>
                <h1> <?= (isset($quotation->quotation_no) ? $quotation->quotation_no : '') ?></h1>
            </left>

            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <div>
                <?php
                echo ($quotation == NULL ? Html::button('Generate Solicitaion #', ['value' => Url::to(['/PurchaseRequest/purchase-request/bac-quotationcreate', 'id' => $purchaserequest->id,]),  'class' => 'btn btn-primary btn-sm modalCreatebtn']) : Html::button('Add Schedule Details', ['value' => Url::to(['/PurchaseRequest/purchase-request/bac-quotationcreate', 'id' => $purchaserequest->id,]),  'class' => 'btn btn-primary btn-sm modalCreatebtn'])) . ' ';

                echo ($quotation == NULL ? Html::button('Generate Solicitation #', ['value' => Url::to(['/PurchaseRequest/purchase-request/bac-quotationcreate', 'id' => $purchaserequest->id,]),  'class' => 'btn btn-primary btn-sm modalCreatebtn']) : Html::button('Add Schedule Details', ['value' => Url::to(['/PurchaseRequest/purchase-request/bac-quotationcreate', 'id' => $purchaserequest->id,]),  'class' => 'btn btn-primary btn-sm modalCreatebtn'])) . ' ';

                echo (Html::button('Request revision', ['class' => 'btn btn-warning btn-sm revisionBtn', 'value' =>  $purchaserequest["id"]]) . ' ');
                ?>
            </div>

            <i>
                <h3>Scheduling Details:</h3>
            </i>
            <?= GridView::widget([
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
                'rowOptions' => function ($url) {
                    if (in_array($url->status, ['18'])) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [
                    [
                        'attribute' => 'option_id',
                        'value' => 'optionsdisplay.options',
                        'options' => ['style' => 'width:20%'],
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'DETAILS',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'filter' => true
                    ],
                    [
                        'attribute' => 'option_date',
                        'value' => function ($model) {

                            return Yii::$app->formatter->asDatetime(strtotime($model->option_date), 'php:Y-M-d | h:i A');
                        },
                        'options' => ['style' => 'width:10%', 'id' => 'optionDate'],
                        'hAlign' => 'left',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'DATE',
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
                        'attribute' => 'remarks',
                        'options' => ['style' => 'width:20%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'REMARKS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:30%'],
                        'header' => '',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' =>  '<div style="text-align:left;">{remarks} {post} {view}  {repost}</div> ',
                        'urlCreator' => function ($action, $model) {
                            if ($action == 'post') {
                                return ['purchase-request/bac-quotation-philgepscreate', 'id' => $model->id,];
                            }
                            if ($action == 'rfq') {
                                return ['purchase-request/bac-quotation-rfqcreate', 'id' => $model->id,];
                            }
                            if ($action == 'remarks') {
                                return ['purchase-request/bac-quotation-remarks-update', 'id' => $model->id,];
                            }
                        },
                        'buttons' => [
                            'post' => function ($url, $model, $key) {

                                echo '<div class="modal fade" id="modalReferenceCreate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalReferenceCreate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-xs" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>';

                                $script = <<< JS
                                    $(document).on('click', '[data-toggle="modal"][data-target="#modalReferenceCreate-$model->id"]', function() {
                                        var modal = $('#modalReferenceCreate-$model->id');
                                        var url = $(this).data('url');
    
                                        modal.find('.modal-body').load(url);
                                    });
                                JS;

                                return Html::a('<span class="glyphicon glyphicon-pencil"></span> Reference Number', $url, [
                                    'class' => 'btn btn-info btn-xs',
                                    // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                    'title' => 'PhilGeps Reference #',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalReferenceCreate-' . $model->id,
                                    'data-url' => Url::to(['PurchaseRequest/purchase-request/bac-quotation-philgepscreate', 'id' => $model->id,])

                                ]);
                            },
                            'remarks' => function ($url, $model, $key) {

                                echo '<div class="modal fade" id="modalRemarksCreate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalRemarksCreate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-xs" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>';

                                $script = <<< JS
                                    $(document).on('click', '[data-toggle="modal"][data-target="#modalRemarksCreate-$model->id"]', function() {
                                        var modal = $('#modalRemarksCreate-$model->id');
                                        var url = $(this).data('url');
    
                                        modal.find('.modal-body').load(url);
                                    });
                                JS;

                                return Html::a('<span class="glyphicon glyphicon-pencil"></span> Add Remarks', $url, [
                                    'class' => 'btn btn-warning btn-xs',
                                    // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                    'title' => 'Remarks Update',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalRemarksCreate-' . $model->id,
                                    'data-url' => Url::to(['PurchaseRequest/purchase-request/bac-quotation-remarks-update', 'id' => $model->id,])

                                ]);
                            },
                            'rfq' => function ($url, $model, $key) {
                                echo '<div class="modal fade" id="modalRfqCreate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalRfqCreate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <div class="modal-body">
                                            </div>
                                        </div>
                                    </div>
                                </div>';

                                $script = <<< JS
                                        $(document).on('click', '[data-toggle="modal"][data-target="#modalRfqCreate-$model->id"]', function() {
                                            var modal = $('#modalRfqCreate-$model->id');
                                            var url = $(this).data('url');

                                            modal.find('.modal-body').load(url);
                                        });
                                    JS;

                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Canvass Form', $url, [
                                    'class' => 'btn btn-success btn-xs',
                                    'title' => 'Canvass Form PDF',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalRfqCreate-' . $model->id,
                                    'data-url' => Url::to(['PurchaseRequest/purchase-request/bac-quotation-rfqcreate', 'id' => $model->id,])

                                ]);
                            },

                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-print"></span> Generate RFQ', ['purchase-request/bac-quotation-rfq-pdf', 'id' => $model->id], ['class' => 'btn btn-default btn-xs', 'target' => 'blank', 'data-pjax' => 0]);
                            },

                            'repost' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-print"></span> Generate RFQ', ['purchase-request/bac-quotation-rfq-repost-pdf', 'id' => $model->id], ['class' => 'btn btn-default btn-xs', 'target' => 'blank', 'data-pjax' => 0]);
                            },
                        ],
                        'visibleButtons' => [
                            'post' => function ($model) {
                                if ($model['option_id'] == 2 || $model['option_id'] == 7) {
                                    return true;
                                }
                                return false;
                            },
                            // 'rfq' => function ($model) {
                            //     if ($model['option_id'] == 4 && $model['status'] == NULL || $model['option_id'] == 9) {
                            //         return true;
                            //     }
                            //     return false;
                            // },

                            'view' => function ($model) {
                                if ($model['status'] == 1 && $model['option_id'] == 4) {
                                    return true;
                                }
                                return false;
                            },
                            'repost' => function ($model) {
                                if ($model['option_id'] == 6 ) {
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
</div>


<?php
$hideDiv = false;

if ($purchaserequest->mode_pr_id >= 4) {
    $divAttributes = ['style' => 'display: none;'];
} else {
    $divAttributes = [];
}
?>

<div <?= \yii\helpers\Html::renderTagAttributes($divAttributes) ?>>
    <div class="panel panel-default" style="padding:20px">
        <i>
            <h3>BID Document Details:</h3>
        </i>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
        <p>
            <left>
                <h5 style="color:red;"><i>Note: Always set the schedule details first before you generate the PDF Documents</i></h5>
            </left>

            <?= GridView::widget([
                'id' => 'quotationTable',
                'dataProvider' => $dataProvider2,
                'responsive' => false,
                'tableOptions' => ['style' => 'overflow-y: visible !important;'],
                'export' => false,
                'panel' => ['type' => 'info',],
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width:3%'],
                    ],
                    [
                        'attribute' => 'item_name',
                        'format' => 'ntext',
                        'options' => ['style' => 'width:30%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'ITEM DESCRIPTION',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'total_cost',
                        'format' => [
                            'decimal', 2
                        ],
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'TOTAL COST',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'bid_title',
                        'format' => 'ntext',
                        'options' => ['style' => 'width:30%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center; text-transform: uppercase'],
                        'header' => 'BID TITLE',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            if ($model->bid_title == NULL) {
                                return '-';
                            }
                            return $model->bid_title;
                        }
                    ],
                    [
                        'attribute' => 'bidding_docs_fee',
                        'format' => [
                            'decimal', 2
                        ],
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'BID DOCUMENTS FEE',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'value' => function ($model) {
                            if ($model->bidding_docs_fee == NULL) {
                                return '0';
                            }
                            return $model->bidding_docs_fee;
                        }
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:10%'],
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{createItemDetails}',
                        'urlCreator' => function ($action, $model) {
                            if ($action == 'createItemDetails') {
                                return ['bidding/bac-biddingitemdetails-create', 'id' => $model->id,];
                            }
                        },
                        'buttons' => [
                            'createItemDetails' => function ($url, $model, $key) {
                                echo '<div class="modal fade" id="modalItemDetails-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalItemDetails-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>';

                                $script = <<< JS
                                $(document).on('click', '[data-toggle="modal"][data-target="#modalItemDetails-$model->id"]', function() {
                                    var modal = $('#modalItemDetails-$model->id');
                                    var url = $(this).data('url');

                                    modal.find('.modal-body').load(url);
                                });
                            JS;

                                return Html::a('<span class="glyphicon glyphicon-pencil"></span> Update Details', $url, [
                                    'class' => 'btn btn-success btn-xs',
                                    // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                    'title' => 'Update Details',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modalItemDetails-' . $model->id,
                                    'data-url' => Url::to(['PurchaseRequest/bidding/bac-biddingitemdetails-create', 'id' => $model->id,])

                                ]);
                            }
                        ],
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'header' => 'BID DOCUMENTS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{invitation}{technical_spec}{biddata_sheet}{scc}{financial}{checklist_page1}{checklist_page2}',
                        'dropdown' => true,
                        'dropdownButton' => [
                            'label' =>  'Generate PDF',
                        ],
                        'dropdownOptions' => ['class' => 'pull-right dropup',  'data-boundary' => "viewport"],
                        'buttons' => [
                            'invitation' => function ($url, $model) {
                                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

                                if ($pr->mode_pr_id == 1) {
                                    return '<li>' . Html::a('Invitation BID', ['bidding/bac-invitationtobidpb-pdf', 'id' => $model['id']], ['class' => 'invitationBtn', 'target' => 'blank']) . '</li>';
                                }

                                if ($pr->mode_pr_id == 2) {
                                    return '<li>' . Html::a('Invitation BID', ['bidding/bac-invitationtobidinfra-pdf', 'id' => $model['id']], ['class' => 'invitationBtn', 'target' => 'blank']) . '</li>';
                                }
                            },
                            'technical_spec' => function ($url, $model) {

                                return '<li>' . Html::a('Technical Specification', ['bidding/bac-techspecificationpb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                            },
                            'biddata_sheet' => function ($url, $model) {
                                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

                                if ($pr->mode_pr_id == 1) {
                                    return '<li>' . Html::a('BID Data Sheet', ['bidding/bac-biddatasheetpb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }

                                if ($pr->mode_pr_id == 2) {
                                    return '<li>' . Html::a('BID Data Sheet', ['bidding/bac-biddatasheetinfra-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }
                            },
                            'scc' => function ($url, $model) {
                                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

                                if ($pr->mode_pr_id == 1) {
                                    return '<li>' . Html::a('Special Condition of Contract', ['bidding/bac-specialconditionpb-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }

                                if ($pr->mode_pr_id == 2) {
                                    return '<li>' . Html::a('Special Condition of Contract', ['bidding/bac-specialconditioninfra-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }
                            },
                            'financial' => function ($url, $model) {

                                return '<li>' . Html::a('Financial Envelope', ['bidding/bac-financialenvelope-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                            },
                            'checklist_page1' => function ($url, $model) {

                                return '<li>' . Html::a('Checklist PB pg 1', ['bidding/bac-technicalrequirements-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                            },
                            'checklist_page2' => function ($url, $model) {

                                return '<li>' . Html::a('Checklist PB pg 2', ['bidding/bac-technicalrequirements2-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                            },
                        ],
                        'visibleButtons' => [
                            'technical_spec' => function ($model) {
                                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

                                if ($pr->mode_pr_id == 2) {
                                    return false;
                                }
                                return true;
                            },
                        ],
                    ],
                ]
            ]);
            ?>
        </p>
    </div>
    <?php ActiveForm::end(); ?>
</div>




<?php
$this->registerJsVar('Cancel', Url::to(['purchase-request/bac-revisionrequest']));
$this->registerJsVar('Reference', Url::to(['purchase-request/bac-quotation-philgepscreate']));

$this->registerJs(
    <<<JS

$('.revisionBtn').on('click', function() {
    var remarks = "";
    var id = $(this).val(); // Store the value of $(this).val() in a variable

    swal({
        title: "Request for revision?",
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
                    url: "/procurement/web/PurchaseRequest/purchase-request/bac-revisionrequest",
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


    $('.modalCreatebtn').on("click", function(){
        $('#modal-create').modal("show");
        $.get($(this).val(), function(data){
            $('#modalCreate').html(data);
        });
    });

    $(".invitationBtn").click(function(event){
        if ($("#optionDate").val() == " ") {
        alert('Please make sure to complete all scheduling details...');
        event.preventDefault();
    }
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

    .modal-content {
        border-radius: 20px;
    }
</style>