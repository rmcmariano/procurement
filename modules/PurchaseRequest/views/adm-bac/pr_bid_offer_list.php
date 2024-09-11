<?php

use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Quotation;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

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


<div class="bidoffer-list-index">
    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>For Bid Offer Lists:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <h1><?= Html::encode($this->title) ?></h1>

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
                        'attribute' => 'pr_id',
                        'format' => 'ntext',
                        'value' => function ($model) {
                            $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

                            return $pr->pr_no;
                        },
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'PR #',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],      
                    [
                        'attribute' => 'pr_id',
                        'format' => 'ntext',
                        'value' => function ($model) {
                            $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                            $solicitation = Quotation::find()->where(['pr_id' => $pr->id])->one();

                            return $solicitation->quotation_no;
                        },
                        'options' => ['style' => 'width:10%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'SOLICITATION #',
                        'headerOptions' => ['style' => 'color:#337ab7'],
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
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:10%'],
                        'header' => 'BID OFFER',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{viewOffer}',
                        'urlCreator' => function ($action, $model) {
                            
                            if ($action == 'viewOffer') {
                                return ['bidding/bac-bidding-offer-create', 'id' => $model->id,];
                            }
                        },
                        'buttons' => [
                          
                            'viewOffer' => function ($url, $model, $key) {
                    
                                echo '<div class="modal fade" id="modal-bidviewOffer-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modal-bidviewOffer-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>';
    
                                $script = <<< JS
                                    $(document).on('click', '[data-toggle="modal"][data-target="#modal-bidviewOffer-$model->id"]', function() {
                                        var modal = $('#modal-bidviewOffer-$model->id');
                                        var url = $(this).data('url');
    
                                        modal.find('.modal-body').load(url);
                                    });
                                JS;
    
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span> View ', $url, [
                                    // 'class' => 'btn btn-success btn-sm',
                                    // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                    'title' => 'Add Details',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-bidOffer-' . $model->id,
                                    'data-url' => Url::to(['bidding/bac-bidding-offer-create', 'id' => $model->id,])
    
                                ]);
                            },
                        ],
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
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:20%'],
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => ' {bidoffer}',
                        'urlCreator' => function ($action, $model) {
                            if ($action == 'bidoffer') {
                                return ['bidding/bac-bidding-offer-create', 'id' => $model->id,];
                            }
                        },
                        'buttons' => [
                            'bidoffer' => function ($url, $model, $key) {
                    
                                echo '<div class="modal fade" id="modal-bidOffer-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modal-bidOffer-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                                <div class="modal-dialog modal-lg" role="document">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>';
    
                                $script = <<< JS
                                    $(document).on('click', '[data-toggle="modal"][data-target="#modal-bidOffer-$model->id"]', function() {
                                        var modal = $('#modal-bidOffer-$model->id');
                                        var url = $(this).data('url');
    
                                        modal.find('.modal-body').load(url);
                                    });
                                JS;
    
                                return Html::a('<span class="glyphicon glyphicon-plus-sign"></span> Add Bid Offer ', $url, [
                                    // 'class' => 'btn btn-success btn-sm',
                                    // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                    'title' => 'Add Details',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal-bidOffer-' . $model->id,
                                    'data-url' => Url::to(['bidding/bac-bidding-offer-create', 'id' => $model->id,])
    
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
    </p>
</div>



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