<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


?>


<div class="pr-disapproval">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($itemHistoryLog, 'action_remarks')->textArea() ?>

  <div class="form-group">
    <p>
      <!-- <= Html::button('Save', ['class' => 'btn btn-success savedBtn']) ?> -->
     <?= Html::button('Save', ['value'=> $_GET['id'], 'class' => 'btn btn-success savedBtn']) ?>
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
            url: "bac-revisionsaved",
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