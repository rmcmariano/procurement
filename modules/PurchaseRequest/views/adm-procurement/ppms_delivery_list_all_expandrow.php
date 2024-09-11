<?php

use app\modules\PurchaseRequest\models\DeliveryAttachments;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\PurchaseRequest\models\PrSubdataSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = 'Purchase Request';
// $this->params['breadcrumbs'][] = $this->title;

Modal::begin([
    'header' => 'Delivery Attachments',
    'headerOptions' => ['class' => 'bg-info'],
    'id' => 'modalfile',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);
echo "<div id = 'modalFile2'></div>";
Modal::end();

?>
<div class="pr-subdata-index">
    <div id="ajaxCrudDatatable">

        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            // 'showFooter' => true,
            'options' => ['style' => 'width:100%'],
            'columns' => [

                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:5%'],
                ],
                [
                    'attribute' => 'actual_date_delivery',
                    'options' => ['style' => 'width:20%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'ACTUAL DATE DELIVERY',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime(strtotime($model->actual_date_delivery), 'php:d-M-Y | h:i');
                    },
                ],
                [
                    'attribute' => 'type_delivery',
                    'options' => ['style' => 'width:30%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'TYPE OF DELIVERY',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'value' => function ($model) {
                        if ($model->type_delivery == 'b') {
                            return 'Partial Payment';
                        }
                        if ($model->type_delivery == 'c') {
                            return 'Complete Payment';
                        }
                    }
                ],
                [
                    'attribute' => 'si_number',
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'Delivery Receipt',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                [
                    'attribute' => 'si_amount',
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'REMARKS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                [
                    'attribute' => 'remarks',
                    'options' => ['style' => 'width:30%'],
                    'hAlign' => 'center',
                    'contentOptions' => ['style' => 'text-align: left'],
                    'header' => 'REMARKS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'header' => 'Actions',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => '{files}',
                    'buttons' => [
                        'files' => function ($url, $model, $key) {
                            return Html::button('<span class="glyphicon glyphicon-upload"> </span> File Uploads', ['value' => Url::to(['purchase-order/deliveryattachments-create', 'id' => $model->id,]),  'class' => 'btn btn-default btn-sm modalfilebtn']);
                        },
                    ],
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    // 'options' => ['style' => 'width:10%'],
                    'header' => 'PDF',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => '{pdf}',
                    'buttons' => [
                        'pdf' => function ($url, $model, $key) {
                            $delAttach = DeliveryAttachments::find()->where(['delivery_id' => $model->id])->one();
                            if ($delAttach == NULL) {
                                return '';
                            }
                            $filename = explode('/', $delAttach->file_directory);

                            if ($delAttach->file_extension == 'pdf') {
                                return '<button id ="pdf-details" class="btn btn-primary btn-sm pdf-details" target="_blank" href="http://localhost/itdi-purchase-request/web/uploads/pr_delivery_files/' . $filename[2] . '"> View PDF </button>';
                            }
                            if ($delAttach->file_extension == 'docx') {
                                return '<button id ="file-download" class="btn btn-primary btn-sm file-download" data-id= "' . $delAttach->id . '"> Download </button>';
                            }
                            if ($delAttach->file_extension == 'jpg' || $delAttach->file_extension == 'png') {
                                return '<a href="#" class="pop img-hover-zoom--brightness"> <img style= "width:50px; height:50px;" src="http://localhost/itdi-purchase-request/web/uploads/pr_delivery_files/' . $filename[2] . '"></a>';
                            }
                            
                        },
                    ],
                ],
            ]
        ]);
        ?>
    </div>
</div>



<?php
$this->registerJs(
    <<<JS
  
  $('.modalfilebtn').on("click", function(){
        $('#modalfile').modal("show");
        $.get($(this).val(), function(data){
            $('#modalFile2').html(data);
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
