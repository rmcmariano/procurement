<?php

use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use kartik\grid\DataColumn;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

Modal::begin([
    'header' => 'History Logs',
    'id' => 'modal-status',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalStatus'></div>";
Modal::end();

Modal::begin([
    'header' => 'Cancel Item Remarks',
    'id' => 'modal-cancel',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalCancel'></div>";
Modal::end();

Modal::begin([
    'header' => 'Bid Bulletin',
    'id' => 'modal-bulletin',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalBulletin'></div>";
Modal::end();

Modal::begin([
    'header' => 'Add Item Specification',
    'id' => 'modal-specification',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalSpecification'></div>";
Modal::end();

?>

<div class="purchase-request-index">
    <p>
        <?= GridView::widget([
            'id' => 'grid-id',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
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
            'showPageSummary' => true,
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width: 2%']
                ],
                [
                    'class' => 'kartik\grid\ExpandRowColumn',
                    'options' => ['style' => 'width: 3%'],
                    'value' => function ($model, $key, $index, $column) {

                        return GridView::ROW_COLLAPSED;
                    },
                    'detail' => function ($model, $key, $index, $column) {

                        $searchModel = new ItemSpecificationSearch();
                        $searchModel->item_id = $model->id;
                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                        return Yii::$app->controller->renderPartial('pr_itemspecs_expand_view', [
                            'dataProvider' => $dataProvider,
                            'searchModel' => $searchModel,
                            'model' => $model
                        ]);
                    },
                ],
                [
                    'attribute' => 'pr_id',
                    'header' => 'PR #',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:15%;'],
                    'value' => function ($model) {
                        $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();

                        if (in_array($pr->status, ['1'])) {
                            return $pr->temp_no;
                        }
                        return $pr->pr_no;
                    },
                    'filter' => true,
                ],
                [
                    'attribute' => 'unit',
                    'header' => 'UNIT',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:5%'],
                    'hAlign' => 'center',
                    'filter' => false
                ],
                [
                    'attribute' => 'item_name',
                    'header' => 'ITEM DESCRIPTION',
                    'format' => 'ntext',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:35%'],
                    'filter' => false
                ],
                [
                    'attribute' => 'unit_cost',
                    'header' => 'UNIT COST',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'format' => [
                        'decimal', 2
                    ],
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'right',
                    'filter' => false
                ],
                [
                    'attribute' => 'quantity',
                    'header' => 'QUANTITY',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:5%'],
                    'hAlign' => 'center',
                    'filter' => false
                ],
                [
                    'class' => DataColumn::class,
                    'attribute' => 'total_cost',
                    'header' => 'TOTAL COST',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'format' => [
                        'decimal', 2
                    ],
                    'options' => ['style' => 'width:10%'],
                    'pageSummary' => true,
                    'hAlign' => 'right',
                    'filter' => false
                ],
                [
                    'attribute' => 'status',
                    'header' => 'ITEM STATUS',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'value' => function ($model) {
                        if (isset($model->statusdisplay)) {
                            return $model->statusdisplay->status;
                        }
                        return 'No Bidder';
                    },
                    'options' => ['style' => 'width:15%;'],
                    'contentOptions' => ['style' => 'text-align:center']
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'options' => ['style' => 'width:15%'],
                    'header' => 'ACTIONS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    // 'template' => '{cancel} {status} {view}',
                    'template' => '{view} {status}',
                    'buttons' => [
                        'status' => function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id], ['title' => 'History Logs']);
                        },
                        'cancel' => function ($url, $model, $key) {
                            return ($model->status != 18 ? Html::button('<span class="glyphicon glyphicon-remove"></span>', ['value' => Url::to(['purchase-request/purchaserequest-itemcancel', 'id' => $model->id,]), 'class' => 'btn btn-danger btn-sm modalCancelbtn', 'title' => 'Cancel']) : '');
                        },
                        'view' => function ($url, $model, $key) {
                            // return Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', ['purchase-request/purchaserequest-view', 'id' => $model->pr_id],  ['class' => 'btn btn-success btn-sm']);
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/purchaserequest-view', 'id' => $model->pr_id], ['title' => 'View']);
                        },
                    ],
                ],
            ]
        ]);
        ?>
    </p>
</div>



<?php
$this->registerJs(
    <<<JS

    $('[data-toggle="tabajax"]').click(function(e) {
        var elem = $(this),
        loadurl = elem.attr('href'),
        targ = elem.attr('data-target');

        $.get(loadurl, function(data) {
            $(targ).html(data);
        });


        elem.tab('show');
        return false;
    });

    $('.modalStatusbtn').on("click", function(){
        $('#modal-status').modal("show");
        $.get($(this).val(), function(data){
            $('#modalStatus').html(data);
        });
    });

    
    $('.modalCancelbtn').on("click", function(){
        $('#modal-cancel').modal("show");
        $.get($(this).val(), function(data){
            $('#modalCancel').html(data);
        });
    });

    $('.modalBidbulletinbtn').on("click", function(){
        $('#modal-bulletin').modal("show");
        $.get($(this).val(), function(data){
            $('#modalBulletin').html(data);
        });
    });

    $('.modalSpecificationbtn').on("click", function(){
        $('#modal-specification').modal("show");
        $.get($(this).val(), function(data){
            $('#modalSpecification').html(data);
        });
    });
JS
);
