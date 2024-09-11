<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


<div class="pr-disapproval">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($bidding, 'item_id')->hiddenInput(['value' => $description->id, 'readonly' => true])->label(false)  ?>

  <?= $form->field($itemHistorylog, 'action_remarks')->textInput(['value' => $bidding->supplierdisplay->supplier_name, 'readonly' => true])->label('Suppliers Name:') ?>
  <?= $form->field($description, 'item_name')->textInput(['value' => $description->item_name, 'readonly' => true])->label('Item/Equipment Name:') ?>
  <?= $form->field($bidding, 'supplier_price')->textInput(['value' => 'Php ' . number_format($bidding->supplier_price, '2'), 'readonly' => true])->label('Bidding Amount:') ?>

  <?= $form->field($bidding, 'yourCheckboxAttribute')->checkbox([
    'label' => '<span style="color: red;"><strong><i>Check if the item has different Bid Offers</i></strong></span>',
    'checked' => $bidding->status == '12', // Set checked based on the value of the 'status' attribute
    'value' => $bidding->id,
]) ?>


  <!-- <= $form->field($model, 'yourCheckboxAttribute')->checkbox(['label' => '<span style="color: red;"><strong> <i>Check if the item has different Bid Offers</i></strong></span>', 'value' => $model->id]) ?> -->
  <!-- <input type="checkbox" class="check_item" /> <span style="color: red;"><strong> <i>Check if the item has different Bid Offers</i></strong></span> -->

  <div class="form-group">
    <div style="text-align:center">
      <p>
        <!-- bac controller actionSaved -->
        <?= Html::button('Comply', ['value' => $_GET['id'], 'class' => 'btn btn-success btn-sm complyBtn']) ?>
        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
      </p>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>


<?php
$this->registerJs(
  <<<JS

//sweetalert 
$('.complyBtn').on('click', function() {
    var idToSubmit = $(this).val();

    swal({
        title: "Confirm to Comply",
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
            url: "/procurement/web/PurchaseRequest/bidding/bac-biddingcomply-saved",
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


JS
);
?>