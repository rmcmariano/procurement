<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cashier\models\CheckType */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="row row-no-gutters">
    <div class="col-xs-12 col-md-offset-3 col-md-6">
        <div class="check-type-form box box-primary">
            <?php $form = ActiveForm::begin(); ?>
            <div class="box-body table-responsive">

                        <?= $form->field($model, 'check_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

            </div>
            <div class="box-footer">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success btn-flat']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>