<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Nav;
use app\modules\PurchaseRequest\models\PrItemSearch;
use app\modules\PurchaseRequest\models\Info;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\Section;
use app\modules\user\models\Profile;

?>

<div>
    <?= Nav::widget([
        'options' => ['class' => 'nav-tabs'],
        'items' => [
            [
                'label' => 'LIST OF PR',
                'url' => ['purchase-request/chief-request-index'],
                'options' => ['class' => 'nav-tab'],
                'active' => true,
            ],
            [
                'label' => 'LIST OF ITEMS',
                'url' => ['purchase-request/chief-request-items'],
                'options' => ['class' => 'nav-tab'],
            ],
        ],
    ]) ?>
</div>

<br>
<div class="chief-index">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>PURCHASE REQUEST LISTS</h3>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'striped' => true,
                'export' => false,
                'hover' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'id' => 'validationtable',
                'rowOptions' => function ($url) {
                    if (in_array($url->status, ['1', '9'])) {
                        return ['class' => 'success'];
                    }
                    if (in_array($url->status, ['2'])) {
                        return ['class' => 'info'];
                    }
                    if (in_array($url->status, ['41'])) {
                        return ['class' => 'warning'];
                    }
                },
                'columns' => [
                    [
                        'class' => 'kartik\grid\SerialColumn',
                    ],
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'value' => function ($model, $key, $index, $column) {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detail' => function ($model, $key, $index, $column) {
                            $searchModel = new PrItemSearch();
                            $searchModel->pr_id = $model->id;
                            $dataProvider = $searchModel->chiefIndex(Yii::$app->request->queryParams);

                            return Yii::$app->controller->renderPartial('pr_items_expand', [
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
                        'options' => ['style' => 'width:15%'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'charge_to',
                        'header' => 'CHARGE TO',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                   
                            if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                                return 'GAA';
                            }
                            if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                                return 'SDO';
                            }
                            if ($model->charge_to == NULL) {
                                return '-';
                            }
                            return $model->chargedisplay->project_title;
                        },
                        'options' => ['style' => 'width:15%'],
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => ArrayHelper::map(Info::find()->orderBy('project_title')->asArray()->all(), 'id', 'project_title'),
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
                            $name = Profile::find()->where(['id' => $v->requested_by])->one();
                            $fullName = ($name ? $name->fname : '') . ' ' .  ($name ? $name->lname : '');

                            return $fullName;
                        },
                        'options' => ['style' => 'width:10%'],
                        'filter' => false
                    ],
                    [
                        'attribute' => 'user_id',
                        'header' => 'END USER',
                        'format' => 'raw',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => function ($model) {
                            return implode("<br/>\n<br/>\n", $model->endUserNames) . '<br/>';
                        },
                        'options' => ['style' => 'width:10%'],
                    ],
                    [
                        'attribute' => 'section',
                        'header' => 'SECTION',
                        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                        'value' => 'sectionThruUser.section_code',
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
                        'contentOptions' => ['style' => 'text-align:center'],
                        'filter' => false
                    ],
                    [
                        'class' => 'kartik\grid\ActionColumn',
                        'options' => ['style' => 'width:10%'],
                        'header' => 'Actions',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'dropdown' => false,
                        'vAlign' => 'middle',
                        'template' => '{view} {status}',
                        'buttons' => [
                            'view' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/chief-prview', 'id' => $model->id], ['title' => 'View']);
                            },
                            'status' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-logs', 'id' => $model->id], ['title' => 'History Logs']);
                            },
                        ],
                    ],
                ]
            ]); ?>
            </p>
        </div>
    </div>
</div>



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