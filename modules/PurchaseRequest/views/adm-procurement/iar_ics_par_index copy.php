<?php

use app\modules\PurchaseRequest\models\BiddingListSearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


Modal::begin([
    'header' => 'Create IAR',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modal-iar',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalIar'></div>";
Modal::end();


?>

<div class="bidding-list-index">
    <p>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <i>
                <h3>List of PO/WO:</h3>
            </i>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>

                <?= GridView::widget([
                    'id' => 'grid-iar',
                    'dataProvider' => $dataProvider,
                    'responsive' => false,
                    'tableOptions' => ['style' => 'overflow-y: visible !important;'],
                    'panel' => ['type' => 'info'],
                    'export' => false,
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

                                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_iar_items_expand', [
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
                                // for delivery
                                if ($model->po_status == 7) {
                                    return 'FOR DELIVERY';
                                }
                                // for delivery validated
                                if ($model->po_status == 8) {
                                    return 'DELIVERY VALIDATED';
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
                            'template' => '{create_iar} ',
                            'options' => ['style' => 'width:10%'],
                            'buttons' => [
                                'create_iar' => function ($url, $model) {
                                    return Html::button('<span class="glyphicon glyphicon-plus-sign"></span>  Create IAR', ['value' => Url::to(['inspection-acceptance-report/ppms-iar-create', 'id' => $model->id,]),  'class' => 'btn btn-info btn-sm modalIarBtn']);
                                },
                            ],
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => '',
                            'template' => '{iar}',
                            'dropdown' => true,
                            'dropdownButton' => [
                                'label' =>  'Generate PDF'
                            ],
                            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
                            'buttons' => [
                                'iar' => function ($url, $model) {
                                    return '<li>' . Html::a('IAR', ['purchase-order/ppms-purchaseorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
                                }
                            ],
                        ],
                    ],
                ]); ?>
            </p>
        </div>
    </div>
</div>


<?php
$this->registerJs(
    <<<JS

$('.modalIarBtn').on("click", function(){
        $('#modal-iar').modal("show");
        $.get($(this).val(), function(data){
            $('#modalIar').html(data);
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
</style>