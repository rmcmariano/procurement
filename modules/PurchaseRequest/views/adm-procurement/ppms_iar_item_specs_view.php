<?php

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
        <h4 style="text-align: left"><i> Item Name: </i> &nbsp;&nbsp; <strong><?= ($model->prItemsdisplay->item_name); ?></strong> </h4>
        <h4 style="text-align: left"><i> Amount: </i> &nbsp;&nbsp; <strong> P <?= number_format($model->prItemsdisplay->quantity * $model->supplier_price , '2'); ?></strong> </h4>

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
                // [
                //     'class' => 'kartik\grid\ActionColumn',
                //     'header' => 'Actions',
                //     'headerOptions' => ['style' => 'color:#337ab7'],
                //     'template' => '{request} ',
                //     'options' => ['style' => 'width:10%'],
                //     'buttons' => [
                //         'request' => function ($url, $model, $key) {
                //             return Html::button('<span class="glyphicon glyphicon-pencil"></span> Request Changes', ['value' => Url::to(['inspection-acceptance-report/iar-request-changes', 'id' => $model->id]),  'class' => 'btn btn-warning btn-xs modalIarcreateBtn']);
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