<?php

use kartik\file\FileInput;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Attachments */
/* @var $form yii\widgets\ActiveForm */

?>
<div class="modal-header deliveryDesign">
    <h5 class="modal-title" id="modal-file-label"> File Upload
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h5>
</div>

<div class="attachments-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'dynamic-form']]) ?>

    <?= $form->field($model, 'id')->hiddenInput(['value' => $_GET['id']])->label(false); ?>
    <!-- <div class="col-sm-3 col-sm-24">
        <= $form->field($model, 'delivery_receipt_no')->textInput(['maxlength' => true])->label('Delivery Number:') ?>
    </div>
    <div class="col-md-3 col-sm-24">
        <= $form->field($model, 'delivery_amount')->textInput(['maxlength' => true])->label('Delivery Amount: ') ?>
    </div> -->


    <div class="row">
        <div class="col-md-12 col-sm-12">
            <?php DynamicFormWidget::begin([
                'widgetContainer' => 'dynamicform_wrapper1',
                'widgetBody' => '.container-items1',
                'widgetItem' => '.item1',
                'limit' => 5,
                'min' => 1,
                'insertButton' => '.add-item1',
                'deleteButton' => '.remove-item1',
                'model' => $modelDelattachment[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'files'
                ],
            ]); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4> Files <button type="button" class="pull-right add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button> </h4>
                </div>
                <div class="box-body">
                    <div class="container-items1">

                        <?php foreach ($modelDelattachment as $i => $del) : ?>
                            <br>
                            <div class="item1">
                                <div class="clearfix">
                                    <?php
                                    if (!$del->isNewRecord) {
                                        echo Html::activeHiddenInput($del, "[{$i}]id");
                                    }
                                    $initialPreview = [];
                                    ?>
                                    <div class="col-sm-11">
                                        <?= $form->field($del, "[{$i}]file_name")->widget(FileInput::classname(), [
                                            'options' => [
                                                'id' => 'imageFile',
                                                'multiple' => false,
                                                'accept' => '.pdf, .docx'
                                            ],
                                            'pluginOptions' => [
                                                'initialPreviewAsData' => true,
                                                // 'allowedFileExtensions' => ['jpg', 'jpeg', 'png'],
                                                'showPreview' => false,
                                                'showCaption' => true,
                                                'showRemove' => false,
                                                'showUpload' => false,
                                                'browseLabel' => '',
                                                'removeLabel' => '',
                                            ],
                                        ])->label(false) ?>
                                    </div>
                                    <button type="button" class="pull-right remove-item1 btn btn-danger btn-xs" style="position:relative; left: -20px; margin-top:5px;"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </div>

    <div class="row">
        <div style="text-align:center">
            <p>
                <?php
                ?>
                <?= Html::submitButton('Saved', ['id' => $model->id, 'class' => 'btn btn-success']) ?> &nbsp;
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </p>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<style>
  .attachments-form {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
    font-size: small;
  }

  .deliveryDesign {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #18B6D5;
  }

  .modal-content {
    border-radius: 20px;
  }
</style>
