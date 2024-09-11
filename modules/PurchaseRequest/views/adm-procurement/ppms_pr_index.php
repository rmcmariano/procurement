<?php

use app\modules\PurchaseRequest\models\BudgetClustering;
use yii\helpers\Html;
use kartik\grid\GridView;
use app\modules\PurchaseRequest\models\PrItemSearch;
use app\modules\PurchaseRequest\models\ProcurementMode;
use app\modules\PurchaseRequest\models\Info;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\Section;
use app\modules\user\models\Profile;
use kartik\editable\Editable;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Nav;

?>

<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [
            [
                'label' => 'LIST OF PR',
                'url' => ['purchase-request/procurement-request-index'],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
            [
                'label' => 'LIST OF ITEMS',
                'url' => ['purchase-request/procurement-request-items'],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<br>
<div class="ppms-request-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <p>
            <h3>PURCHASE REQUEST LISTS</h3>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <p>
                <center>
                    <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Generate PR Report', ['generate-report/ppms-pr-report', 'id' => $model->id,], ['class' => 'btn btn-warning']) . ' '; ?>
                </center>
            </p>
            <?= GridView::widget([
                'id' => 'grid-id',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'striped' => true,
                'hover' => true,
                'export' => false,
                // 'pjax' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'rowOptions' => function ($url) {

                    if (in_array($url->status, ['7'])) {
                        return ['class' => 'success'];
                    }
                },
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
                        'options' => ['style' => 'width:2%;'],
                        'detail' => function ($model, $key, $index, $column) {

                            $searchModel = new PrItemSearch();
                            $searchModel->pr_id = $model->id;
                            $dataProvider = $searchModel->procurementIndex(Yii::$app->request->queryParams);

                            return Yii::$app->controller->renderPartial('/adm-procurement/ppms_pr_items_expand', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                            ]);
                        },
                    ],
                    [
                        'attribute' => 'pr_no',
                        'header' => 'PR #',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:10%;'],
                        'value' => function ($model) {
                            if (in_array($model->status, ['1', '8', '9'])) {
                                return $model->temp_no;
                            }
                            if ($model->revised_series_no == 0) {
                                return $model->pr_no;
                            }
                            if ($model->revised_series_no == 1) {
                                return $model->pr_no . '-A';
                            }
                            if ($model->revised_series_no == 2) {
                                return $model->pr_no . '-B';
                            }
                            if ($model->revised_series_no == 3) {
                                return $model->pr_no . '-C';
                            }
                            if ($model->revised_series_no == 4) {
                                return $model->pr_no . '-D';
                            }
                            if ($model->revised_series_no == 5) {
                                return $model->pr_no . '-E';
                            }
                            return $model->pr_no;
                        },
                    ],
                    [
                        'attribute' => 'date_of_pr',
                        'header' => 'DATE PREPARED',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:7%;'],
                        'value' => function ($model) {
                            return Yii::$app->formatter->asDatetime(strtotime($model->date_of_pr), 'php:d-M-Y');
                        },
                        'filter' => false
                    ],
                    [
                        'attribute' => 'pr_type_id',
                        'header' => 'TYPE',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => 'prtype.type_name',
                        'options' => ['style' => 'width:7%;'],
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(PrType::find()->orderBy('type_name')->asArray()->all(), 'id', 'type_name'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => 'Search Type of PR'],
                    ],
                    [
                        'attribute' => 'mode_pr_id',
                        'header' => 'MODE OF PROCUREMENT',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => 'procurementmode.mode_name',
                        'options' => ['style' => 'width:10%;'],
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(ProcurementMode::find()->orderBy('mode_name')->asArray()->all(), 'mode_id', 'mode_name'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => 'Search Mode of PR'],
                    ],
                    [
                        'class' => 'kartik\grid\EditableColumn',
                        'attribute' => 'budget_clustering_id',
                        'header' => 'BUDGET CLUSTERING',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'options' => ['style' => 'width:7%'],
                        'contentOptions' => ['style' => 'text-align: center'],
                        'pageSummary' => true,
                        'width' => '100px',
                        'format' => 'html',
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(BudgetClustering::find()->orderBy('expenses_name')->asArray()->all(), 'id', 'expenses_name'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => '- Budget Clustering -'],
                        'editableOptions' => function ($model, $key, $index, $widget) {
                            $budgetTypes = ArrayHelper::map(BudgetClustering::find()->orderBy('expenses_name')->asArray()->all(), 'id', 'expenses_name');

                            $model->budget_clustering_id = ($model->budget_clustering_id === null) ? '<span style="color: red;">Select Budget Clustering</span>' : $model->budget_clustering_id;

                            return [
                                'header' => 'Budget Clustering',
                                'attribute' => 'budget_clustering_id',
                                'size' => 'sm',
                                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                                'displayValueConfig' => $budgetTypes,
                                'data' => $budgetTypes,
                                'showAjaxErrors' => false
                            ];
                        }
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
                        'value' => function ($model) {
                            if ($model->charge_to == 0) {
                                return 'GAA';
                            }
                            return $model->chargedisplay->project_title;
                        },
                        'options' => ['style' => 'width:12%'],
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(Info::find()->orderBy('project_title')->asArray()->all(), 'id', 'project_title'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => 'Search Project'],
                    ],
                    [
                        'attribute' => 'requested_by',
                        'header' => 'REQUESTED USER',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'format' => 'raw',
                        'value' => function ($model) {
                            $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                            $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                            return $fullNameProfile;
                        },
                        'options' => ['style' => 'width:8%'],
                        'filter' => false,
                    ],
                    [
                        'attribute' => 'user_id',
                        'header' => 'END USER',
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                            return implode("<br/>\n<br/>\n", $model->endUserNames) . '<br/>';
                        },
                        'options' => ['style' => 'width:8%'],
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
                        'options' => ['style' => 'width:10%'],
                        'header' => 'ACTIONS',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{view} {status}',
                        'buttons' => [
                            'status' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-logs', 'id' => $model->id], ['title' => 'History Logs']);
                            },
                            'view' => function ($url, $model, $key) {
                                $enable = Html::a('<span class="glyphicon glyphicon-eye-open"></span> ', ['procurement-prview', 'id' => $model->id,],  ['title' => 'View']);
                                $disable = Html::a('<span class="glyphicon glyphicon-eye-open"></span>');

                                if ($model->budget_clustering_id == NULL) {
                                    return $disable;
                                }
                                return $enable;
                            },

                        ],
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

    $('.kv-editable-submit').click(function() {
        $('#purchaserequest-0-budget_clustering_id').css( {
            borderColor: "green"
        })
    });


JS
);
?>


<style>
    .nav-tabs li a {
        background-color: #5F9EA0;
        color: #000000;
        font-weight: bold;
        border-top-right-radius: 16px 16px;

    }

    .nav-tabs:after {
        content: "";
        clear: both;
        display: block;
        background: #000000;
    }


    .nav-tabs li.active {
        height: 40px;
        line-height: 40px;
        width: 200px;
        background: #5F9EA0;
        border-top-left-radius: 16px 16px;
        border-top-right-radius: 16px 16px;
        color: #5F9EA0;
        margin-right: 5px;
        font-weight: bold;

    }

    .nav-tabs li.active:after {
        content: "";
        display: block;
        position: absolute;
        border-left: 35px #5F9EA0;
        left: 145px;
        border-top: 35px solid transparent;
        bottom: 0;
    }
</style>