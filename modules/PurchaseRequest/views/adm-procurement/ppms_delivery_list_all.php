<?php

use app\modules\PurchaseRequest\models\DeliverySearch;
use app\modules\PurchaseRequest\models\Supplier;
use app\modules\PurchaseRequest\models\TrackStatus;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Orders';
// $this->params['breadcrumbs'][] = $this->title;

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
<div class="purchase-order-index">
  <p>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
        <h3 class="panel-title pull-left">LIST OF DELIVERY</h3>
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
                $searchModel = new DeliverySearch();
                $searchModel->po_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return Yii::$app->controller->renderPartial('/adm-procurement/ppms_delivery_list_all_expandrow', [
                  'dataProvider' => $dataProvider,
                  'model' => $model
                ]);
              },
            ],
            // [
            //   'attribute' =>  'po_date_created',
            //   'header' => 'DATE CREATED',
            //   'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            //   'options' => ['style' => 'width:8%;'],
            //   'value' => function ($model) {
            //     return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php:d-M-Y');
            //   },
            // ],
            [
              'attribute' =>  'actual_date_delivery',
              'header' => 'DATE OF DELIVERY',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:8%;'],
              'value' => function ($model) {
                if ($model->actual_date_delivery == NULL) {
                  return '-';
                }
                return Yii::$app->formatter->asDatetime(strtotime($model->actual_date_delivery), 'php:d-M-Y');
              },
            ],
            [
              'attribute' =>  'po_no',
              'header' => 'P.O. #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
            ],
            // [
            //   'attribute' => 'bid_id',
            //   'format' => 'html',
            //   'value' => function ($model) {
            //     $orderItems = PurchaseOrderItems::find()->where(['po_id' => $model->id])->all();
            //     $output = '';
            //     foreach ($orderItems as $orderItem) {
            //       $bidding = BiddingList::find()->where(['id' => $orderItem->bid_id])->one();
            //       $output .= $bidding->item_remarks . "<br/>";
            //     }
            //     return $output;
            //   },
            //   'options' => ['style' => 'width:20%'],
            //   'hAlign' => 'center',
            //   'contentOptions' => ['style' => 'text-align: left'],
            //   'header' => 'ITEM',
            //   'headerOptions' => ['style' => 'color:#337ab7'],
            // ],
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
              'attribute' => 'place_delivery',
              'options' => ['style' => 'width:15%'],
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
                  return 'PARTIAL';
                }

                if ($model->delivery_status == 'c') {
                  return 'COMPLETE';
                }
              },
              'options' => ['style' => 'width:10%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'PAYMENT STATUS',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'template' => '{view} {create_del}',
              'options' => ['style' => 'width:10%'],
              'buttons' => [
                'view' => function ($url, $model, $key) {
                  return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/procurement-prview', 'id' => $model->pr_id], ['title' => 'view']);
                },
                'create_del' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-plus-sign"></span> Delivery Date', ['value' => Url::to(['purchase-order/ppms-delivery-create', 'id' => $model->id,]),  'class' => 'btn btn-success btn-sm modalDelivryBtn']);
                },
              ],
              'visibleButtons' => [
                'create_del' => function ($model) {
                  if (in_array($model->delivery_status, ['c'])) {
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
