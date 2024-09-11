<?php

use app\modules\PurchaseRequest\models\DeliverySearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\bootstrap\Nav;


Modal::begin([
  'header' => 'Create Delivery',
  'id' => 'modal-delivery',
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);
echo "<div id = 'modalDelivery'></div>";
Modal::end();

?>

<div>
  <?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [
      [
        'label' => 'PR DETAILS',
        'url' => ['purchase-request/procurement-prview', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'PURCHASE ORDER',
        'url' => ['bidding/ppms-biddingawardlist', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
        'visible' => (!in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19'])),
      ],
      [
        'label' => 'WORK ORDER',
        'url' => ['bidding/ppms-biddingawardlist', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
        'visible' => in_array($purchaserequest->budget_clustering_id, ['1', '2', '3', '10', '11', '19']),
      ],
      [
        'label' => 'CONFORME',
        'url' => ['purchase-order/ppms-suppliersconforme-index', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'DELIVERY',
        'url' => ['purchase-order/ppms-delivery-index', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
        'active' => true,
      ],
      [
        'label' => 'INSPECTION',
        'url' => ['inspection-acceptance-report/ppms-iar-index', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'PAR/ICS',
        'url' => ['inspection-acceptance-report/ppms-ics-index', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'DISBURSEMENT VOUCHER',
        'url' => ['purchase-order/ppms-dv-index', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
    ],
  ]) ?>
</div>

<br>
<div class="purchase-order-index">
  <div class="panel panel-default">
    <div style="padding: 20px">
      <left>
        <i>
          <h5>Purchase Request Number:</h5>
        </i>
        <h1><?= $purchaserequest->pr_no ?></h1>
      </left>
      <i>
        <h3>List of Delivery:</h3>
      </i>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <p>

        <?= GridView::widget([
          'id' => 'grid-delivery',
          'dataProvider' => $dataProvider,
          'options' => [
            'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
          ],
          'export' => false,
          'striped' => true,
          'hover' => true,
          'pjax' => true,
          'panel' => ['type' => 'info',],
          'floatHeader' => true,
          'floatHeaderOptions' => ['scrollingTop' => '5'],
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
              'options' => ['style' => 'width:2%'],
              'detail' => function ($model, $key, $index, $column) {
                $searchModel = new DeliverySearch();
                $searchModel->po_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $_GET['id']);

                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_delivery_index_expandrow', [
                  'dataProvider' => $dataProvider,
                  'model' => $model
                ]);
              },
            ],
            [
              'attribute' =>  'po_no',
              'header' => 'P.O. #',
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
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: left'],
              'header' => 'SUPPLIERS NAME',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'attribute' =>  'date_delivery',
              'header' => 'DATE OF DELIVERY',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
              'value' => function ($model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->actual_date_delivery), 'php:Y-M-d');
              },
            ],
            [
              'attribute' => 'place_delivery',
              'options' => ['style' => 'width:20%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'PLACE OF DELIVERY',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'attribute' => 'delivery_status',
              'value' => function ($model) {

                if ($model->delivery_status == NULL) {
                  return '-';
                }

                if ($model->delivery_status == 'b') {
                  return 'Partial Delivery';
                }

                if ($model->delivery_status == 'c') {
                  return 'Complete Delivery';
                }

                if ($model->delivery_status == 'd') {
                  return 'Delivery Validated';
                }
              },
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'DELIVERY STATUS',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'template' => '{create_del}',
              'options' => ['style' => 'width:20%'],
              'buttons' => [
                'create_del' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-plus-sign"></span>  Add Delivery Details', ['value' => Url::to(['purchase-order/ppms-delivery-create', 'id' => $model->id,]),  'class' => 'btn btn-info btn-sm modalDelivryBtn']);
                },
                'verify' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-ok"></span> Validate',  ['value' => $model['id'], 'class' => 'btn btn-warning btn-sm validateBtn', 'title' => 'Validate']);
                },
              ],
              'visibleButtons' => [
                'create_del' => function ($model) {
                  if (in_array($model->delivery_status, ['c', 'd'])) {
                    return false;
                  }
                  return true;
                },
                'verify' => function ($model) {
                  if (in_array($model->delivery_status, ['d'])) {
                    return false;
                  }
                  return true;
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
  
  $('.modalDelivryBtn').on("click", function(){
        $('#modal-delivery').modal("show");
        $.get($(this).val(), function(data){
            $('#modalDelivery').html(data);
        });
    });

  $('.acceptBtn').on('click', function() {
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
            url: "acceptconforme",
            type: "get",
            data: {
              acc: $(this).val()
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

  $('.validateBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Please Validate the Delivery",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Validated", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/ppms-delivery-validated",
            type: "get",
            data: {
              valId: $(this).val()
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
            url: "declineconforme",
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
    width: 300px;
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