<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


<div class="modal-header design">
  <h5 class="modal-title" id="modal-bulletin-label">  ADD CHANGES FOR THE ITEM 
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>
<div class="pr">
  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($itemSpec, 'description')->textarea(['readonly' => true, 'rows' => '6'])->label('Equipment Technical Specification') ?>

  <?= $form->field($itemSpec, 'bidbulletin_changes')->textarea()->label('Equipment Technical Specification Changes') ?>

  <div class="form-group">
    <p>
      <center>
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </center>
    </p>
  </div>


  <?php ActiveForm::end(); ?>
</div>

<style>
  .pr {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }

  .design {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #12B359;
  }
</style>