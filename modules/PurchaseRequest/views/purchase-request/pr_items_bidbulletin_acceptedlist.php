<?php

use app\modules\PurchaseRequest\models\BidBulletin;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Quotation;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;


Modal::begin([
  'header' => 'CREATE BID BULLETIN',
  'id' => 'modal-bidbulletin',
  'headerOptions' => ['class' => 'bg-info'],
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static',
    'float' => true
  ]
]);

echo "<div id = 'modalBidbulletin'></div>";
Modal::end();

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div class="pr-disapproval">
  <div class="box box-primary">
    <div class="panel panel-default" id="div-bulletin">
      <div style="padding: 20px">
        <h3>ACCEPTED BID BULLETIN LISTS</h3>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
        <div class="clearfix"></div>

        <div>
          <p>
            <center>
              <?= Html::button('<span class="glyphicon glyphicon-print"></span> Generate PDF Bid Bulletin Form', ['value' => Url::to(['purchase-request/bac-bidbulletin-create?id=', 'id' => $model['id']]), 'class' => 'btn btn-warning modalBidbulletinbtn']); ?> &nbsp;
            </center>
          </p>
        </div>

        <?= GridView::widget([
          'id' => 'grid-id',
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'options' => ['style' => 'width:100%'],
          'panel' => ['type' => 'info',],
          'export' => false,
          'columns' => [
            [
              'class' => 'kartik\grid\SerialColumn',
              'options' => ['style' => 'width:1%'],
            ],
            [
              'class' => 'kartik\grid\CheckboxColumn',
              'header' => Html::checkbox('selection_all', false, ['class' => 'select-on-check-all', 'value' => 1, 'onclick' => '$(".kv-row-checkbox").prop("checked", $(this).is(":checked"));', 'disabled' => ($model['bidbulletin_id'] != NULL) ? true : false]),
              'contentOptions' => ['class' => 'kv-row-select test'],
              'content' => function ($model, $key) {
                return Html::checkbox('selection[]', false, ['class' => 'kv-row-checkbox', 'value' => $model->id, 'onclick' => '$(this).closest("tr").toggleClass("danger");', 'disabled' => ($model['bidbulletin_id'] != NULL) ? true : false]);
              },
              'hAlign' => 'center',
              'vAlign' => 'middle',
              'hiddenFromExport' => true,
              'mergeHeader' => true,
              'options' => ['style' => 'width:2%'],
            ],

            [
              'class' => 'kartik\grid\ExpandRowColumn',
              'options' => ['style' => 'width:1%'],
              'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
              },
              'detail' => function ($model, $key, $index, $column) {

                $searchModel = new ItemSpecificationSearch();
                $searchModel->item_id = $model->id;
                $dataProvider = $searchModel->acceptedBidbulletin(Yii::$app->request->queryParams);

                return Yii::$app->controller->renderPartial('pr_itemspecs_expand_bidbulletin_acceptedlist', [
                  'dataProvider' => $dataProvider,
                  'searchModel' => $searchModel,
                  'model' => $model
                ]);
              },
            ],
            [
              'attribute' => 'pr_id',
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'PR #',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'value' => function ($model) {
                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                return $pr->pr_no;
              },
              'filter' => true,
            ],
            [
              'attribute' => 'mode_pr_id',
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'MODE OF PR',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'value' => function ($model) {
                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                return $pr->procurementmode->mode_name;
              },
              'filter' => true,
            ],
            [
              'attribute' => 'options_date',
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'DATE OPENING',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'value' => function ($model) {
                $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                $quotation = Quotation::find()->where(['pr_id' => $pr->id])->andWhere(['option_id' => '4'])->one();
                return Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:d-M-Y | H:i a');
              },
              'filter' => true,
              'filterType' => GridView::FILTER_SELECT2,
              // 'filter' => ArrayHelper::map(PrType::find()->orderBy('type_name')->asArray()->all(), 'id', 'type_name'),
              'filterWidgetOptions' => [
                'pluginOptions' => ['allowClear' => true],
              ],
              'filterInputOptions' => ['placeholder' => 'Any type'],
            ],
            [
              'attribute' => 'item_name',
              'format' => 'ntext',
              'options' => ['style' => 'width:20%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: left'],
              'header' => 'ITEM NAME',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
              'filter' => false,
            ],

            [
              'attribute' => 'bid_title',
              'format' => 'ntext',
              'options' => ['style' => 'width:20%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: left'],
              'header' => 'BID TITLE',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
              'filter' => false,
            ],
            [
              'attribute' => 'bidbulletin_id',
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center;'],
              'header' => 'Bid bulletin #',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'value' => function ($model) {
                $bidbulletinModel = BidBulletin::find()->where(['id' => $model->bidbulletin_id])->one();
                if ($model->bidbulletin_id == NULL) {
                  return 'not set';
                }
                return $bidbulletinModel->bidbulletin_no;
              }
            ],
            // [
            //   'attribute' => 'unit_cost',
            //   'format' => [
            //     'decimal', 2
            //   ],
            //   'options' => ['style' => 'width:10%'],
            //   'hAlign' => 'center',
            //   'contentOptions' => ['style' => 'text-align: right'],
            //   'header' => 'UNIT COST',
            //   'headerOptions' => ['style' => 'color:#337ab7'],
            //   'filter' => false,
            // ],
            // [
            //   'attribute' => 'quantity',
            //   'options' => ['style' => 'width:5%'],
            //   'hAlign' => 'center',
            //   'contentOptions' => ['style' => 'text-align: center'],
            //   'header' => 'QUANTITY',
            //   'headerOptions' => ['style' => 'color:#337ab7'],
            //   'filter' => false,

            // ],
            [
              'attribute' => 'total_cost',
              'format' => [
                'decimal', 2
              ],
              'value' => function ($model) {
                if ($model->status == 18) {

                  return '';
                }
                return $model['total_cost'];
              },
              'options' => ['style' => 'width:10%; text-align: right'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: right'],
              'header' => 'TOTAL COST',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'filter' => false,
            ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'ACTIONS',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'options' => ['style' => 'width:10%;'],
              'template' => '{view} {bidbulletin}',
              'buttons' => [
                'bidbulletin' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-print"></span>', ['purchase-request/bac-bidbulletin-pdf', 'id' => $model->bidbulletin_id],  ['target' => 'blank', 'title' => 'Bidbulletin Form']);
                },
                'view' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/pr-itemsbidbulletinlist', 'id' => $model->pr_id], ['title' => 'View']);
                }
              ],
              'visibleButtons' => [
                'bidbulletin' => function ($model) {
                  if ($model->bidbulletin_id == NULL) {
                    return false;
                  }
                  return true;
                },
              ],
            ],
          ]
        ]);
        ?>
      </div>
    </div>
    </p>
  </div>
</div>

<?php
$this->registerJs(
  <<<JS

$('.modalBidbulletinbtn').on("click", function(){
    $('#modal-bidbulletin').modal("show");
        var selectedKeys = $('#grid-id').yiiGridView('getSelectedRows');
        var currentId = $(this).data('id');
        $.get(
            $(this).val(),
        {
            id: currentId,
            keys: selectedKeys
        },
        function(data){
            $('#modalBidbulletin').html(data);
        });
    });

JS
);
?>