<?php

use kartik\file\FileInput;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Attachments */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attachments-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'dynamic-form']]) ?>

    <?= $form->field($model, 'id')->hiddenInput(['value' => $_GET['id']])->label(false); ?>
    <?= $form->field($historylog, 'remarks')->textarea()->label('Remarks:'); ?>
    

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
                'model' => $modelAttach[0],
                'formId' => 'dynamic-form',
                'formFields' => [
                    'files'
                ],
            ]); ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4> Other Attachments <button type="button" class="pull-right add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button> </h4>
                </div>
                <div class="box-body">
                    <div class="container-items1">
                        <?php foreach ($modelAttach as $i => $attach) : ?>
                            <br>
                            <div class="item1">
                                <div class="clearfix">
                                    <?php
                                    if (!$attach->isNewRecord) {
                                        echo Html::activeHiddenInput($attach, "[{$i}]id");
                                    }
                                    $initialPreview = [];
                                    ?>
                                    <div class="col-sm-11">

                                        <?= $form->field($attach, "[{$i}]file_name")->widget(FileInput::classname(), [
                                            'options' => [
                                                'id' => 'imageFile',
                                                'multiple' => false,
                                                'accept' => 'image/*, .pdf, .docx'
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