<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


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