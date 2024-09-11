<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;



?>

<div class="pr-generate">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <center>
        <div class="col-md-12 col-sm-12">
            <?= $form->field($model, 'id')->hiddenInput(['value' => Yii::$app->request->get('id')])->label(false); ?>
        </div>

        <div class="row">
            <div class="col-md-12 col-sm-12">
                <!-- Display resolution number -->
                <?= $form->field($model, 'resolution_no')->textInput(['value' => $resolutionNo, 'readonly' => true, 'style' => 'text-align:center'])->label('Resolution Number:') ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 col-sm-12">
                <?= $form->field($model, 'resolution_date')->widget(
                    DatePicker::className(),
                    [
                        'options' => ['placeholder' => 'Select date'],
                        'pluginOptions' => [
                            'autoclose' => true,
                            'format' => 'yyyy-mm-dd',
                            'todayHighlight' => true,
                            'orientation' => 'bottom',
                        ]
                    ],
                ); ?>
            </div>
        </div>
    </center>

    <div class="form-group">
        <div style="text-align:center">
            <p>
                <?= Html::submitButton('Generate', ['target' => '_blank', 'class' => 'btn btn-info btn-sm', 'id' => $model->id]) ?>
            </p>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
