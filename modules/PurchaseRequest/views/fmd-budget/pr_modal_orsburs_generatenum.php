<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


?>


<div class="pr-disapproval">

  <?php $form = ActiveForm::begin(); ?>

  <center>
    <div class="row">
      <div class="col-md-12 col-sm-12">
        <?= $form->field($model, 'ors_burs_num')->textInput(['style' => 'text-align: center'])  ?>
      </div>
    </div>
  </center>
  <div class="form-group">
    <div style="text-align:center">
      <p>
        <!-- bac controller actionSaved -->
        <!-- <= Html::button('Proceed to PDF', ['value' => $model['id'], 'class' => 'btn btn-info btn-sm resoBtn']) ?> -->
        <?= Html::submitButton('<span class="glyphicon glyphicon-print"></span> Generate',['target' => '_blank', 'class' => 'btn btn-info btn-sm', 'id' => $model['id']]) ?>
      </p>
    </div>
    <?php ActiveForm::end(); ?>
  </div>
</div>

