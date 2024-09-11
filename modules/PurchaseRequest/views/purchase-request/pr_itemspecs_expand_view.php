<?php


use kartik\grid\GridView;

?>
<div class="pr-subdata-index">
    <div id="ajaxCrudDatatable">

        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'options' => [
                'style' => 'overflow: auto; word-wrap: break-word;'
            ],
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width: 2%']
                ],
                [
                    'attribute' => 'description',
                    'header' => 'ITEM SPECIFICATION',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:40%;'],
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'quantity',
                    'header' => 'QUANTITY',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:40%;'],
                    'format' => 'ntext',
                ],
            ]
        ]);
        ?>
    </div>
</div>