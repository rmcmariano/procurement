<?php

use app\modules\PurchaseRequest\models\Supplier;
use app\modules\PurchaseRequest\models\TrackStatus;
use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Orders';
// $this->params['breadcrumbs'][] = $this->title;


?>
<div class="purchase-order-index">
  <p>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 45px; font-family:Arial, Helvetica, sans-serif;">
        <h1 class="panel-title pull-left" style="font-size: medium; margin-top: 8px">LIST OF PURCHASE ORDER</h1>
        <div class="clearfix"></div>
      </div>
      <p>

        <?= GridView::widget([
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
              'attribute' =>  'po_date_created',
              'header' => 'DATE CREATED',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:8%;'],
              'value' => function ($model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php:d-M-Y');
              },
            ],
            [
              'attribute' =>  'po_id',
              'header' => 'P.O. #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:8%;'],
            ],
            // [
            //   'attribute' =>  'ors_burs_num',
            //   'header' => 'ORS/BURS #',
            //   'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            //   'options' => ['style' => 'width:8%;'],
            // ],

            [
              'attribute' =>  'date_delivery',
              'header' => 'DATE OF DELIVERY',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:8%;'],
              'value' => function ($model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->date_delivery), 'php:d-M-Y');
              },
            ],

            [
              'attribute' => 'supplier_id',
              'value' => function ($model) {
                $supplier = Supplier::find()->where(['id' => $model->supplier_id])->one();

                return $supplier->supplier_name;
              },
              'options' => ['style' => 'width:15%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'SUPPLIER NAME',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'attribute' => 'po_status',
              'value' => function ($model) {
                $trackStattus = TrackStatus::find()->where(['id' => $model->po_status])->one();
                return $trackStattus->status;
              },
              'options' => ['style' => 'width:15%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'PO STATUS',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'template' => '{accept} {conforme} {cancel} {po} ',
              'buttons' => [
                'po' => function ($url, $model) {
                  return Html::a('<span class="glyphicon glyphicon-print"></span>', ['purchase-order/pdfpo', 'id' => $model->id], ['class' => 'btn btn-default', 'title' => 'Print P.O']);
                },
                'accept' => function ($url, $model) {
                  $enabled =  Html::button('<span class="glyphicon glyphicon-check"></span>',  ['value' => $model['id'], 'class' => 'btn btn-info enableBtn', 'title' => 'Accept']);
                  $disabled =  Html::button('<span class="glyphicon glyphicon-check"></span>',  ['value' => $model['id'], 'class' => 'btn btn-danger enableBtn', 'title' => 'Accept',  'disabled' => true]);
                  // $approved =  Html::button('<span class="glyphicon glyphicon-level-up "></span> Conforme',  ['value' => $model['id'], 'class' => 'btn btn-info enableBtn']);

                  if (in_array($model->po_status, ['32', '22'])) {
                    return $disabled;
                  }
                  return $enabled;
                },
                'conforme' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-ok "></span> Submit for Conforme',  ['value' => $model['id'], 'class' => 'btn btn-info conformBtn']);
                },
                'cancel' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-remove "></span>',  ['value' => $model['id'], 'class' => 'btn btn-danger conformBtn',  'title' => 'Cancel']);
                },
              ],
              'visibleButtons' => [
                'accept' => function ($model) {
                  if (in_array($model->po_status, ['23'])) {
                    return false;
                  }
                  return true;
                },
                'conforme' => function ($model) {
                  if (in_array($model->po_status, ['23'])) {
                    return true;
                  }
                  return false;
                }
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
