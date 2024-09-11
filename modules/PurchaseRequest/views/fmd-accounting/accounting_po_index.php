<?php

use app\modules\PurchaseRequest\models\BiddingListSearch;
use app\modules\PurchaseRequest\models\Supplier;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\bootstrap\Nav;


Modal::begin([
  'header' => 'Generate ORS/BURS No.',
  'id' => 'modal-genNum',
  'size' => 'modal-sm',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);

echo "<div id = 'modalGenNum'></div>";
Modal::end();

?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div>
  <?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [
      [
        'label' => 'PR DETAILS',
        'url' => ['purchase-request/accounting-prview', 'id' => $modelPurchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'ITEM DETAILS',
        'url' => ['purchase-request/pr-accountingmonitoring', 'id' => $modelPurchaserequest->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'PURCHASE ORDER / WORKING ORDER',
        'url' => ['purchase-order/accounting-po-index', 'id' => $modelPurchaserequest->id],
        'options' => ['class' => 'nav-tab'],
        'active' => true,
      ],
    ],
  ]) ?>
</div>

<br>
<div class="purchase-request-powo">
  <div class="panel panel-default">
    <div style="padding: 20px">
    <left>
        <i>
          <h5>Purchase Request Number:</h5>
        </i>
        <h1><?= $modelPurchaserequest->pr_no ?></h1>
      </left>

      <?= GridView::widget([
        'id' => 'grid-powo2',
        'dataProvider' => $dataProvider,
        'responsive' => false,
        'options' => [
          'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
        ],
        'striped' => true,
        'hover' => true,
        'export' => false,
        // 'pjax' => true,
        'panel' => ['type' => 'info',],
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
            'options' => ['style' => 'width:3%'],
            'detail' => function ($model, $key, $index, $column) {

              $searchModel = new BiddingListSearch();
              $searchModel->po_id = $model->id;
              $dataProvider = $searchModel->budgetPo(Yii::$app->request->queryParams);

              return Yii::$app->controller->renderPartial('/fmd-budget/budget_po_items_expand', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
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
            'attribute' =>  'po_date_created',
            'header' => 'DATE CREATED',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'options' => ['style' => 'width:8%;'],
            'value' => function ($model) {
              return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php:d-M-Y');
            },
          ],
          [
            'attribute' =>  'ors_burs_num',
            'header' => 'ORS/BURS #',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'options' => ['style' => 'width:10%;'],
          ],
          [
            'attribute' =>  'date_ors_burs',
            'header' => 'DATE OF ORS/BURS',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'options' => ['style' => 'width:8%;'],
            'value' => function ($model) {
              return Yii::$app->formatter->asDatetime(strtotime($model->date_ors_burs), 'php:d-M-Y');
            },
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
            'header' => 'SUPPLIER NAME',
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
          ],
          [
            'class' => 'kartik\grid\ActionColumn',
            'header' => 'Actions',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'template' => '{genNum} {obligate} {validate}',
            'buttons' => [
              'genNum' => function ($url, $model) {
                return Html::button('Generate ORS/BURS No.', ['value' => Url::to(['purchase-order/orsburs-generatenum', 'id' => $model->id]), 'class' => 'btn btn-warning btn-sm genNumbtn']);
              },
              'obligate' => function ($url, $model) {
                return Html::button('<span class="glyphicon glyphicon-check"></span> Obligate',  ['value' => $model['id'], 'class' => 'btn btn-info btn-sm obligateBtn']);
              },
              'validate' => function ($url, $model) {
                return Html::button('<span class="glyphicon glyphicon-check"></span> Validate',  ['value' => $model['id'], 'class' => 'btn btn-info btn-sm validateBtn']);
              }
            ],
            'visibleButtons' => [
              'genNum' => function ($model) {
                if ($model->ors_burs_num != NULL) {
                  return false;
                }
                return true;
              },
              'obligate' => function ($model) {
                if ($model->po_status == 4 || $model->ors_burs_num == NULL) {
                  return false;
                }
                return true;
              },
              'validate' => function ($model) {
                if ($model->po_status == 4) {
                  return true;
                }
                return false;
              }
            ],
          ],
          [
            'class' => 'kartik\grid\ActionColumn',
            'header' => '',
            'template' => '{po}{ors}{wo}{burs}',
            'dropdown' => true,
            'dropdownButton' => [
              'label' =>  'Generate PDF'
            ],
            'dropdownOptions' => ['class' => 'float-left', 'data-boundary' => "viewport"],
            'buttons' => [
              // 'po' => function ($url, $model) {
              //   return '<li>' . Html::a('Purchase Order', ['purchase-order/ppms-purchaseorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
              // },
              'ors' => function ($url, $model) {
                return '<li>' . Html::a('ORS', ['purchase-order/ppms-ors-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
              },
              // 'wo' => function ($url, $model) {
              //   return '<li>' . Html::a('Work Order', ['purchase-order/ppms-purchaseorder-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
              // },
              'burs' => function ($url, $model) {
                return '<li>' . Html::a('BURS', ['purchase-order/ppms-burs-pdf', 'id' => $model['id']], ['target' => 'blank']) . '</li>';
              },
            ],
          ],
        ],
      ]); ?>
      </p>
    </div>
  </div>
</div>






<?php
$this->registerJs(
  <<<JS

  $('.obligateBtn').on('click', function() {
    var idToSubmit = $(this).val();

    swal({
        title: "Do you want to Obligate?",
        text: "Note: This will be submit to FMD-Accounting.",
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
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/budget-po-obligate",
            type: "get",
            data: {
              fmd: $(this).val()
            }, 
          }); 
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

    swal({
        title: "Do you want to Validate?",
        text: "Note: This will be submit to ADM-Procurement.",
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
        
          $.ajax({
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/accounting-po-validate",
            type: "get",
            data: {
              val: $(this).val()
            }, 
          }); 
        });
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            location.reload();
          });
        }
      });
  });

  $('.genNumbtn').on("click", function(){
    $('#modal-genNum').modal("show");
        $.get($(this).val(), function(data){
            $('#modalGenNum').html(data);
        });
    });

    $('[data-toggle="tabajax"]').click(function(e) {
    var elem = $(this),
        loadurl = elem.attr('href'),
        targ = elem.attr('data-target');

    $.get(loadurl, function(data) {
        $(targ).html(data);
    });

    elem.tab('show');
    return false;
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
    width: 400px;
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