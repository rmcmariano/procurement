<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>


<div class="modal-header request">
  <h5 class="modal-title" id="modal-bulletin-label"> Request Form
    <button type="button" class="close" id="closing-<?= $model ?>" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="requestIar">
  <?php $form = ActiveForm::begin(); ?>

  <?= $form->field($itemSpec, 'bidbulletin_changes')->textarea(['readonly' => true, 'rows' => '6'])->label('Item Specification') ?>

  <?= $form->field($itemSpec, 'request_changes')->textarea()->label('Item Specification Changes') ?>

  <div class="form-group">
    <p>
      <center>
        <?= Html::submitButton('Request', ['class' => 'btn btn-warning']) ?>
        <!-- <button type="button" class="btn btn-default" id="closing-<?= $model ?>">Close</button> -->
      </center>
    </p>
  </div>


  <?php ActiveForm::end(); ?>
</div>
<?php
$this->registerJs(
  <<<JS

    $("#closing-$model").on("click", function() {
        $("#modal-bulletin-$model").modal("hide");
      });
JS
);
?>
<style>
  .requestIar {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }

  .request {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #EA7E18;
  }
</style>