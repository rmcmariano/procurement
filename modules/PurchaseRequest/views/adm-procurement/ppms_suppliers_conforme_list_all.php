<?php

use app\modules\PurchaseRequest\models\ConformeAttachments;
use app\modules\PurchaseRequest\models\ConformeAttachmentsSearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Conforme';
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
  'header' => 'Conforme Attachments',
  'id' => 'modal-file',
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);
echo "<div id = 'modalFile'></div>";
Modal::end();

?>
<div class="purchase-order-index">
  <p>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 50px; font-family:Arial, Helvetica, sans-serif;">
        <h1 class="panel-title pull-left" style="font-size: large; margin-top: 8px">LIST OF SUPPLIERS</h1>
        <div class="clearfix"></div>
      </div>
      <p>
        <?= GridView::widget([
          'id' => 'grid-id',
          'dataProvider' => $dataProvider,
          'options' => [
            'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
          ],
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
              'options' => ['style' => 'width:3%'],
              'detail' => function ($model, $key, $index, $column) {

                $searchModel = new ConformeAttachmentsSearch();
                $searchModel->po_id = $model->id;
                $dataProvider = $searchModel->conformeLists(Yii::$app->request->queryParams);
                // var_dump($dataProvider);die;
                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_conforme_list_expandrow', [
                  'dataProvider' => $dataProvider,
                  'model' => $model
                ]);
              },
            ],
            [
              'attribute' => 'supplier_id',
              'value' => function ($model) {
                $supplier = Supplier::find()->where(['id' => $model->supplier_id])->one();

                return $supplier->supplier_name;
              },
              'options' => ['style' => 'width:40%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: left'],
              'header' => 'SUPPLIER NAME',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'attribute' =>  'po_no',
              'header' => 'P.O./W.O. #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],

            ],
            [
              'attribute' =>  'ors_burs_num',
              'header' => 'ORS/BURS #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
              'contentOptions' => ['style' => 'text-align: center']
            ],

            [
              'attribute' => 'conforme_status',
              'value' => function ($model) {
                if ($model->conforme_status == NULL) {
                  return '-';
                }
                // submitted for conforme
                if ($model->conforme_status == 1) {
                  return 'ON PROCESS';
                }
                // after the approval of PPMS and submitted to budget
                if ($model->conforme_status == 2) {
                  return 'CONFIRMED';
                }
                // after the approval of PPMS and submitted to budget
                if ($model->conforme_status == 3) {
                  return 'FOR DELIVERY';
                }
              },
              'options' => ['style' => 'width:15%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'CONFORME STATUS',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            // [
            //   'attribute' => 'date_conforme',
            //   'header' => 'DATE CONFORME',
            //   'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            //   'options' => ['style' => 'width:10%;'],
            //   'value' => function ($model) {
            //     $conforme = ConformeAttachments::find()->where(['po_id' => $model->id])->one();

            //     return Yii::$app->formatter->asDatetime(strtotime($conforme->time_stamp), 'php:d-M-Y | h:i');
            //   },
            //   'filter' => false
            // ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'template' => '{view} {files} {accept}',
              'buttons' => [
                'view' => function ($url, $model, $key) {
                  // var_dump($model);die;
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/procurement-prview', 'id' => $model->pr_id], ['title' => 'view']);
                },

                'files' => function ($url, $model, $key) {
                  return Html::button('<span class="glyphicon glyphicon-upload"> </span> File Uploads', ['value' => Url::to(['purchase-order/ppms-suppliersconforme-create', 'id' => $model->id,]),  'class' => 'btn btn-info btn-sm modalFilebtn']);
                },

                'accept' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-ok"></span> Proceed to Delivery',  ['value' => $model['id'], 'class' => 'btn btn-warning btn-sm acceptBtn', 'title' => 'Confirm']);
                },

                // 'decline' => function ($url, $model) {
                //   return Html::button('<span class="glyphicon glyphicon-remove"></span>',  ['value' => $model['id'], 'class' => 'btn btn-danger declineBtn']);
                // }
              ],
              'visibleButtons' => [
                'accept' => function ($model) {
                  if (in_array($model->po_status, ['7', '6'])) {
                    return true;
                  }
                  return false;
                },
              ],
            ],
          ],
        ]); ?>
      </p>
    </div>
  </div>
  </p>
</div>



<?php
$this->registerJs(
  <<<JS
  
  $('.modalFilebtn').on("click", function(){
        $('#modal-file').modal("show");
        $.get($(this).val(), function(data){
            $('#modalFile').html(data);
        });
    });

  $('.acceptBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to proceed to delivery?",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Confirmed", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "/itdi-purchase-request/web/PurchaseRequest/purchase-order/ppms-suppliersconforme-accept",
            type: "get",
            data: {
              conId: $(this).val()
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

  $('.declineBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to decline?",
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
            url: "/itdi-purchase-request/web/PurchaseRequest/bidding/ppms-suppliersconforme-decline",
            type: "get",
            data: {
              dec: $(this).val()
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
