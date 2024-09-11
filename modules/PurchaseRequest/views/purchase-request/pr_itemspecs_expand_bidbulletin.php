<?php


use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;


// Modal::begin([
//     'header' => 'Bid Bulletin',
//     'id' => 'modal-bulletin',
//     'size' => 'modal-lg',
//     'options' => [
//         'data-keyboard' => 'false',
//         'data-backdrop' => 'static'
//     ]
// ]);

// echo "<div id = 'modalBulletin'></div>";
// Modal::end();


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
                    'header' => 'ITEM SPECIFICATION',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:40%; white-space: pre-line'],
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'bidbulletin_changes',
                    'header' => 'BID BULLETIN',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:40%; white-space: pre-line'],
                    'value' => function ($model) {
                        if ($model->bidbulletin_changes == NULL) {
                            return '-';
                        }
                        return $model->bidbulletin_changes;
                    },
                    'format' => 'ntext',
                ],
                [
                    'attribute' => 'bidbulletin_status',
                    'header' => 'STATUS',
                    'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                    'options' => ['style' => 'width:10%'],
                    'hAlign' => 'right',
                    'contentOptions' => ['style' => 'text-align: center'],
                    'value' => function ($model) {
                        // var_dump($model);die;
                        if ($model->bidbulletin_status == NULL) {
                            return '-';
                        }
                        if ($model->bidbulletin_status == 1) {
                            return 'Pending';
                        }
                        if ($model->bidbulletin_status == 2) {
                            return 'Accepted';
                        }
                        if ($model->bidbulletin_status == 3) {
                            return 'Declined';
                        }
                        if ($model->bidbulletin_status == 4) {
                            return 'Revised';
                        }
                        if ($model->bidbulletin_status == 5) {
                            return 'Created Bidbulletin';
                        }
                    }
                ],
                [
                    'class' => 'kartik\grid\ActionColumn',
                    'options' => ['style' => 'width:20%'],
                    'header' => 'ACTIONS',
                    'headerOptions' => ['style' => 'color:#337ab7'],
                    'template' => ' {accept} {cancel}  ',
                    'buttons' => [
                        'accept' => function ($url, $model, $key) {
                            return  Html::button('<span class="glyphicon glyphicon-ok"></span>', ['class' => 'btn btn-warning btn-sm acceptBtn', 'value' => $model['id'], 'title' => 'Accept']);
                        },
                        'cancel' => function ($url, $model, $key) {
                            return  Html::button('<span class="glyphicon glyphicon-remove"></span>', ['class' => 'btn btn-danger btn-sm cancelBtn', 'value' =>  $model["id"], 'title' => 'Decline']);
                        },
                    ],
                    'visibleButtons' => [
                        'accept' => function ($model) {
                            if ($model['bidbulletin_status'] == 4 || $model['bidbulletin_status'] == 1 &&  $model['bidbulletin_changes'] != NULL) {
                                return true;
                            }
                            return false;
                        },
                        'cancel' => function ($model) {
                            // var_dump($model);die;
                            if ($model['bidbulletin_status'] == 4 || $model['bidbulletin_status'] == 1 &&  $model['bidbulletin_changes'] != NULL) {
                                return true;
                            }
                            return false;
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

    // $('.modalBidbulletinbtn').on("click", function(){
    //     $('#modal-bulletin').modal("show");
    //     $.get($(this).val(), function(data){
    //         $('#modalBulletin').html(data);
    //     });
    // });

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
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/acceptbulletin",
            type: "get",
            data: {
              id: $(this).val()
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

    $('.cancelBtn').on('click', function() {
        console.log('test');
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/declinebulletin",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                id: $(this).val()
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

JS
);
?>

<script>
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
                        url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/acceptbulletin",
                        type: "get",
                        data: {
                            id: $(this).val()
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

    $('.cancelBtn').on('click', function() {
        console.log('test');
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
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/declinebulletin",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                id: $(this).val()
                            }
                        });
                        // console.log(willDisapprove);
                        location.reload();
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
</script>