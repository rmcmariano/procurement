<?php

use app\modules\PurchaseRequest\models\DeliveryAttachments;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;


// Modal::begin([
//     'header' => 'Delivery Attachments',
//     'id' => 'modalfile',
//     'size' => 'modal-lg',
//     'options' => [
//         'data-keyboard' => 'false',
//         'data-backdrop' => 'static'
//     ]
// ]);
// echo "<div id = 'modalFile2'></div>";
// Modal::end();

?>
<div class="pr-subdata-index">
    <div id="ajaxCrudDatatable">

        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'options' => ['style' => 'width:100%'],
            'columns' => [

                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:5%'],
                ],
                [
                    'attribute' => 'actual_date_delivery',
                    'options' => ['style' => 'width:20%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'ACTUAL DATE DELIVERY',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime(strtotime($model->actual_date_delivery), 'php:d-M-Y | h:i');
                    },
                ],
                [
                    'attribute' => 'type_delivery',
                    'options' => ['style' => 'width:30%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'TYPE OF DELIVERY',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'value' => function ($model) {
                        if ($model->type_delivery == 'b') {
                            return 'Partial Delivery';
                        }
                        if ($model->type_delivery == 'c') {
                            return 'Complete Delivery';
                        }
                    }
                ],
                // [
                //     'attribute' => 'delivery_receipt_no',
                //     'options' => ['style' => 'width:10%'],
                //     'hAlign' => 'center',
                //     'contentOptions' => ['style' => 'text-align: center'],
                //     'header' => 'D.R. NUMBER',
                //     'headerOptions' => ['style' => 'color:#337ab7'],
                // ],
                // [
                //     'attribute' => 'delivery_amount',
                //     'options' => ['style' => 'width:10%'],
                //     'hAlign' => 'center',
                //     'contentOptions' => ['style' => 'text-align: right'],
                //     'header' => 'D.R. AMOUNT',
                //     'headerOptions' => ['style' => 'color:#337ab7'],
                //     'format' => [
                //         'decimal', 2
                //     ],  
                // ],
                [
                    'attribute' => 'remarks',
                    'options' => ['style' => 'width:30%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'REMARKS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'options' => ['style' => 'width:20%'],
                    'header' => 'ACTIONS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => ' {files}',
                    'urlCreator' => function ($action, $model) {
                        if ($action == 'files') {
                            return ['purchase-order/deliveryattachments-create', 'id' => $model->id,];
                        }
                    },
                    'buttons' => [
                        'files' => function ($url, $model, $key) {

                            echo '<div class="modal fade" id="modalFile-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modalFile-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                    </div>
                                </div>
                            </div>
                        </div>';

                            $script = <<< JS
                                $(document).on('click', '[data-toggle="modal"][data-target="#modalFile-$model->id"]', function() {
                                    var modal = $('#modalFile-$model->id');
                                    var url = $(this).data('url');

                                    modal.find('.modal-body').load(url);
                                });
                            JS;

                            // var_dump($model);die;
                            return Html::a('<span class="glyphicon glyphicon-upload"></span> File Upload ', $url, [
                                'class' => 'btn btn-default btn-sm',
                                // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                'title' => 'File Upload',
                                'data-toggle' => 'modal',
                                'data-target' => '#modalFile-' . $model->id,
                                'data-url' => Url::to(['purchase-order/deliveryattachments-create', 'id' => $model->id,])

                            ]);
                        },
                    ],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => 'PDF',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => '{pdf}',
                    'buttons' => [
                        'pdf' => function ($url, $model, $key) {
                            $delAttach = DeliveryAttachments::find()->where(['delivery_id' => $model->id])->one();
                            if ($delAttach == NULL) {
                                return '';
                            }
                            $filename = explode('/', $delAttach->file_directory);

                            if ($delAttach->file_extension == 'pdf') {
                                return '<button id ="pdf-details" class="btn btn-primary btn-sm pdf-details" target="_blank" href="http://localhost/itdi-purchase-request/web/uploads/pr_delivery_files/' . $filename[2] . '"> View PDF </button>';
                            }
                            if ($delAttach->file_extension == 'docx') {
                                return '<button id ="file-download" class="btn btn-primary btn-sm file-download" data-id= "' . $delAttach->id . '"> Download </button>';
                            }
                            if ($delAttach->file_extension == 'jpg' || $delAttach->file_extension == 'png') {
                                return '<a href="#" class="pop img-hover-zoom--brightness"> <img style= "width:50px; height:50px;" src="http://localhost/itdi-purchase-request/web/uploads/pr_delivery_files/' . $filename[2] . '"></a>';
                            }
                            
                        },
                    ],
                ],
                // [
                //     'class' => 'kartik\grid\ActionColumn',
                //     'header' => 'Actions',
                //     'headerOptions' => ['style' => 'color:#337ab7'],
                //     'template' => '{files}',
                //     'buttons' => [
                //         'files' => function ($url, $model, $key) {
                //             return Html::button('<span class="glyphicon glyphicon-upload"> </span> File Uploads', ['value' => Url::to(['purchase-order/deliveryattachments-create', 'id' => $model->id,]),  'class' => 'btn btn-default modalfilebtn']);
                //         },
                //     ],
                // ],
            
            ]
        ]);
        ?>
    </div>
</div>

<style>
    .modal-content {
        border-radius: 20px;
    }
</style>
