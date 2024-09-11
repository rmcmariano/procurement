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

<div class="purchase-order-index">
  <div class="box box-primary">
    <div class="box-header with-border">
      <p>

      <h3>PURCHASE ORDER / WORK ORDER LISTS</h3>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <p>

      <div class="container-fluid">
        <ul class="nav nav-tabs">
          <li class="active"><a data-toggle="tab" href="#home">ON-PROCESS</a></li>
          <li><?= Html::a('CANCELLED', ['purchase-order/purchaseorder-cancelled', 'id' => $model->id], ['data-toggle' => 'tabajax', 'data-target' => '#cancel-list'])  ?></li>
        </ul>

        <div class="tab-content">
          <div id="home" class="tab-pane fade in active">
            <p>
            <p>
              <!-- <center>
                <= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Generate PR Report', ['generate-report/ppms-pr-report', 'id' => $model->id,], ['class' => 'btn btn-warning']) . ' '; ?>
              </center> -->
            </p>
            <?= GridView::widget([
              'id' => 'grid-id',
              'dataProvider' => $dataProvider,
              'filterModel' => $searchModel,
              'responsive' => false,
              'tableOptions' => ['style' => 'overflow-y: visible !important;'],
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
                  'header' => 'P.O./W.O #',
                  'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                  'options' => ['style' => 'width:10%;'],
                  'contentOptions' => ['style' => 'text-align: center'],
                ],
                [
                  'attribute' =>  'po_date_created',
                  'header' => 'DATE CREATED',
                  'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                  'options' => ['style' => 'width:8%;'],
                  'value' => function ($model) {
                    return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php: M d, Y');
                  },
                ],

                [
                  'attribute' =>  'ors_burs_num',
                  'header' => 'ORS/BURS #',
                  'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                  'options' => ['style' => 'width:10%;'],
                  'contentOptions' => ['style' => 'text-align: center'],
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
                    // Conforme by PPMS
                    if ($model->po_status == 6) {
                      return 'FOR CONFORME';
                    }
                    // for supplier's conforme
                    if ($model->po_status == 7) {
                      return 'FOR DELIVERY';
                    }
                  },
                  'options' => ['style' => 'width:20%'],
                  'hAlign' => 'center',
                  'contentOptions' => ['style' => 'text-align: center'],
                  'header' => 'PO/WO STATUS',
                  'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                [
                  'class' => 'kartik\grid\ActionColumn',
                  'header' => 'Actions',
                  'headerOptions' => ['style' => 'color:#337ab7'],
                  'template' => '{view} {accept}{cancel}  {conforme}',
                  'options' => ['style' => 'width:15%'],
                  'contentOptions' => ['style' => 'text-align: left'],
                  'buttons' => [
                    'view' => function ($url, $model, $key) {
                      return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', ['purchase-request/procurement-prview', 'id' => $model->pr_id], ['class' => 'btn btn-success btn-sm', 'title' => 'View']);
                    },
                    'accept' => function ($url, $model) {
                      return Html::button('<span class="glyphicon glyphicon-check"></span>',  ['value' => $model['id'], 'class' => 'btn btn-info btn-sm enableBtn', 'title' => 'Accept']);
                    },
                    'conforme' => function ($url, $model) {
                      return Html::button('Submit for Conforme',  ['value' => $model['id'], 'class' => 'btn btn-warning btn-sm conformBtn']);
                    },
                    'cancel' => function ($url, $model) {
                      // var_dump($model);die;
                      return Html::button('<span class="glyphicon glyphicon-remove "></span>',  ['class' => 'btn btn-danger btn-sm cancelPo', 'value' => $model->id,  'title' => 'Cancel']);
                    },
                  ],
                  'visibleButtons' => [
                    'accept' => function ($model) {
                      if (in_array($model->po_status, ['1'])) {
                        return true;
                      }
                      return false;
                    },
                    'cancel' => function ($model) {
                      if (in_array($model->po_status, ['1', '5', '6', '7'])) {
                        return true;
                      }
                      return false;
                    },
                    'conforme' => function ($model) {
                      if ($model->po_status == 5) {
                        return true;
                      }
                      return false;
                    }
                  ],
                ],
              ],
            ]); ?>
          </div>

          <div id="cancel-list" class="tab-pane fade in">
            <p>
            <div class="panel panel-default">
              <div style="padding: 20px">
              </div>
            </div>

            </p>
          </div>
        </div>
        </p>
      </div>
    </div>
  </div>
</div>





<?php
$this->registerJs(
  <<<JS

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

  $('.cancelPo').on('click', function() {
            var remarks = "";
            swal({
                title: "Cancel Purchase Order?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Input remarks:',
                content: "input",
                closeOnClickOutside: false,
                closeOnEsc: false,
            })
            .then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "/itdi-purchase-request/web/PurchaseRequest/purchase-order/ppms-purchaseorder-cancel",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                cancelid: $(this).val()
                            }
                        }); 
                        console.log(willDisapprove);
                        location.reload();
                    });       
                }
                else{
                    swal("Canceled", {
                        icon: "warning",
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });
                }
            });
        });
 

JS
);
