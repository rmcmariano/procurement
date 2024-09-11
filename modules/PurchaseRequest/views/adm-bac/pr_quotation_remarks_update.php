<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>

<div class="modal-header remarksModal">
  <h5 class="modal-title" id="modal-remarks-label"> Create
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="remarksForm">
  <?php $form = ActiveForm::begin(); ?>

  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-body">
        <div class="row">
          <?= $form->field($quotation, 'remarks')->textarea(['maxlength' => true])->label('Remarks:')  ?>
        </div>

        <div class="row">
          <div style="text-align:right">
            <?= Html::submitButton('Save', ['id' => $quotation->id, 'class' => 'btn btn-success']) ?> &nbsp; &nbsp;
          </div>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>

<script>
  $('.close').on("click", function() {
    location.reload();
  });
</script>

<style>
  .remarksForm {
    font-size: smaller;
  }

  .remarksForm {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }

  .remarksModal {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #18B6D5;
  }
</style>