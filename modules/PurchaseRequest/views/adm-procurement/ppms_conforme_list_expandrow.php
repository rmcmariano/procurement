<?php

use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\PurchaseRequest\models\PrSubdataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Request';
// $this->params['breadcrumbs'][] = $this->title;


?>
<div class="pr-subdata-index">
    <div id="ajaxCrudDatatable">

        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            // 'showFooter' => true,
            'options' => ['style' => 'width:100%'],
            'columns' => [

                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:5%'],
                ],
                [
                    'attribute' => 'file_name',
                    'options' => ['style' => 'width:50%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'FILE NAME',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                [
                    'attribute' => 'remarks',
                    'options' => ['style' => 'width:30%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'REMARKS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    
                ],
                [
                    'attribute' => 'time_stamp',
                    'options' => ['style' => 'width:15%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: center'],
                    'header' => 'DATE & TIME',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime(($model->time_stamp), 'php:d-M-Y | h:i A');
                    },
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => 'PDF',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => '{pdf}',
                    'buttons' => [
                        'pdf' => function ($url, $model, $key) {
                            $filename = explode('/', $model->file_directory);
                            if ($model->file_extension == 'pdf') {
                                return '<button id ="pdf-details" class="btn btn-primary btn-sm pdf-details" target="_blank" href="http://localhost/itdi-purchase-request/web/uploads/pr_conforme_files/' . $filename[2] . '"> View PDF </button>';
                            }
                            if ($model->file_extension == 'docx') {
                                return '<button id ="file-download" class="btn btn-primary btn-sm file-download" data-id= "' . $model->id . '"> Download </button>';
                            }
                            if ($model->file_extension == 'jpg' || $model->file_extension == 'png') {
                                return '<a href="#" class="pop img-hover-zoom--brightness"> <img style= "width:50px; height:50px;" src="http://localhost/itdi-purchase-request/web/uploads/pr_conforme_files/' . $filename[2] . '"></a>';
                            }
                        },
                    ],
                ],
            ]
        ]);
        ?>
    </div>
</div>