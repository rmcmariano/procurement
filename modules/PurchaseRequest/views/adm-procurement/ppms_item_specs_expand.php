<?php

use app\modules\PurchaseRequest\models\PrItems;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
?>

<div class="pr-subdata-index">
    <!-- <div id="ajaxCrudDatatable"> -->

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
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => 'Actions',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => '{request} ',
                    'options' => ['style' => 'width:10%'],
                    'buttons' => [
                        'request' => function ($url, $model, $key) {
                            return Html::button('<span class="glyphicon glyphicon-pencil"></span> Request Changes', ['value' => Url::to(['inspection-acceptance-report/iar-request-changes', 'id' => $model->id]),  'class' => 'btn btn-warning btn-sm modalIarcreateBtn']);
                        },
                    ],
                ],
            ]
        ]);
        ?>
    <!-- </div> -->
</div>


