<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

if ($_GET['type'] == 'IC'){
$this->title = 'Internal Communication Documents';
}
if ($_GET['type'] == 'I'){
$this->title = 'Incoming Documents';
}
if ($_GET['type'] == 'O'){
$this->title = 'Outgoing Documents';
}
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="box box-primary">
	<div class="box-header with-border">
    <hr style="margin-top: -5px !important; border-bottom: 2px solid #fbc02d;"/>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
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
                    $url = Url::to(['view', 'tracking_number' => $tracking->tracking_number, 'type' => $_GET['type']]);
                    return Html::a('<span class="fa fa-eye"></span>', $url, ['title' => 'view']);
                        },
                    ]    
                ]
            ],
        ])?>
	</div>
</div>
