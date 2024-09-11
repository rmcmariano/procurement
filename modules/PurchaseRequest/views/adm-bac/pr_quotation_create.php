<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use app\modules\PurchaseRequest\models\DateOptions;

?>

<div class="quotation-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="panel panel-default">
        <div style="padding: 20px">
          
                <div class="row">

                    <?= $form->field($quotation, 'pr_id')->hiddenInput(['value' => $purchaserequest->id, 'readonly' => true])->label(false)  ?>

                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotation, 'quotation_no')->textInput(['value' => $quotation->quotation_no, 'readonly' => ($quotation->quotation_no == '' || NULL ? false : true)])->label('Solicitation Number:')  ?>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotation, 'option_id')->dropDownList(
                            ArrayHelper::map(DateOptions::find()->where(['<>', 'id', 6])->all(), 'id', 'options'),
                            ['id' => 'test', 'prompt' => 'Select',]
                        )->label('Select: ') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotation, 'date')->widget(
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
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotation, 'time')->textInput(['type' => 'time']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <?= $form->field($quotation, 'remarks')->textarea(['rows' => 6, 'value' => '']) ?>
                    </div>
                </div>
        
        </div>



        <div class="row">
            <div style="text-align:center">
                <p>
                    <?= Html::submitButton('Set Schedule Details', ['id' => $quotation->id, 'class' => 'btn btn-primary']) ?> &nbsp;
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </p>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
</div>
</div>

<style>
    .quotation-form {
        font-size: smaller;
    }
</style>