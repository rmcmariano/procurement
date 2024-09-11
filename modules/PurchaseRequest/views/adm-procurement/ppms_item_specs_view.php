<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\PurchaseOrder;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;


Modal::begin([
    'header' => 'Bid Bulletin',
    'id' => 'modal-iarcreate',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalIarcreate'></div>";
Modal::end();

?>


<div class="modal-header design">
    <h5 class="modal-title" id="modal-bulletin-label"> View
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h5>
</div>

<div class="pr-itemspecs">
    <div class="box-header with-border">
        <h4 style="text-align: left"><i> Item Name: </i> &nbsp;&nbsp; <strong><?= ($modelBidding->prItemsdisplay->item_name); ?></strong> </h4>
        <h4 style="text-align: left"><i> Amount: </i> &nbsp;&nbsp; <strong> P <?= number_format($modelBidding->prItemsdisplay->quantity * $modelBidding->supplier_price, '2'); ?></strong> </h4>

        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

        <?= GridView::widget([
            'id' => 'itemspecs-create-grid',
            'dataProvider' => $dataProvider,
            'showFooter' => true,
            'options' => [
                'style' => 'overflow: auto; word-wrap: break-word;'
            ],

            'showPageSummary' => true,
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:2%'],
                ],
                [
                    'attribute' => 'description',
                    'header' => 'ITEMS SPECIFICATION',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:50%'],
                    'hAlign' => 'left',
                    'value' => function ($model) {
                        //   var_dump($model);die;
                        return $model->description;
                    }
                ],
                [
                    'attribute' => 'quantity',
                    'header' => 'QUANTITY',
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:10%; white-space: pre-line'],
                ],
                [
                    'attribute' => 'property_no',
                    'header' => 'PROPERTY NO.',
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:20%; white-space: pre-line'],
                ],
                [
                    'attribute' => 'po_status',
                    'value' => function ($model) {
                     
                        $bidding = BiddingList::find()->where(['item_id' => $model->item_id])->one();
                        $po = PurchaseOrder::find()->where(['id' => $bidding->po_id])->one();
                  
                        if ($po == NULL) {
                            return '-';
                        }

                        if ($po->po_status == NULL) {
                            return '-';
                        }
                        // created PO
                        if ($po->po_status == 1) {
                            return 'CREATED';
                        }
                        // after the approval of PPMS and submitted to budget
                        if ($po->po_status == 2) {
                            return 'FOR OBLIGATION';
                        }
                        // declined by PPMS
                        if ($po->po_status == 3) {
                            return 'DECLINED';
                        }
                        // obligated by FMD-budget
                        if ($po->po_status == 4) {
                            return 'OBLIGATED';
                        }
                        // validated by FMD-accounting
                        if ($po->po_status == 5) {
                            return 'VALIDATED';
                        }
                        // for supplier's conforme
                        if ($po->po_status == 6) {
                            return 'FOR CONFORME';
                        }
                        // for supplier's conforme
                        if ($po->po_status == 7) {
                            return 'FOR DELIVERY';
                        }
                        // for delivery validated
                        if ($po->po_status == 8) {
                            return 'DELIVERY VALIDATED';
                        }
                          // for delivery validated
                          if ($po->po_status == 9) {
                            return 'Approval of End User for IAR Changes';
                        }
                    },
                    'options' => ['style' => 'width:15%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: center'],
                    'header' => 'STATUS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                // [
                //     'class' => 'kartik\grid\ActionColumn',
                //     'header' => 'Actions',
                //     'headerOptions' => ['style' => 'color:#337ab7'],
                //     'template' => '{cancel} ',
                //     'options' => ['style' => 'width:10%'],
                //     'template' => ' {requestChanges} ',
                //     'urlCreator' => function ($action, $model) {
                //         if ($action == 'requestChanges') {
                //             return ['inspection-acceptance-report/requestchanges', 'id' => $model->id,];
                //         }
                //     },
                //     'buttons' => [
                //         'requestChanges' => function ($url, $model, $key) {
                //             echo '<div class="modal fade" id="modal-bulletin-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modal-bulletin-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                //             <div class="modal-dialog" role="document">
                //                 <div class="modal-content">
                //                     <div class="modal-body">
                //                     </div>
                //                 </div>
                //             </div>
                //         </div>';

                //             $script = <<< JS
                //                 $(document).on('click', '[data-toggle="modal"][data-target="#modal-bulletin-$model->id"]', function() {
                //                     var modal = $('#modal-bulletin-$model->id');
                //                     var url = $(this).data('url');
                    
                //                     modal.find('.modal-body').load(url);
                //                 });
                //             JS;

                //             // var_dump($model);die;
                //             return Html::a('<span class="glyphicon glyphicon-pencil"></span> Request Changes ', $url, [
                //                 'class' => 'btn btn-warning btn-xs',
                //                 // 'id' => 'modalBidbulletinbtn-' . $model->id,
                //                 'title' => 'Add Details',
                //                 'data-toggle' => 'modal',
                //                 'data-target' => '#modal-bulletin-' . $model->id,
                //                 'data-url' => Url::to(['inspection-acceptance-report/requestchanges', 'id' => $model->id,])

                //             ]);
                //         },
                //     ],
                // ],
            ]
        ]);
        ?>
    </div>
</div>


<?php
$this->registerJs(
    <<<JS

    $('.modalIarcreateBtn').on("click", function(){
        $('#modal-iarcreate').modal("show");
        $.get($(this).val(), function(data){
            $('#modalIarcreate').html(data);
        });
    });

JS
);
?>

<style>
    .pr-itemspecs {
        padding-bottom: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background-color: white;
        display: inline-block;
        width: 100%;
    }

    .design {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background-color: #12B359;
    }
</style>