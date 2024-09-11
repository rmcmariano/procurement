<?php


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
    'header' => 'Bid Bulletin',
    'id' => 'modal-bulletin2',
    'size' => 'modal-lg',
    'options' => [
        'data-keyboard' => 'false',
        'data-backdrop' => 'static'
    ]
]);

echo "<div id = 'modalBulletin2'></div>";
Modal::end();


?>

<div class="pr-subdata-index">
    <div id="ajaxCrudDatatable">

        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'options' => [
                'style' => 'overflow: auto; word-wrap: break-word;'
            ],
            'columns' => [
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'options' => ['style' => 'width:2%'],
                ],

                [
                    'attribute' => 'description',
                    'format' => 'ntext',
                    'header' => 'ITEM SPECIFICATION',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:40%; white-space: pre-line'],
                ],
                [
                    'attribute' => 'bidbulletin_changes',
                    'header' => 'DETAILS FOR BID BULLETIN ',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: left'],
                    'options' => ['style' => 'width:40%; white-space: pre-line'],
                    'value' => function ($model) {
                        if ($model->bidbulletin_changes == NULL) {
                            return '-';
                        }
                        return $model->bidbulletin_changes;
                    }
                ],
            ]
        ]);
        ?>
    </div>
</div>


<?php
$this->registerJs(
    <<<JS

    $('.modalBidbulletinbtn2').on("click", function(){
        $('#modal-bulletin2').modal("show");
        $.get($(this).val(), function(data){
            $('#modalBulletin2').html(data);
        });
    });

    //sweetalert 
    $('.acceptBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Confirm to Accept?",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Saved!", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "/itdi-purchase-request/web/PurchaseRequest/purchase-request/acceptbulletin",
            type: "get",
            data: {
              id: $(this).val()
            },
            
          }); console.log(url);
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            location.reload();
          });
        }
      });
    });
    
    $('.cancelBtn').on('click', function() {
        var remarks = "";
        swal({
            title: "Confirm to Decline?",
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
                            url: "/itdi-purchase-request/web/PurchaseRequest/purchase-request/declinebulletin",
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

JS
);
