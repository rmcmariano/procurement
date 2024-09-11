<?php

use app\modules\PurchaseRequest\models\PrEnduser;
use app\modules\PurchaseRequest\models\PrItems;
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\PurchaseRequest\models\PrItemSearch;
use app\modules\PurchaseRequest\models\ProjectBasicInfo;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Section;
use app\modules\user\models\Profile;
use kartik\grid\DataColumn;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Requests';
// $this->params['breadcrumbs'][] = $this->title;

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

?>

<div class="purchase-request-index">


    <?= GridView::widget([
        'id' => 'validationtable',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
        ],
        'striped' => true,
        'hover' => true,
        'pjax' => true,
        'panel' => ['type' => 'primary', 'heading' => 'Purchase Request List'],
        // 'showFooter' => true,
        'export' => false,
        'showPageSummary' => true,
        'toggleDataContainer' => ['class' => 'btn-group mr-2 me-2'],
        'columns' => [

            [
                'class' => 'kartik\grid\SerialColumn',
                'options' => ['style' => 'width:3%'],
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_COLLAPSED;
                },
                'options' => ['style' => 'width:3%'],
                'detail' => function ($model, $key, $index, $column) {
                    $searchModel = new PrItemSearch();
                    $searchModel->pr_id = $model->id;
                    $dataProvider = $searchModel->topManagementSearch(Yii::$app->request->queryParams);

                    return Yii::$app->controller->renderPartial('pr_top_management_items_expand', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                    ]);
                },
                'group' => true,

            ],
            [
                'attribute' => 'temp_no',
                'header' => 'PR #',
                'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                'options' => ['style' => 'width:10%;'],
                'value' => function ($model) {
                    if (in_array($model->status, ['1', '8', '9'])) {
                        return $model->temp_no;
                    }
                    return $model->pr_no;
                },
                'group' => true,

            ],
            [
                'attribute' => 'date_of_pr',
                'header' => 'DATE PREPARED',
                'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime(strtotime($model->date_of_pr), 'php:d-M-Y');
                },
                'options' => ['style' => 'width:7%'],
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
                'filterInputOptions' => ['placeholder' => 'Any type'],
            ],
            [
                'attribute' => 'purpose',
                'header' => 'PURPOSE',
                'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                'options' => ['style' => 'width:12%'],
                'filter' => false,
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
                'filterInputOptions' => ['placeholder' => 'Project Name'],
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
                'filter' => false,
            ],
            // [
            //     'attribute' => 'end_user_id',
            //     'header' => 'END USER',
            //     'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            //     'value' => function ($v, $model) {
            //         $name = Profile::find()->where(['user_id' => $v->end_user_id])->one();
            //         return ($name == NULL ? '' : $name->fname . ' ' .  $name->lname);
            //     },
            //     'options' => ['style' => 'width:10%'],
            //     'filter' => false,
            // ],
            // [
            //     'attribute' => 'selectuser.user_id',
            //     'group' => true,
                
            // ],
            [
                'attribute' => 'user_id',
                'header' => 'END USER',
                'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                'value' => function ($model) {
                    $endusers = PrEnduser::find()->where(['pr_id' => $model])->all();
                    foreach ($endusers as $enduser) {
                        // $name = Profile::find()->where(['user_id' => $enduser->user_id])->one();
                        // return ($name == NULL ? '' : $name->fname . ' ' .  $name->lname);
                        // var_dump($enduser);die;
                        return $enduser->user_id;
                    }                     
                  
                },
                'options' => ['style' => 'width:10%'],   
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
                'filterInputOptions' => ['placeholder' => 'Any Section'],
            ],
            [
                'class' => DataColumn::class,
                'attribute' => 'total_cost',
                'format' => [
                    'decimal', 2
                ],
                'value' => function ($model) {
                    $items = PrItems::find()->where(['pr_id' => $model->id])->all();
                    $total = 0;
                    foreach ($items as $item) {
                        $total += $item['total_cost'];
                    }
                    return $total;
                },
                'options' => ['style' => 'width:10%; text-align: right'],
                'pageSummary' => true,
                'pageSummaryOptions' => ['style' => 'text-align: right'],
                'hAlign' => 'center',
                'contentOptions' => ['style' => 'text-align: right'],
                'header' => 'TOTAL COST',
                'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'options' => ['style' => 'width:20%'],
                'header' => 'Actions',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'dropdown' => false,
                'vAlign' => 'middle',
                'template' => '{view} {status}',
                'buttons' => [
                    'view' => function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', ['pr-topmanagement-view', 'id' => $model->id],  ['class' => 'btn btn-success btn-sm']);
                    },
                    'status' => function ($url, $model, $key) {
                        return Html::button('<span class="glyphicon glyphicon-file"></span> Logs', ['value' => Url::to(['purchase-request/purchaserequest-logs', 'id' => $model->id,]),  'class' => 'btn btn-info btn-sm modalStatusbtn']);
                    },
                ],
                
            ],
        ]
    ]); ?>

    </p>
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