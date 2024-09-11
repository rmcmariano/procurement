<?php

use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="itemspecification-view">
    <div id="crud">

        <?= GridView::widget([
            'id' => 'items-process-datatable',
            'dataProvider' => $dataProvider,
            'options' => [
                'style' => 'overflow: auto; word-wrap: break-word;'
            ],
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:2%'],
                ],

                [
                    'attribute' => 'description',
                    'header' => 'ITEM SPECIFICATION',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:35%;'],
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'bidbulletin_changes',
                    'header' => 'DETAILS FOR BID BULLETIN',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:35%; white-space: pre-line'],
                    'value' => function ($model) {
                        if ($model->bidbulletin_changes == NULL) {
                            return '-';
                        }
                        return $model->bidbulletin_changes;
                    },
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'bidbulletin_status',
                    'header' => 'BID BULLETIN STATUS',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'right',
                    'contentOptions' => ['style' => 'text-align: center'],
                    'value' => function ($model) {
                        if ($model->bidbulletin_status == NULL) {
                            return '-';
                        }
                        if ($model->bidbulletin_status == 1) {
                            return 'Pending';
                        }
                        if ($model->bidbulletin_status == 2) {
                            return 'Accepted';
                        }
                        if ($model->bidbulletin_status == 3) {
                            return 'Declined';
                        }
                        if ($model->bidbulletin_status == 4) {
                            return 'Revised';
                        }
                        if ($model->bidbulletin_status == 5) {
                            return 'Created Bidbulletin';
                        }
                    }
                ],
                [
                    'attribute' => 'bidbulletin_remarks',
                    'header' => 'REMARKS',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:20%; white-space: pre-line'],
                ],

                [
                    'class' => 'kartik\grid\ActionColumn',
                    'options' => ['style' => 'width:20%'],
                    'header' => 'ACTIONS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => ' {bidbulletin} {revised} ',
                    'urlCreator' => function ($action, $model) {
                        if ($action == 'bidbulletin') {
                            return ['purchase-request/pr-itemsbidbulletin-create', 'id' => $model->id,];
                        }
                        if ($action == 'revised') {
                            return ['purchase-request/pr-itemsbidbulletin-update', 'id' => $model->id,];
                        }
                    },
                    'buttons' => [
                        'bidbulletin' => function ($url, $model, $key) {

                            echo '<div class="modal fade" id="modal-bulletin-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modal-bulletin-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                    </div>
                                </div>
                            </div>
                        </div>';

                            $script = <<< JS
                                $(document).on('click', '[data-toggle="modal"][data-target="#modal-bulletin-$model->id"]', function() {
                                    var modal = $('#modal-bulletin-$model->id');
                                    var url = $(this).data('url');

                                    modal.find('.modal-body').load(url);
                                });
                            JS;

                            // var_dump($model);die;
                            return Html::a('<span class="glyphicon glyphicon-plus-sign"></span> ', $url, [
                                // 'class' => 'btn btn-success btn-sm',
                                // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                'title' => 'Add Details',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-bulletin-' . $model->id,
                                'data-url' => Url::to(['purchase-request/pr-itemsbidbulletin-create', 'id' => $model->id,])

                            ]);
                        },

                        'revised' => function ($url, $model, $key) {
                            // return Html::button('<span class="glyphicon glyphicon-edit"></span> Revised', ['value' => Url::to(['purchase-request/pr-itemsbidbulletin-update', 'id' => $model->id,]),  'class' => 'btn btn-warning btn-sm']);

                            echo '<div class="modal fade" id="modal-bulletinUpdate-' . $model->id . '" tabindex="-1" role="dialog" aria-labelledby="modal-bulletinUpdate-label" aria-hidden="true" data-keyboard="false" data-backdrop="static">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                    </div>
                                </div>
                            </div>
                        </div>';

                            $script = <<< JS
                                $(document).on('click', '[data-toggle="modal"][data-target="#modal-bulletinUpdate-$model->id"]', function() {
                                    var modal = $('#modal-bulletinUpdate-$model->id');
                                    var url = $(this).data('url');

                                    modal.find('.modal-body').load(url);
                                });
                            JS;

                            // var_dump($model);die;
                            return Html::a('<span class="glyphicon glyphicon-edit"></span> ', $url, [
                                // 'class' => 'btn btn-success btn-sm',
                                // 'id' => 'modalBidbulletinbtn-' . $model->id,
                                'title' => 'Revise',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal-bulletinUpdate-' . $model->id,
                                'data-url' => Url::to(['purchase-request/pr-itemsbidbulletin-update', 'id' => $model->id,])

                            ]);
                        },
                    ],
                    'visibleButtons' => [
                        'bidbulletin' => function ($model) {
                            $item = PrItems::find()->where(['id' => $model->item_id])->one();
                            $pr = PurchaseRequest::find()->where(['id' => $item->pr_id])->one();
                            if ($model['bidbulletin_changes'] == NULL && $pr['pr_no'] != NULL) {
                                return true;
                            }
                            return false;
                        },
                        'revised' => function ($model) {
                            if ($model['bidbulletin_status'] == 3) {
                                return true;
                            }
                            return false;
                        },
                    ],
                ],
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