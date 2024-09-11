<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use kartik\grid\DataColumn;


Modal::begin([
  'header' => 'Disapproved Purchase Request',
  'id' => 'modal',
  'size' => 'modal-lg',
  'options' => [
    'data-keyboard' => 'false',
    'data-backdrop' => 'static'
  ]
]);

echo "<div id = 'modalContent'></div>";
Modal::end();

?>

<div class="purchase-request-index">
  <div class="box box-primary">
    <div class="box-header with-border">
      <i>
        <h5>Purchase Request Number:</h5>
      </i>
      <h1 style="text-align: center"> <?= ((in_array($model->status, ['1', '8'])) ? $model->temp_no : $model->pr_no); ?> </h1>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

      <div style="text-align:center"> &nbsp;&nbsp;
        <?php
        if (in_array($model->status, ['1', '41'])) {

          echo $approve = Html::button('<span class="glyphicon glyphicon-ok-sign"></span> Approve', ['class' => 'btn btn-success btn-lg approvedBtn', 'value' => $_GET['id'], ]) . ' ';
          echo ($disapprove = Html::button('<span class="glyphicon glyphicon-remove"></span> Disapproved',  ['class' => 'btn btn-danger btn-lg disapproveBtn', 'value' =>  $model["id"]]) . ' ');
        }
        ?>
      </div>

      <table class="table table-responsive">
        <tr>
          <td style="width: 25%; text-align: left">Date Prepared:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= Yii::$app->formatter->asDatetime(strtotime($model->date_of_pr), 'php:d-M-Y') ?></span></td>
        </tr>
        <tr>
          <td class="col-sm-4" style="width: 25%; text-align: left">PR number:</td>
          <td><span style="font-size: 15px; font-weight: bold">
              <?= ((in_array($model->status, ['1', '8'])) ? $model->temp_no : $model->pr_no);
              ?>
            </span></td>
        </tr>
        <tr>
          <td style="text-align: left">Purchasing Type:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->prtype->type_name ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Mode of Procurement:</span> </td>
          <td> <span style="font-size: 15px; font-weight: bold;"><?= $model->mode_pr_id == '' || NULL ? ' ': $model->procurementmode->mode_name ?> </span> </td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Division:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->divisiondisplay->division_name ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Section:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->sectiondisplay->section_name ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Requested By:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->profile->fname ?> <?= $model->profile->lname ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">End Users:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->enduser ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Purpose:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->purpose ?></span></td>
        </tr>
        <tr>
          <td style="width: 25%; text-align: left">Charge to:</td>
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->chargedisplay->project_title ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Delivery Period:</td> &nbsp;
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->delivery_period ?></span></td>
        </tr>
        <tr>
          <td style="text-align: left">Warranty:</td> &nbsp;
          <td><span style="font-size: 15px; font-weight: bold"><?= $model->warranty ?></span></td>
        </tr>
      </table>
    </div>
  </div>
  </p>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 50px; font-family:Arial, Helvetica, sans-serif;">
        <h1 class="panel-title pull-left" style="font-size: large; margin-top: 8px">ITEM DETAILS</h1>
        <div class="clearfix"></div>
      </div>
      <p>
        &nbsp; &nbsp; &nbsp;

        <?= GridView::widget([
          'dataProvider' => $dataProvider,
          'showFooter' => true,
          'options' => ['style' => 'width:100%'],
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
                return GridView::ROW_EXPANDED;
              },
              'detail' => function ($model, $key, $index, $column) {

                $searchModel = new ItemSpecificationSearch();
                $searchModel->item_id = $model->id;
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return Yii::$app->controller->renderPartial('pr_itemspecs_expand_view', [
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
          ]
        ]);
        ?>
    </div>
    </p>
  </div>


  <p>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 50px; font-family:Arial, Helvetica, sans-serif;">
        <h1 class="panel-title pull-left" style="font-size: large; margin-top: 8px">ATTACHMENTS</h1>
        <div class="clearfix"></div>
      </div>
      <p>
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
            'class' => 'kartik\grid\ActionColumn',
            // 'options' => ['style' => 'width:10%'],
            'header' => 'PDF',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'template' => '{pdf}',
            'buttons' => [
              'pdf' => function ($url, $model, $key) {
                $filename = explode('/', $model->file_directory);
                if ($model->file_extension == 'pdf') {
                  return '<button id ="pdf-details" class="btn btn-primary btn-sm pdf-details" target="_blank" href="http://localhost/itdi-purchase-request/web/uploads/pr_files/' . $filename[2] . '"> View PDF </button>';
                }
                if ($model->file_extension == 'docx') {
                  return '<button id ="file-download" class="btn btn-primary btn-sm file-download" data-id= "' . $model->id . '"> Download </button>';
                }
                if ($model->file_extension == 'jpg' || $model->file_extension == 'png' ) {
                  return '<a href="#" class="pop img-hover-zoom--brightness"> <img style= "width:50px; height:50px;" src="http://localhost/itdi-purchase-request/web/uploads/pr_files/' . $filename[2] . '"></a>';
                } 
              },
            ],
          ],
        ]
      ]);
      ?>
    </div>
  </div>
</div>




<?php

$this->registerJsVar('Cancel', Url::to(['purchase-request/chief-prdisapproved']));

$this->registerJs(
  <<<JS

          $('.disapproveBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Disapproved Request?",
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
                            url: "/itdi-purchase-request/web/PurchaseRequest/purchase-request/chief-prdisapproved",
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

    $('.modalButton').on("click", function(){
    $('#modal').modal("show");
        $.get($(this).val(), function(data){
            $('#modalContent').html(data);
        });
    });


//sweetalert 
$('.approvedBtn').on('click', function() {
    var idToSubmit = $(this).val();
    // console.log(idToSubmit);
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
            url: "chief-prapproved",
            type: "get",
            data: {
              rtn: $(this).val()
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
  
  // view btn and download btn of attachments
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
      var iframe = '<div class ="iframe-container"><iframe src="' + pdf_link + '"></iframe></div>'
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


JS
);
?>