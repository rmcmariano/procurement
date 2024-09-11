<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'For-Receive Documents';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box box-primary">
	<div class="box-header with-border">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'rowOptions' => function ($url) {

                if ($url->view == 1) {
                    return ['class' => 'info'];
                }
        
                else{
                    return ['class' => 'success'];
                }

            },
            'columns' => [
                'tracking_number',
                [

                            'header' => 'Title',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => function ($data) {
                                return $data->documentDetails->title;
                            }

                        ],
                [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Actions',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}',
                'buttons' => [
                'view'   => function ($url, $tracking) {
                    $url = Url::to(['view', 'id' => $tracking->id, 'type' => $_GET['type']]);
                    return Html::a('<span class="fa fa-eye"></span>', $url, ['title' => 'view']);
                        },
                    ]    
                ]
            ],
        ])?>
	</div>
</div>
