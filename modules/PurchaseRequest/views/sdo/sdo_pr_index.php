<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use app\modules\PurchaseRequest\models\PrItemSearch;
use app\modules\PurchaseRequest\models\ProjectBasicInfo;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\Section;
use app\modules\user\models\Profile;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

Modal::begin([
    'header' => 'History Logs',
    // 'headerOptions' => ['id' => 'modalHeader'],
    'id' => 'modal-status',
    'size' => 'modal-lg',
    'clientOptions' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalStatus'></div>";
Modal::end();

?>


<div class="purchase-request-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>ON-PROCESS REQUEST LISTS</h3>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <div class="container-fluid">
                <ul class="nav nav-tabs ">
                    <li class="active"><a data-toggle="tab" href="#home">LIST OF PR</a></li>
                    <li><?= Html::a('LIST OF ITEMS', ['purchase-request/sdo-items', 'id' => $model->id], ['data-toggle' => 'tabajax', 'data-target' => '#items'])  ?></li>
                </ul>

                <div class="tab-content">
                    <div id="home" class="tab-pane fade in active">
                        <p>

                        <p>
                            <center>
                                <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Generate PR Report', ['generate-report/pr-report', 'id' => $model->id,], ['class' => 'btn btn-warning']) . ' '; ?>
                            </center>
                        </p>

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
                                if (in_array($url->status, ['1', '2', '4', '5', '6', '7'])) {
                                    return ['class' => 'success'];
                                }
                                if (in_array($url->status, ['44', '39', '45', '48', '49'])) {
                                    return ['class' => 'warning'];
                                }
                                if (in_array($url->status, ['3'])) {
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
                                        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                                        return Yii::$app->controller->renderPartial('pr_items_expand', [
                                            'searchModel' => $searchModel,
                                            'dataProvider' => $dataProvider,
                                            'model' => $model
                                        ]);
                                    },
                                ],

                                [
                                    'attribute' => 'pr_no',
                                    'header' => 'PR #',
                                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                                    'options' => ['style' => 'width:10%;'],
                                    // 'value' => function ($model) {

                                    //     if (in_array($model->status, ['1', '8', '18', '31'])) {
                                    //         return $model->temp_no;
                                    //     }
                                    //     return $model->pr_no;
                                    // },
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
                                    'filterType' => GridView::FILTER_SELECT2,
                                    'filter' => ArrayHelper::map(PrType::find()->orderBy('type_name')->asArray()->all(), 'id', 'type_name'),
                                    'filterWidgetOptions' => [
                                        'pluginOptions' => ['allowClear' => true],
                                    ],
                                    'filterInputOptions' => ['placeholder' => 'Search Type of PR'],
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
                                    'filterType' => GridView::FILTER_SELECT2,
                                    'filter' => ArrayHelper::map(ProjectBasicInfo::find()->orderBy('project_title')->asArray()->all(), 'id', 'project_title'),
                                    'filterWidgetOptions' => [
                                        'pluginOptions' => ['allowClear' => true],
                                    ],
                                    'filterInputOptions' => ['placeholder' => 'Search Project'],
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
                                        // return Inflector::sentence($model->endUserNames);
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
                                    'filterType' => GridView::FILTER_SELECT2,
                                    'filter' => ArrayHelper::map(Section::find()->orderBy('section_code')->asArray()->all(), 'id', 'section_code'),
                                    'filterWidgetOptions' => [
                                        'pluginOptions' => ['allowClear' => true],
                                    ],
                                    'filterInputOptions' => ['placeholder' => 'Search Section'],
                                ],
                                [
                                    'attribute' => 'status',
                                    'value' => 'statusdisplay.status',
                                    'header' => 'STATUS',
                                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                                    'options' => ['style' => 'width:10%'],
                                    'contentOptions' => ['style' => 'text-align:center']

                                ],

                                [
                                    'class' => 'kartik\grid\ActionColumn',
                                    'options' => ['style' => 'width:20%'],
                                    'header' => 'ACTIONS',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'template' => '{view} {status}',
                                    'buttons' => [
                                        'view' => function ($url, $model, $key) {
                                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', ['purchase-request/sdo-view', 'id' => $model->id],  ['class' => 'btn btn-success btn-sm']);
                                        },
                                        'status' => function ($url, $model, $key) {
                                            // return Html::button('<span class="glyphicon glyphicon-file"></span> Logs', ['value' => Url::to(['purchase-request/purchaserequest-logs', 'id' => $model->id,]),  'class' => 'btn btn-info btn-sm modalStatusbtn']);
                                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span> Logs', ['purchase-request/purchaserequest-logs', 'id' => $model->id],  ['class' => 'btn btn-info btn-sm']);
                                        },
                                    ],
                                ],
                            ]
                        ]); ?>
                        </p>


                    </div>

                    <div id="items" class="tab-pane fade in">
                        <p>
                        <div class="panel panel-default">
                            <div style="padding: 20px">
                            </div>
                        </div>
                        </p>
                    </div>

                </div>
            </div>
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

JS
);
?>


<style>
    .nav-tabs li a {
        background-color: #e4e4e4;
        color: #969696;
        font-weight: bold;
        border-top-right-radius: 16px 16px;

    }

    .nav-tabs:after {
        content: "";
        clear: both;
        display: block;
        background: #AFAFAF;
    }


    .nav-tabs li.active {
        height: 40px;
        line-height: 40px;
        width: 150px;
        background: #e4e4e4;
        border-top-left-radius: 16px 16px;
        border-top-right-radius: 16px 16px;
        color: #e4e4e4;
        margin-right: 5px;
        font-weight: bold;

    }

    .nav-tabs li.active:after {
        content: "";
        display: block;
        position: absolute;
        border-left: 35px #e4e4e4;
        left: 145px;
        border-top: 35px solid transparent;
        bottom: 0;
    }
</style>