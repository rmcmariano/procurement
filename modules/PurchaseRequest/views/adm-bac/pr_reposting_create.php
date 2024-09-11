<?php

use app\modules\PurchaseRequest\models\DateOptions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Quotation */
/* @var $form yii\widgets\ActiveForm */

?>


<div class="reposting-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <div class="panel-body">
                <div class="row">
                    <?= $form->field($quotationReposting, 'pr_id')->hiddenInput(['value' => $prItem->pr_id, 'readonly' => true])->label(false)  ?>
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotation, 'quotation_no')->textInput(['value' => $quotation->quotation_no, 'readonly' => ($quotation->quotation_no == '' || NULL ? false : true)])  ?>
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotationReposting, 'option_id')->dropDownList(
                            ArrayHelper::map(DateOptions::find()->where(['id' => ['6', '8']])->all(), 'id', 'options'),
                            ['id' => 'test', 'prompt' => 'Select',]
                        ) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($quotationReposting, 'date')->widget(
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
                        <?= $form->field($quotationReposting, 'time')->textInput(['type' => 'time']) ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <?= $form->field($quotationReposting, 'remarks')->textarea(['rows' => 6, 'value' => '']) ?>
                    </div>
                </div>

                <div class="row">
                    <div style="text-align:center">
                        <p>
                            <?= Html::submitButton('Set Schedule Details', ['value' => $_GET['id'], 'class' => 'btn btn-primary']) ?> &nbsp;
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<style>
    .quotation-form {
        font-size: smaller;
    }
</style>