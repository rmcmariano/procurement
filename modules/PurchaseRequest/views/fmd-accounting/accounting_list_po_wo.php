<?php

use app\modules\PurchaseRequest\models\PurchaseOrderItemsSearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Orders';
// $this->params['breadcrumbs'][] = $this->title;


?>

<div class="purchase-request-index">
  <div class="box box-primary">
    <div class="box-header with-border">
      <p>

      <h3>PURCHASE ORDER / WORK ORDER LISTS</h3>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <p>
        <?= GridView::widget([
          'id' => 'grid-id',
          'dataProvider' => $dataProvider,
          'filterModel' => $searchModel,
          'responsive' => false,
          'tableOptions' => ['style' => 'overflow-y: visible !important;'],
          'options' => [
            'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
          ],
          'striped' => true,
          'hover' => true,
          // 'pjax' => true,
          'panel' => ['type' => 'default',],
          'floatHeader' => true,
          'floatHeaderOptions' => ['scrollingTop' => '5'],
          'columns' => [
            [
              'class' => 'kartik\grid\SerialColumn',
              'options' => ['style' => 'width:3%;'],
            ],
            [
              'class' => 'kartik\grid\ExpandRowColumn',
              'value' => function ($model, $key, $index, $column) {
                return GridView::ROW_COLLAPSED;
              },
              'options' => ['style' => 'width:2%'],
              'detail' => function ($model, $key, $index, $column) {

                $searchModel = new PurchaseOrderItemsSearch();
                $searchModel->po_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_po_items_expand', [
                  'dataProvider' => $dataProvider,
                  'searchModel' => $searchModel,
                  'model' => $model
                ]);
              },
            ],
            [
              'attribute' =>  'po_no',
              'header' => 'P.O./W.O. #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
            ],
            [
              'attribute' =>  'po_date_created',
              'header' => 'DATE CREATED',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:8%;'],
              'value' => function ($model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php:M d, Y');
              },
              'filter' => false,
            ],
         
            [
              'attribute' =>  'ors_burs_num',
              'header' => 'ORS/BURS #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
            ],
            [
              'attribute' => 'supplier_id',
              'value' => function ($model) {
                $supplier = Supplier::find()->where(['id' => $model->supplier_id])->one();

                return $supplier->supplier_name;
              },
              'options' => ['style' => 'width:30%'],
              'hAlign' => 'left',
              'contentOptions' => ['style' => 'text-align: left'],
              'header' => 'BIDDERS NAME',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],

            [
              'attribute' => 'po_status',
              'value' => function ($model) {
                if ($model->po_status == NULL) {
                  return '-';
                }
                // created PO
                if ($model->po_status == 1) {
                  return 'CREATED';
                }
                // after the approval of PPMS and submitted to budget
                if ($model->po_status == 2) {
                  return 'FOR OBLIGATION';
                }
                // declined by PPMS
                if ($model->po_status == 3) {
                  return 'DECLINED';
                }
                // obligated by FMD-budget
                if ($model->po_status == 4) {
                  return 'OBLIGATED';
                }
                // validated by FMD-accounting
                if ($model->po_status == 5) {
                  return 'VALIDATED';
                }
                // Conforme by FMD-accounting
                if ($model->po_status == 6) {
                  return 'On-process';
                }
              },
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'STATUS',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'filter' => false
            ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'options' => ['style' => 'width:10%'],
              'header' => 'ACTIONS',
              'headerOptions' => ['style' => 'color:#337ab7'],
              // 'template' => '{cancel} {status} {view}',
              'template' => '{view}',
              'buttons' => [
                'view' => function ($url, $model, $key) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span> View', ['purchase-request/budget-prview', 'id' => $model->pr_id],  ['class' => 'btn btn-success btn-sm']);
                },
              ],
            ],
          ],
        ]); ?>
    </div>
  </div>
  </p>
</div>




<?php
$this->registerJs(
  <<<JS

$('.enableBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to approved?",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Approved", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "/itdi-purchase-request/web/PurchaseRequest/bidding/ppms-purchaseorder-accept",
            type: "get",
            data: {
              acpt: $(this).val()
            },
            
          }); console.log(data);
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            location.reload();
          });
        }
      });
  });


$('.conformBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to accept the P.O. for conforme?",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Accepted", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "/itdi-purchase-request/web/PurchaseRequest/bidding/ppms-po-submitforconforme",
            type: "get",
            data: {
              con: $(this).val()
            },
            
          }); console.log(data);
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            location.reload();
          });
        }
      });
  });

 

JS
);
