<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>


<div class="pr-modal-remarks">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($itemHistoryLog, 'action_remarks')->textArea()->label('Remarks') ?>

  <div class="form-group">
    <p>
      <center>
        <?= Html::button('Submit', ['value' => $_GET['id'], 'class' => 'btn btn-info savedBtn']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </center>
    </p>
  </div>
  <?php ActiveForm::end(); ?>
</div>


<?php
$this->registerJs(
  <<<JS

//sweetalert 
$('.savedBtn').on('click', function() {
    var idToSubmit = $(this).val();
    // console.log(idToSubmit);
    swal({
        title: "Confirm to Saved",
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
            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/purchaserequest-itemcancelsaved",
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

JS
);
?>