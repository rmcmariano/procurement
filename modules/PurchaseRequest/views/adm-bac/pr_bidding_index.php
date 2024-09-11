<?php

use app\modules\PurchaseRequest\models\PrItems;
use app\modules\user\models\Profile;
use kartik\form\ActiveForm;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;


// var_dump($description);die;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

Modal::begin([
    'header' => 'Create Signatories',
    'id' => 'modal-signatories',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalSignatories'></div>";
Modal::end();


Modal::begin([
    'header' => 'Non Comply Bidder',
    'id' => 'modal-noncomply',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalNoncomply'></div>";
Modal::end();


Modal::begin([
    'header' => 'Comply Bidder',
    'id' => 'modal-comply',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalComply'></div>";
Modal::end();

?>

<div class="bidding-list-index">
    <?php $form = ActiveForm::begin(); ?>
    <p>
    <p>
        <?= Html::button('<span class="glyphicon glyphicon-print"></span>  Print Abstract', ['value' => Url::to(['bac-signatories/create', 'id' => $_GET['id']]),  'class' => 'btn btn-default modalSignatoriesbtn']); ?>
    </p>
    <div class="panel panel-default">
        <div style="padding: 20px">
            <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 45px; font-family:Arial, Helvetica, sans-serif;">
                <h1 class="panel-title pull-left" style="font-size: medium; margin-top: 8px">LIST OF BIDDERS</h1>
                <div class="clearfix"></div>
            </div>
            <p>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'options' => [
                        'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                    ],
                    'columns' => [
                        [
                            'class' => 'kartik\grid\SerialColumn',
                            'options' => ['style' => 'width:3%'],
                        ],

                        [
                            'attribute' => 'item_id',
                            'format' => 'ntext',
                            'header' => 'ITEM DESCRIPTION',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => 'PrItemsdisplay.item',
                            'options' => ['style' => 'width:30%'],
                            'contentOptions' => ['style' => 'text-align: left'],
                            'hAlign' => 'center',
                        ],
                        [
                            'attribute' => 'supplier_id',
                            'header' => 'BIDDERS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => 'supplierdisplay.supplier_name',
                            'options' => ['style' => 'width:10%'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'hAlign' => 'center',
                        ],
                        [
                            'attribute' => 'supplier_price',
                            'header' => 'BID PRICE',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'format' => [
                                'decimal', 2
                            ],
                            'options' => ['style' => 'width:5%'],
                            'contentOptions' => ['style' => 'text-align: right'],
                            'hAlign' => 'center',
                        ],
                        [
                            'attribute' => 'time_stamp',
                            'header' => 'DATE',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime(strtotime($model->time_stamp), 'php:d-M-Y');
                            },
                            'options' => ['style' => 'width:10%'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'hAlign' => 'center',
                        ],
                        [
                            'attribute' => 'asssign_twg',
                            'header' => 'BAC MEMBERS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($model) {
                                $name = Profile::find()->where(['user_id' => $model->assign_twg])->one();
                                if (isset($model->userdisplay)) {
                                    return $name->fname . ' ' .  $name->lname;
                                }
                                return 'No TWG Assigned';
                            },
                            'options' => ['style' => 'width:10%'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'hAlign' => 'center',
                        ],
                        [
                            'attribute' => 'status',
                            'header' => 'STATUS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($model) {
                                if (isset($model->statusdisplay)) {
                                    return $model->statusdisplay->status;
                                }
                                return 'No Bidders';
                            },
                            'options' => ['style' => 'width:10%'],
                            'contentOptions' => ['style' => 'text-align: center'],
                            'hAlign' => 'center',
                        ],
                        [
                            'class' => 'kartik\grid\ActionColumn',
                            'header' => 'ACTIONS',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'template' => '{approved} {disapproved}',
                            'buttons' => [
                                'disapproved' => function ($url, $model, $key) {
                                    $status = PrItems::find()->where(['id' => $model->item_id])->one();

                                    $enable = Html::button('<span class="glyphicon glyphicon-remove"></span> Non-Comply', ['value' => Url::to(['bidding/noncomply', 'id' => $model->id,]),  'class' => 'btn btn-danger modalNoncomplybtn']);
                                    $disable =  Html::button('<span class="glyphicon glyphicon-remove"></span> Non-Comply', ['value' => Url::to(['bidding/noncomply', 'id' => $model->id,]),  'class' => 'btn btn-danger modalNoncomplybtn', 'disabled' => true]);

                                    if ($status['status'] == 16 || $status['status'] == 17) {
                                        return $disable;
                                    }
                                    return $enable;
                                },

                                'approved' => function ($url, $model, $key) {

                                    $status = PrItems::find()->where(['id' => $model->item_id])->one();

                                    $enable = Html::button('<span class="glyphicon glyphicon-check"></span> Comply', ['value' => Url::to(['bidding/dialogbox', 'id' => $model->id]), 'class' => 'btn btn-success modalComplybtn']);
                                    $disable = Html::button('<span class="glyphicon glyphicon-check"></span> Comply', ['value' => Url::to(['bidding/dialogbox', 'id' => $model->id]), 'class' => 'btn btn-success modalComplybtn', 'disabled' => true]);

                                    if ($status['status'] == 16 || $status['status'] == 17) {
                                        return $disable;
                                    }
                                    return $enable;
                                },
                            ],
                            'visibleButtons' => [
                                'disapproved' => function ($model) {

                                    if (in_array($model->status, ['15'])) {
                                        return false;
                                    }
                                    return true;
                                },
                                'approved' => function ($model) {
                                    // var_dump($model);die;
                                    if (in_array($model->status, ['15'])) {
                                        return false;
                                    }
                                    return true;
                                }
                            ],
                        ],
                    ],
                ]); ?>
            </p>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    </p>
</div>


<?php
$this->registerJs(
    <<<JS

    $('.modalSignatoriesbtn').on("click", function(){
        $('#modal-signatories').modal("show");
        $.get($(this).val(), function(data){
            $('#modalSignatories').html(data);
        });
    });

    $('.modalNoncomplybtn').on("click", function(){
    $('#modal-noncomply').modal("show");
        $.get($(this).val(), function(data){
            $('#modalNoncomply').html(data);
        });
    });

    $('.modalComplybtn').on("click", function(){
    $('#modal-comply').modal("show");
        $.get($(this).val(), function(data){
            $('#modalComply').html(data);
        });
    });


JS
);
?>