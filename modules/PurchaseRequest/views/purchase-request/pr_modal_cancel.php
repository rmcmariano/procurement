<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<div class="modal-header cancelPr">
  <h5 class="modal-title" id="modal-PrCancel"> Cancel PR
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="modalCancelPr">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($itemHistoryLog, 'action_remarks')->textArea()->label('Remarks') ?>

  <div class="form-group">
    <p>
      <center>
        <?= Html::button('Submit', ['value' => $_GET['id'], 'class' => 'btn btn-info']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </center>
    </p>
  </div>
  <?php ActiveForm::end(); ?>
</div>

<style>
  .modalCancelPr {
    font-size: smaller;
  }

  .modalCancelPr {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }

  .cancel {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #18B6D5;
  }
</style>

