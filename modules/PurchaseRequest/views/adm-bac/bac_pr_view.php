<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use kartik\grid\DataColumn;
use yii\bootstrap\Nav;

$this->params['breadcrumbs'][] = ['label' => 'Home', 'url' => ['bac-prindex']];
?>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<?php

Modal::begin([
  'header' => 'Upload Attachments',
  'headerOptions' => ['class' => 'bg-success'],
  'id' => 'modal-attach',
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);

echo "<div id = 'modalAttach'></div>";
Modal::end();

?>

<div>
  <?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [

      [
        'label' => 'PR DETAILS',
        'url' => ['purchase-request/bac-prview', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
        'active' => true,
      ],
      [
        'label' => 'SCHEDULING DETAILS',
        'url' => ['purchase-request/bac-quotationindex', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'BID BULLETIN',
        'url' => ['purchase-request/pr-itemsbidbulletinlist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
        'visible' => in_array($model->mode_pr_id, ['1', '2', '3']),
      ],
      [
        'label' => 'SUBMISSION & OPENING OF BIDS',
        'url' => ['bidding/bac-biddingitemlist-smv', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
        'visible' => !in_array($model->mode_pr_id, ['1', '2', '3']),
      ],
      [
        'label' => 'SUBMISSION & OPENING OF BIDS',
        'url' => ['bidding/bac-biddingitemlist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
        'visible' => in_array($model->mode_pr_id, ['1', '2', '3']),
      ],
      [
        'label' => 'WINNING BIDDERS',
        'url' => ['bidding/bac-bidding-complyinglist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'RESOLUTION',
        'url' => ['bidding/bac-resolutionlist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
    ],
  ]) ?>
</div>

<p>
<div class="panel panel-default">
  <div style="padding: 20px">
    <p>
      <left>
        <i>
          <h5>Purchase Request Number:</h5>
        </i>
        <h1><?= $model->pr_no ?></h1>
      </left>

    <div style="text-align:left">
      <?php
      echo (Html::button('Return to End-User', ['class' => 'btn btn-warning btn-lg revisionBtn', 'value' =>  $model["id"]]) . ' ');
      ?>
    </div>
    <br>

    <div class="panel panel-default">
      <div style="padding: 20px">
        <i>
          <h3>Details:</h3>
        </i>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
        <p>
        <p>
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

    <div class="panel panel-default">
      <div style="padding: 20px">
        <i>
          <h3>Item Details:</h3>
        </i>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
        <p>

          <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'options' => [
              'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
            ],
            'export' => false,
            'striped' => true,
            'hover' => true,
            'panel' => ['type' => 'info',],
            'floatHeader' => true,
            'floatHeaderOptions' => ['scrollingTop' => '5'],
            'rowOptions' => function ($url) {
              if (in_array($url->status, ['18'])) {
                return ['class' => 'danger'];
              }
            },
            'showPageSummary' => true,
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
                'value' => function ($model, $key, $index, $column) {
                  return GridView::ROW_COLLAPSED;
                },
                'detail' => function ($model, $key, $index, $column) {

                  $searchModel = new ItemSpecificationSearch();
                  $searchModel->item_id = $model->id;
                  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                  return Yii::$app->controller->renderPartial('/purchase-request/pr_itemspecs_expand_view', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
                    'model' => $model
                  ]);
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
                'options' => ['style' => 'width:45%'],
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
                'attribute' => 'status',
                'value' => function ($model) {
                  if (isset($model->statusdisplay)) {
                    return $model->statusdisplay->status;
                  }
                  return 'No Bidder';
                },
                'options' => ['style' => 'width:20%'],
                'hAlign' => 'center',
                'contentOptions' => ['style' => 'text-align: center'],
                'header' => 'STATUS',
                'headerOptions' => ['style' => 'color:#337ab7'],
              ],
              [
                'class' => 'kartik\grid\ActionColumn',
                'header' => 'ACTIONS',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{status}',
                'options' => ['style' => 'width:15%'],
                'buttons' => [
                  'status' => function ($url, $model, $key) {
                    return Html::a('<span class="glyphicon glyphicon-list-alt"></span>', ['purchase-request/purchaserequest-itemlogs', 'id' => $model->id],  ['class' => 'btn btn-info btn-xs', 'title' => 'History Logs']);
                  },
                ],
              ],
            ]
          ]);
          ?>
      </div>
      </p>
    </div>


    <div class="panel panel-default">
      <div style="padding: 20px">
        <i>
          <h3>Attachments:</h3>
        </i>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
        <?php
        echo ($create = Html::button('<span class="glyphicon glyphicon-upload "></span> Upload Attachments', ['value' => Url::to(['attachments/bac-attachment-create', 'id' => $model->id,]),  'class' => 'btn btn-success btn-sm modalAttachbtn'])) . ' ';

        echo ($request = Html::button('Request End-user for Additional Files', ['class' => 'btn btn-warning btn-sm requestBtn', 'value' => $model["id"]]));
        ?>
        <br><br>

        <?= GridView::widget([
          'dataProvider' => $dataProvider2,
          'showFooter' => true,
          'options' => ['style' => 'width:100%'],
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
              'attribute' => 'time_stamp',
              'options' => ['style' => 'width:15%'],
              'hAlign' => 'center',
              'contentOptions' => ['style' => 'text-align: center'],
              'header' => 'DATE & TIME',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'value' => function ($model) {
                return Yii::$app->formatter->asDatetime(strtotime($model->time_stamp), 'php:Y-m-d h:i');
              },
            ],
            [
              'class' => 'kartik\grid\ActionColumn',
              'header' => 'PDF',
              'headerOptions' => ['style' => 'color:#337ab7'],
              'template' => '{pdf}',
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
              ],
            ],
          ]
        ]);
        ?>
        </p>
        </p>
      </div>
    </div>
  </div>
</div>


<?php
$this->registerJsVar('Request', Url::to(['attachments/bac-attachment-request']));

$this->registerJs(
  <<<JS

        $('.requestBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Request for Additional Attachments?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Remarks:',
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/attachments/bac-attachment-request",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                id: $(this).val()
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

  $('.modalAttachbtn').on("click", function(){
        $('#modal-attach').modal("show");
        $.get($(this).val(), function(data){
            $('#modalAttach').html(data);
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
        title: 'Attachments',
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

       $('.revisionBtn').on('click', function() {
    var remarks = "";
    var id = $(this).val(); // Store the value of $(this).val() in a variable

            swal({
                title: "Return to End-user?",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Remarks:',
                content: "input",
                closeOnClickOutside: false,
                closeOnEsc: false,
            }).then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "/procurement/web/PurchaseRequest/purchase-request/bac-addlrequest-btn",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                "id": id // Use the stored id variable here
                            },
                            success: function(response) {
                                location.reload();
                            },
                            error: function(xhr, status, error) {
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

<style>
  .nav-tabs li a {
    background-color: #5F9EA0;
    color: #000000;
    font-weight: bold;
    border-top-right-radius: 16px 16px;
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