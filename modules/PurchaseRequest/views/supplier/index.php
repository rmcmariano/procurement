<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Suppliers';
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'header' => 'Create New Supplier',
    'id' => 'modal-supplier',
    'size' => 'modal-lg',
]);

echo "<div id = 'modalSupplier'></div>";
Modal::end();


?>

<div class="supplier-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>SUPPLIER LISTS</h3>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'striped' => true,
                'hover' => true,
                'export' => false,
                'pjax' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width: 2%']
                    ],
                    [
                        'attribute' => 'supplier_name',
                        'header' => 'Supplier Name',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:15%;'],
                        'filter' => true
                    ],
                    [
                        'attribute' => 'supplier_address',
                        'header' => 'Supplier Address',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:15%;'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'owner_name',
                        'header' => 'Owner Name',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:10%;'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'tel_no',
                        'header' => 'Contact #',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:10%;'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'fax_no',
                        'header' => 'FAX #',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:10%;'],
                        'filter' => false
                    ],
                    // [
                    //     'attribute' => 'classification_philgeps',
                    //     'header' => 'Classification',
                    //     'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    //     'options' => ['style' => 'width:5%;'],
                    //     'filter' => true
                    // ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:10%'],
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{view} {update}  ',

                    ],
                ]
            ]); ?>
            </p>
        </div>
    </div>
</div>




<?php
$this->registerJs(
    <<<JS

    $('.modalSupplierbtn').on("click", function(){
        $('#modal-supplier').modal("show");
        $.get($(this).val(), function(data){
            $('#modalSupplier').html(data);
        });
    });


JS
);
?>