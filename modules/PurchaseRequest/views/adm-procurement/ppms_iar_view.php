<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PrItems;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="modal-header iardesign">
  <h5 class="modal-title" id="modal-iar-label"> View
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="iar-view">
  <?php $form = ActiveForm::begin(); ?>

  <div>
    <h4><i><strong> Inspection Acceptance Report Details</strong></i>
      <?= Html::button('<span class="glyphicon glyphicon-plus"></span>Create IAR ', [
        'class' => 'btn btn-info btn-xs',
        'title' => 'Create',
        'data-toggle' => 'modal',
        'data-target' => '#modalIarCreate-' . $modelPurchaseorder->id,
        'data-url' => Url::to(['inspection-acceptance-report/ppms-iar-create', 'id' => $modelPurchaseorder->id]),
        // 'onclick' => 'yourJavaScriptFunction(); return false;',
      ]);
      ?>

      <div class="modal fade" id="modalIarCreate- <?php $modelPurchaseorder->id ?>" tabindex="-1" role="dialog" aria-labelledby="modalIarCreateLabel" aria-hidden="true" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-body">
            </div>
          </div>
        </div>
      </div>
  </div>

  <table class="table table-responsive; font-size: small; ">
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> ICS No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->iar_number ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of IAR: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : Yii::$app->formatter->asDatetime(strtotime($modelIar->iar_date), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> P.O. No. / W.O. No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_no ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of P.O. / W.O.: </i></strong></td>
      <td style=" text-align: left;"> <?= Yii::$app->formatter->asDatetime(strtotime($modelPurchaseorder->po_date_created), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Supplier Name: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->supplierdisplay->supplier_name ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Sales Invoice Number: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->sales_invoice_number ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of Sale Invoice: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : Yii::$app->formatter->asDatetime(strtotime($modelIar->sales_invoice_date), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Inspector: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->inspector_id ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of Inspection: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : Yii::$app->formatter->asDatetime(strtotime($modelIar->inspection_date), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Status: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->iar_status ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Created By: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->created_by ?> </td>
    </tr>
  </table>

  <?= GridView::widget([
    // 'id' => 'db-iar',
    'dataProvider' => $dataProvider,
    'options' => [
      'style' => 'overflow: auto; word-wrap: break-word;'
    ],
    'columns' => [
      [
        'class' => 'kartik\grid\SerialColumn',
        'options' => ['style' => 'width:2%'],
      ],
      [
        'class' => 'kartik\grid\ExpandRowColumn',
        'value' => function ($model, $key, $index, $column) {
          return GridView::ROW_COLLAPSED;
        },
        'options' => ['style' => 'width:2%'],
        'detail' => function ($model, $key, $index, $column) {

          $searchModel = new ItemSpecificationSearch();
          $searchModel->item_id = $model->item_id;
          $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

          return Yii::$app->controller->renderPartial('/adm-procurement/ppms_iar_item_specs_view_modal', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'model' => $model
          ]);
        },
      ],
      [
        'attribute' => 'unit',
        'header' => 'UNIT',
        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
        'options' => ['style' => 'width:5%'],
        'hAlign' => 'center',
        'value' => function ($model) {
          $item = PrItems::find()->where(['id' => $model->item_id])->one();
          return $item->unit;
        }
      ],
      [
        'attribute' => 'bid_id',
        'header' => 'ITEM / EQUIPMENT NAME',
        'format' => 'ntext',
        'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
        'options' => ['style' => 'width:30%; white-space: pre-line'],
        'value' => function ($model) {
          $item = PrItems::find()->where(['id' => $model->item_id])->one();
          return $item->item_name;
        }
      ],
      [
        'attribute' => 'bid_id',
        'header' => 'BID OFFER',
        'format' => 'ntext',
        'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
        'options' => ['style' => 'width:40%; white-space: pre-line'],
        'value' => function ($model) {
          $items = PrItems::find()->where(['id' => $model->item_id])->one();

          if ($model->item_remarks == $items->id) {
            return $items->item_name;
          }
          return $model->item_remarks;
        }
      ],
      [
        'attribute' => 'quantity',
        'options' => ['style' => 'width:5%'],
        'hAlign' => 'center',
        'contentOptions' => ['style' => 'text-align: center'],
        'header' => 'QTY',
        'headerOptions' => ['style' => 'color:#337ab7'],
        'value' => function ($model) {
          $test = PrItems::find()->where(['id' => $model->item_id])->one();
          return $test->quantity;
        }
      ],
      [
        'attribute' => 'bid_id',
        'header' => 'BID PRICE',
        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
        'options' => ['style' => 'width:10%'],
        'contentOptions' => ['style' => 'text-align: right'],
        'value' => function ($model) {
          return $model->supplier_price;
        },
        'format' => [
          'decimal', 2
        ],
      ],
      [
        'attribute' => 'bid_id',
        'header' => 'TOTAL PRICE',
        'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
        'options' => ['style' => 'width:20%'],
        'contentOptions' => ['style' => 'text-align: right'],
        'value' => function ($model) {
          $item = PrItems::find()->where(['id' => $model->item_id])->one();
          $total =  $item['quantity'] * $model['supplier_price'];
          return $total;
        },
        'format' => [
          'decimal', 2
        ],
      ],
    ]
  ]);
  ?>
  <?php ActiveForm::end(); ?>
</div>


<style>
  .iar-view {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
    font-size: small;
  }

  .iardesign {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #12B359;
  }

  .modal-content {
    border-radius: 20px;
  }
</style>

<?php
$this->registerJs(
  <<<JS

        $(document).on('click', '[data-toggle="modal"][data-target="#modalIarCreate-$modelPurchaseorder->id"]', function() {
            // $("#closed").off("click");

            var modal = $('#modalIarCreate-$modelPurchaseorder->id');
            var url = $(this).data('url');

            modal.find('.modal-body').load(url);
        }); 
JS
);
?>