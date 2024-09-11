<?php

use app\modules\PurchaseRequest\models\ConformeAttachments;
use app\modules\PurchaseRequest\models\ConformeAttachmentsSearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\bootstrap\Nav;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Purchase Orders';
$this->params['breadcrumbs'][] = $this->title;

Modal::begin([
  'header' => 'Conforme Attachments',
  'headerOptions' => ['class' => 'bg-info'],
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
        'active' => true,
      ],
      [
        'label' => 'DELIVERY',
        'url' => ['purchase-order/ppms-delivery-index', 'id' => $purchaserequest->id],
        'options' => ['class' => 'nav-tab'],
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
        <h3>Lists of Conforme:</h3>
      </i>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <p>
        <?= GridView::widget([
          'id' => 'grid-conforme',
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
                $searchModel = new ConformeAttachmentsSearch();
                $searchModel->po_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $_GET['id']);

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
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'Actions',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'template' => '{files} {accept}',
              'buttons' => [
                'files' => function ($url, $model, $key) {
                  return Html::button('<span class="glyphicon glyphicon-upload"> </span> File Uploads', ['value' => Url::to(['purchase-order/ppms-suppliersconforme-create', 'id' => $model->id,]),  'class' => 'btn btn-default btn-sm modalFilebtn']);
                },
                'accept' => function ($url, $model) {
                  return Html::button('<span class="glyphicon glyphicon-ok"></span> Proceed to Delivery',  ['value' => $model['id'], 'class' => 'btn btn-warning btn-sm acceptBtn', 'title' => 'Confirm']);
                },
              ],
              'visibleButtons' => [
                'accept' => function ($model) {
                  if (in_array($model->po_status, ['7', '6'])) {
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
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/ppms-suppliersconforme-accept",
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
            url: "https://procurement.itdi.ph/PurchaseRequest/bidding/ppms-suppliersconforme-decline",
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