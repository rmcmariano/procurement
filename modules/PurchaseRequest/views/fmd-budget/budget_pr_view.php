<?php

use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\bootstrap\Nav;

?>


<?php
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
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<div>
  <?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [

      [
        'label' => 'PR DETAILS',
        'url' => ['purchase-request/budget-prview', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
        'active' => true,
      ],
      [
        'label' => 'BUDGET MONITORING',
        'url' => ['purchase-request/pr-budgetmonitoring', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'PURCHASE ORDER / WORKING ORDER',
        'url' => ['purchase-order/powo-budgetview', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
    ],
  ]) ?>
</div>

<p>
<div class="box box-primary">
  <div style="padding: 20px">
    <p>
      <left>
        <i>
          <h5>Purchase Request Number:</h5>
        </i>
        <h1><?= $model->pr_no ?></h1>
      </left>

    <div class="panel panel-default">
      <div style="padding: 20px">
        <i>
          <h3>Details:</h3>
        </i>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

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
            <!-- <= ($model->charge_to == 0 ? 'GAA' : $model->chargedisplay->project_title) ?></span></td> -->
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
          <h3>Attachments:</h3>
        </i>
        <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
        <p>
          &nbsp; &nbsp; &nbsp;
          <?= GridView::widget([
            'dataProvider' => $dataProvider,
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
                'template' => '{pdf}',
                'buttons' => [
                  'pdf' => function ($url, $model, $key) {
                    $filename = explode('/', $model->file_directory);
                    if ($model->file_extension == 'pdf') {
                      return '<button id ="pdf-details" class="btn btn-primary btn-sm pdf-details" target="_blank" href="https://procurement.itdi.ph/itdi-purchase-request/web/uploads/pr_files/' . $filename[2] . '"> View PDF </button>';
                    }
                    if ($model->file_extension == 'docx') {
                      return '<button id ="file-download" class="btn btn-primary btn-sm file-download" data-id= "' . $model->id . '"> Download </button>';
                    }
                    if ($model->file_extension == 'jpg' || $model->file_extension == 'png') {
                      return '<a href="#" class="pop img-hover-zoom--brightness"> <img style= "width:50px; height:50px;" src="http://localhost/itdi-purchase-request/web/uploads/pr_files/' . $filename[2] . '"></a>';
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
  </div>
</div>







<?php
$this->registerJs(
<<<JS

    $('.modalButton').on("click", function(){
    $('#modal').modal("show");
        $.get($(this).val(), function(data){
            $('#modalContent').html(data);
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