<?php

use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\BiddingListSearch;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Supplier;

Modal::begin([
  'header' => 'Upload Attachments',
  'id' => 'modal-attach',
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);

echo "<div id = 'modalAttach'></div>";
Modal::end();


Modal::begin([
  'header' => 'Cancel Item Remarks',
  'headerOptions' => ['class' => 'bg-danger'],
  'id' => 'modal-cancel',
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);

echo "<div id = 'modalCancel'></div>";
Modal::end();

?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <br>
      <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true" style="padding-right:10px;">&times;</span><span class="sr-only">Close</span></button>
      <h4 style="padding-left:10px"> Image attachments</h4>
      <hr>
      <div class="modal-body">
        <img src="" class="imagepreview" style="width: 100%;">
      </div>
    </div>
  </div>
</div>

<div class="purchase-request-view">
  <div class="box box-primary">
    <div class="box-header with-border">
      <i>
        <h4>Purchase Request Number:</h4>
      </i>
      <h1 style="text-align: center"> <?= ($model->pr_no == NULL ? $model->temp_no : $model->pr_no); ?> </h1>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

      <div style="text-align:center">
        <?php
        if (in_array($model->status, ['1', '2', '3', '4', '5', '46', '39', '18', '41', '58', '8', '60', '39'])) {
          echo ($cancel = Html::button('<span class="glyphicon glyphicon-trash"></span> Cancel Request',  ['class' => 'btn btn-danger cancel', 'value' =>  $model["id"]]) . ' ');
        }
        if ($model['status'] != '39') {
          echo ($print = Html::a('<span class="glyphicon glyphicon-print"></span> Generate PDF Form', ['purchaserequest-pdf', 'id' => $model->id], ['target' => '_blank', 'class' => 'btn btn-warning']) . ' ');
        }
        if (in_array($model->status, ['39'])) {
          echo ($update = Html::a('<span class="glyphicon glyphicon-pencil"></span> Revise', ['purchaserequest-update', 'id' => $model->id], ['class' => 'btn btn-warning']));
        }
        ?>
      </div>

      <table class="table table-responsive" style="text-transform: uppercase">
        <tr>
          <td style="width: 25%; text-align: left">Date Prepared:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= Yii::$app->formatter->asDatetime(strtotime($model->date_of_pr), 'php:F d, Y') ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Purchasing Type:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= $model->prtype->type_name ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Mode of Procurement:</td>
          <td> <span style="font-size: 17px; font-weight: bold; color: red"><i><?= ($model->procurementmode == NULL ? 'Note: This will be updated by BAC' : $model->procurementmode['mode_name']) ?></i> </span> </td>
        </tr>
        <!-- <tr>
          <td style="width: 25%; text-align: left">Division:</td>
          <td><span style="font-size: 17px; font-weight: bold"><= ($model->divisionThruUser == NULL ? ' ' : $model->divisionThruUser->division_name) ?></span></td>
        </tr> -->
        <tr>
          <td style="width: 25%; text-align: left">Section:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= ($model->sectionThruUser == NULL ? '' : $model->sectionThruUser->section_code) ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Requested By:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= $model->profile ? $model->profile->fname : '' ?> <?= $model->profile ? $model->profile->lname : '' ?></span></td>
        </tr>

        <tr>
          <td style="width: 25%; text-align: left">End Users:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= ($model->enduser == NULL ? ' ' : $model->enduser) ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Purpose:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= ($model->purpose == NULL ? '-' : $model->purpose) ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Charge to:</td>
          <td colspan="2"><span style="font-size: 17px; font-weight: bold">
              <!-- <= ($model->pr_type_id == 3 && $model->charge_to == 0 && $model->charge_to == NULL ? 'GAA' : ($model->pr_type_id == 1 && $model->charge_to == 0 && $model->charge_to == NULL ? 'SDO' : $model->chargedisplay->project_title)) ?></span></td> -->
              <?= ($model->charge_to == 0 && $model->charge_to == NULL ? 'GAA' : $model->chargedisplay->project_title) ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Delivery Period:</td> &nbsp;
          <td><span style="font-size: 17px; font-weight: bold"><?= ($model->delivery_period == NULL ? 'N/A' : $model->delivery_period) ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Warranty:</td> &nbsp;
          <td><span style="font-size: 17px; font-weight: bold"><?= ($model->warranty == NULL ? 'N/A' : $model->warranty) ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Approved By:</td>
          <td><span style="font-size: 17px; font-weight: bold"><?= $model->approvedBy ? $model->approvedBy->fname : '' ?> <?= $model->approvedBy ? $model->approvedBy->lname : '' ?></span></td>
        </tr>
      </table>
    </div>
  </div>
  <p>

    <!-- Item Details in View page of End User -->
  <div class="panel panel-default">
    <div style="padding: 20px">
      <i>
        <h3>Item Details:</h3>
      </i>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
          'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
        ],
        'striped' => true,
        'hover' => true,
        'export' => false,
        'showPageSummary' => true,
        'panel' => ['type' => 'info',],
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => '5'],
        'rowOptions' => function ($url) {
          if (in_array($url->status, ['18'])) {
            return ['class' => 'danger'];
          }
        },
        'columns' => [

          [
            'class' => 'kartik\grid\SerialColumn',
            'options' => ['style' => 'width:2%'],
          ],
          [
            'class' => 'kartik\grid\ExpandRowColumn',
            'options' => ['style' => 'width:2%'],
            'value' => function ($model, $key, $index, $column) {
              return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) {
              $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

              if (in_array($pr->mode_pr_id, ['1', '2', '3'])) {
                $searchModel = new ItemSpecificationSearch();
                $searchModel->item_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return Yii::$app->controller->renderPartial('pr_itemspecs_expand_process', [
                  'dataProvider' => $dataProvider,
                  'searchModel' => $searchModel,
                  'model' => $model
                ]);
              } else {
                $searchModel = new ItemSpecificationSearch();
                $searchModel->item_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return Yii::$app->controller->renderPartial('pr_itemspecs_expand_view', [
                  'dataProvider' => $dataProvider,
                  'searchModel' => $searchModel,
                  'model' => $model
                ]);
              }
            },
          ],
          [
            'attribute' => 'unit',
            'options' => ['style' => 'width:5%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'UNIT',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'item_name',
            'format' => 'ntext',
            'options' => ['style' => 'width:40%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: left'],
            'header' => 'ITEM DESCRIPTION',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'unit_cost',
            'format' => [
              'decimal', 2
            ],
            'options' => ['style' => 'width:10%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: right'],
            'header' => 'UNIT COST',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'quantity',
            'options' => ['style' => 'width:5%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'QUANTITY',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'class' => DataColumn::class,
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
            'pageSummary' => true,
            'pageSummaryOptions' => ['style' => 'text-align: right'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: right'],
            'header' => 'TOTAL COST',
            'headerOptions' => ['style' => 'color:#337ab7'],

          ],
          [
            'attribute' => 'item_status',
            'value' => function ($model) {
              if (isset($model->statusdisplay)) {
                return $model->statusdisplay->status;
              }
              return 'No Bidder';
            },
            'options' => ['style' => 'width:15%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'STATUS',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'class' => 'kartik\grid\ActionColumn',
            'header' => 'ACTIONS',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'template' => '{evaluate} {cancel} {status}',
            'options' => ['style' => 'width:20%'],
            'buttons' => [
              'evaluate' => function ($url, $model, $key) {
                return Html::button('Done Evaluation',  ['class' => 'btn btn-warning btn-xs evaluateBtn', 'value' =>  $model["id"]]);
              },
              'status' => function ($url, $model, $key) {
                return Html::a('<span class="glyphicon glyphicon-time"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id],  ['class' => 'btn btn-primary btn-xs', 'title' => 'History Logs']);
              },
              'cancel' => function ($url, $model, $key) {
                return (Html::button('<span class="glyphicon glyphicon-remove"></span>', ['value' => Url::to(['purchase-request/purchaserequest-itemcancel', 'id' => $model->id,]), 'class' => 'btn btn-danger btn-xs modalCancelbtn', 'title' => 'Cancel']));
              },
            ],
            'visibleButtons' => [
              'evaluate' => function ($model) {
                if ($model->status != 48) {
                  return false;
                }
                return true;
              },
              'cancel' => function ($model) {
                if ($model->status >= 13) {
                  return false;
                }
                return true;
              }
            ],
          ],
        ]
      ]);
      ?>
    </div>
    </p>
  </div>

  <!-- Attachment details in view -->
  <p>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <i>
        <h3>Attachments:</h3>
      </i>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <p>
      <p>
        <?php
        if ($model->status == 44) {
          echo ($create = Html::button('<span class="glyphicon glyphicon-plus"></span> Add Attachments', ['value' => Url::to(['attachment-create', 'id' => $model->id,]),  'class' => 'btn btn-success modalAttachbtn']));
        }
        ?>
      </p>
      &nbsp; &nbsp; &nbsp;

      <?= GridView::widget([
        'dataProvider' => $dataProvider2,
        'showFooter' => true,
        'options' => ['style' => 'width:80%'],
        'columns' => [

          [
            'class' => 'kartik\grid\SerialColumn',
            'options' => ['style' => 'width:5%'],
          ],
          [
            'attribute' => 'file_name',
            'options' => ['style' => 'width:50%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: left'],
            'header' => 'FILE NAME',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'file_extension',
            'options' => ['style' => 'width:15%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'FILE EXTENSION',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'time_stamp',
            'options' => ['style' => 'width:15%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'DATE & TIME',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'value' => function ($model) {
              return Yii::$app->formatter->asDatetime(strtotime($model->time_stamp), 'php:d-M-Y | h:i');
            },
          ],
          [
            'class' => 'kartik\grid\ActionColumn',
            'header' => 'PDF',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'template' => '{pdf} {download}',
            'buttons' => [
              'pdf' => function ($url, $model, $key) {
        
                $filename = explode('/', $model->file_directory);
                if ($model->file_extension == 'pdf') {
                  return '<button id ="pdf-details" class="btn btn-primary btn-sm pdf-details" target="_blank" href="https://procurement.itdi.ph/uploads/pr_files/' . $filename[2] . '"> View PDF </button>';
                }
                if ($model->file_extension == 'docx') {
                  return '<button id ="file-download" class="btn btn-primary btn-sm file-download" data-id= "' . $model->id . '"> Download </button>';
                }
                if ($model->file_extension == 'jpg' || $model->file_extension == 'png') {
                  return '<a href="#" class="pop img-hover-zoom--brightness"> <img style= "width:50px; height:50px;" src="https://procurement.itdi.ph/uploads/pr_files/' . $filename[2] . '"></a>';
                }
              },
              'download' => function ($url, $model, $key) {
                $filename = explode('/', $model->file_directory);
                if ($model->file_extension == 'jpg' || $model->file_extension == 'png') {
                  return '<a href="https://procurement.itdi.ph/uploads/pr_files/' . $filename[2] . '" class="btn btn-primary btn-sm" download> Download Image </a>';
                }
              },
            ],
          ],
        ]
      ]);
      ?>
    </div>
    </p>
  </div>

  <!-- List of Purchase Order -->
  <div class="panel panel-default">
    <div style="padding: 20px">
      <i>
        <h3>Purchase Order / Work Order Lists:</h3>
      </i>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <p>

        <?= GridView::widget([
          'dataProvider' => $dataProvider3,
          'options' => [
            'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
          ],
          'striped' => true,
          'hover' => true,
          'export' => false,
          'showPageSummary' => true,
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

                $searchModel = new BiddingListSearch();
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
              'attribute' =>  'po_date_created',
              'header' => 'DATE CREATED',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
              'value' => function ($model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->po_date_created), 'php:d-M-Y');
              },
            ],
            [
              'attribute' =>  'po_no',
              'header' => 'P.O./W.O #',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:10%;'],
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
              'options' => ['style' => 'width:15%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
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
                // for supplier's conforme
                if ($model->po_status == 6) {
                  return 'FOR CONFORME';
                }
                // for supplier's conforme
                if ($model->po_status == 7) {
                  return 'FOR DELIVERY';
                }
                // for delivery validated
                if ($model->po_status == 8) {
                  return 'DELIVERY VALIDATED';
                }
                // for delivery validated
                if ($model->po_status == 9) {
                  return 'Approval of End User for IAR Changes';
                }
              },
              'options' => ['style' => 'width:15%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'STATUS',
              'headerOptions' => ['style' => 'color:#337ab7'],
            ],
          ],
        ]); ?>
      </p>
    </div>
  </div>
</div>




<?php
$this->registerJsVar('Cancel', Url::to(['https://procurement.itdi.ph/PurchaseRequest/purchase-request/purchaserequest-cancel']));
$this->registerJsVar('Cancel', Url::to(['https://procurement.itdi.ph/PurchaseRequest/purchase-order/purchaseorder-cancel']));
$this->registerJs(
  <<<JS

    $('.cancel').on('click', function() {
        var remarks = "";
        var button = this; // Store reference to 'this'
        swal({
            title: "Cancel Request?",
            icon: "info",
            buttons: {
                confirm: {
                    text: "Yes",
                    value: true,
                },
                cancel: true,
            },
            text: 'Add Remarks:',
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
                        url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/purchaserequest-cancel",
                        type: 'post',
                        data: {
                            "remarks": willDisapprove,
                            id: $(button).val() // Use the stored reference
                        },
                        success: function() {
                            location.reload();
                        },
                        error: function() {
                            // Handle error
                        }
                    }); 
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


    $('.poCancelbtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Cancel PO/WO Request?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Add Remarks:',
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-order/purchaseorder-cancel",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                poid: $(this).val()
                                
                            }
                        }); 
                        // console.log(willDisapprove);
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

  $('.modalAttachbtn').on("click", function(){
        $('#modal-attach').modal("show");
        $.get($(this).val(), function(data){
            $('#modalAttach').html(data);
        });
    });

  $('.modalCancelbtn').on("click", function(){
      $('#modal-cancel').modal("show");
      $.get($(this).val(), function(data){
          $('#modalCancel').html(data);
      });
  });

  $(function() {
    $('.pop').on('click', function() {
      $('.imagepreview').attr('src', $(this).find('img').attr('src'));
      $('#imagemodal').modal('show');
    });
  });

  (function(a) {
    a.createModal = function(b) {
      defaults = {
        title: "",
        closeButton: true,
        scrollable: false
      };
      var b = a.extend({}, defaults, b);
      var c = (b.scrollable === true) ? 'style="max-height: 420px;overflow-y: auto;"' : "";
      html = '<div class="modal fade" id="myModal">';
      html += '<div class="modal-dialog modal-lg">';
      html += '<div class="modal-content">';
      html += '<div class="modal-header">';
      html += '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
      if (b.title.length > 0) {
        html += '<h4 class="modal-title">' + b.title + "</h4>"
      }
      html += "</div>";
      html += '<div class="modal-body" ' + c + ">";
      html += b.message;
      html += "</div>";
      html += '<div class="modal-footer">';
      if (b.closeButton === true) {
        html += '<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>'
      }
      html += "</div>";
      html += "</div>";
      html += "</div>";
      html += "</div>";
      a("body").prepend(html);
      a("#myModal").modal().on("hidden.bs.modal", function() {
        a(this).remove()
      })
    }
  })(jQuery);


  $(function viewPdf(){
    $("body").on('click', '#pdf-details', function(){
      var pdf_link = $(this).attr('href');
      var iframe = '<div class ="iframe-container"><iframe width = "800px" height = "600px" src="' + pdf_link + '"></iframe></div>'
      $.createModal({
        title: 'PDF Attachments',
        message: iframe,
        closeButton: true,
        scrollable: false,
      });
      return false;
    });
  })

  $("body").on('click', '#file-download', function() {

      var obj;
      var id = $(this).data('id');
      obj = {
      id:id,
          };
          var csrfToken = $('meta[name="csrf-token"]').attr("content")
              
            $.ajax({
                url: 'download',
                type: 'post',
                dataType: 'json',
                data: {
                  obj : obj,
                  _csrf : csrfToken,
                },
                success: function(data) {
                  if (data === 'success'){
                    window.location.reload();
                  }else{
                    console.log(data);
                  }
                }
            });
    });

            
    $('.evaluateBtn').on('click', function() {
    var remarks = "";
    swal({
        title: "Are you done for Evaluation of Bidders?",
        icon: "info",
        buttons: {
            confirm: {
                text: "Yes",
                value: true,
            },
            cancel: true,
        },
        text: 'Add Remarks:',
        content: "input",
        closeOnClickOutside: false,
        closeOnEsc: false,
    }).then((willDisapprove) => {
        if (willDisapprove != null) {
            swal("Success.", {
                icon: "success",
            }).then((value) => {
                $.ajax({
                    url: "https://procurement.itdi.ph/PurchaseRequest/bidding/enduser-evaluation-remarks",
                    type: 'post',
                    data: {
                        "remarks": willDisapprove,
                        id: $(this).val()
                    },
                    success: function(response) {
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        // Handle errors if needed
                        console.error(xhr.responseText);
                    }
                });
            });
        } else {
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
?>