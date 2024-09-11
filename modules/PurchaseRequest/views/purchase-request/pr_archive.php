<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\PurchaseRequest\models\PrItemSearch;
use app\modules\user\models\Profile;

?>

<div class="purchase-request-archive">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>CANCELLED REQUEST LISTS</h3>
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
                // 'pjax' => true,
                'panel' => ['type' => 'default',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'rowOptions' => function ($url) {
                    if (in_array($url->status, ['31'])) {
                        return ['class' => 'danger'];
                    }
                },
                'columns' => [

                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail' => function ($model, $key, $index, $column) {
                            $searchModel = new PrItemSearch();
                            $searchModel->pr_id = $model->id;
                            $dataProvider = $searchModel->archiveIndex(Yii::$app->request->queryParams);

                            return Yii::$app->controller->renderPartial('pr_items_expand', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                            ]);
                        },
                    ],

                    [
                        'attribute' => 'temp_no',
                        'header' => 'PR #',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:10%;'],
                        'value' => function ($model) {
                            if (in_array($model->status, ['1', '31'])) {
                                return $model->temp_no;
                            }
                            return $model->pr_no;
                        }, 
                    ],
                    [
                        'attribute' => 'date_of_pr',
                        'header' => 'DATE PREPARED',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime(strtotime($model->date_of_pr), 'php:d-M-Y');
                        },
                        'options' => ['style' => 'width:7%'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'pr_type_id',
                        'header' => 'TYPE OF PR',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => 'prtype.type_name',
                        'options' => ['style' => 'width:7%'],
                    ],
                    [
                        'attribute' => 'purpose',
                        'header' => 'PURPOSE',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:12%'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'charge_to',
                        'header' => 'CHARGE TO',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => 'chargedisplay.project_title',
                        'options' => ['style' => 'width:12%'],
                    ],
                    [
                        'attribute' => 'requested_by',
                        'header' => 'REQUESTED BY',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'format' => 'raw',
                        'value' => function ($v, $model) {
                            $name = Profile::find()->where(['user_id' => $v->requested_by])->one();
                            return $name->fname . ' ' .  $name->lname;
                        },
                        'options' => ['style' => 'width:10%'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'user_id',
                        'format' => 'raw',
                        'header' => 'END USER',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                            return implode("<br/>\n<br/>\n", $model->endUserNames) . '<br/>';
                        },
                        'options' => ['style' => 'width:10%'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'section',
                        'header' => 'SECTION',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => 'sectiondisplay.section_code',
                        'options' => ['style' => 'width:7%'],
                    ],
                    [
                        'attribute' => 'status',
                        'value' => 'statusdisplay.status',
                        'header' => 'STATUS',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:10%'],
                        'contentOptions' => ['style' => 'text-align:center'],
                        'filter' => false
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:20%'],
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{view} {submit} {status} {delete} ',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/purchaserequest-view', 'id' => $model->id], ['title' => 'View']);
                            },
                            'status' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-logs', 'id' => $model->id], ['title' => 'History Logs']);
                            },
                        ],
                        'visibleButtons' => [
                            'delete' => function ($model) {
                                if ($model->status != 8) {
                                    return false;
                                }
                                return true;
                            }

                        ],
                    ],
                ]
            ]); ?>

        </div>
    </div>
</div>


<?php
$this->registerJs(
    <<<JS

$('.modalStatusbtn').on("click", function(){
        $('#modal-status').modal("show");
        $.get($(this).val(), function(data){
            $('#modalStatus').html(data);
        });
    });

    
$('.modalViewbutton').on("click", function(){
        $('#modal-view').modal("show");
        $.get($(this).val(), function(data){
            $('#modalView').html(data);
        });
    });

    

JS
);
?>