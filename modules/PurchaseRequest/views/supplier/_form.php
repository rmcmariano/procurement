<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

$classification = [
    1 => 'PhilGEPS',
    2 => 'Non-PhilGEPS',
];

$businessType = [
    1 => 'Sole Proprietorship',
    2 => 'Partnership',
    3 => 'Company',
];
?>


<div class="supplier-form">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'dynamic-form', 'autocomplete' => 'off']]) ?>

    <div class="box box-primary">
        <div class="box-header with-border">
            <p>
                <left>
                    <h2>CREATE NEW SUPPLIER</h2>
                </left>

                <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 50px; font-family:Arial, Helvetica, sans-serif;">
                    <h1 class="panel-title pull-left" style="font-size: large; margin-top: 8px">SUPPLIER'S DETAILS</h1>
                    <div class="clearfix"></div>
                </div>
                <p>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'supplier_name')->textInput(['placeholder' => "Enter text here"])->label('Suppliers Name <span style="color:red">*</span>'); ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'supplier_address')->textInput(['placeholder' => "Enter text here"])->label('Suppliers Address <span style="color:red">*</span>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'owner_name')->textInput(['placeholder' => "Enter text here"])->label('Owners Name <span style="color:red">*</span>'); ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'account_no')->textInput(['placeholder' => "Enter text here"])->label('Bank Account No. <span style="color:red">*</span>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'tin_no')->textInput(['placeholder' => "Enter text here"])->label('TIN No. <span style="color:red">*</span>'); ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($model, 'fax_no')->textInput(['placeholder' => "Enter text here"])->label('FAX No. <span style="color:red">*</span>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-12" id="fund">
                            <?= $form->field($model, 'classification_philgeps')->dropDownList(
                                $classification,
                                [
                                    'id' => 'classification-id',
                                    'prompt' => 'Select Classification'
                                ]
                            )->label('Classification <span style="color:red">*</span>'); ?>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <?php DynamicFormWidget::begin([
                                'widgetContainer' => 'dynamicform_wrapper',
                                'widgetBody' => '.container-suppliers',
                                'widgetItem' => '.supplierItem',
                                'limit' => 6,
                                'min' => 1,
                                'insertButton' => '.add-supplier',
                                'deleteButton' => '.remove-supplier',
                                'model' => $modelsuppliercontacts[0],
                                'formId' => 'dynamic-form',
                                'formFields' => [
                                    'assigned_dept',
                                    'contact_person',
                                    'contact_no',
                                ],
                            ]); ?>

                            <div class="panel panel-default">
                                <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
                                    <h3 class="panel-title pull-left">Contact Person Details</h3>
                                    <button type="button" class="pull-right add-supplier btn btn-success btn-xs" style="position:relative; left: -4px; margin-top:5px;"><i class="glyphicon glyphicon-plus"></i>ADD</button>
                                    <div class="clearfix"></div>
                                </div>

                                <div class="box-body">
                                    <div class="container-suppliers">
                                        <!-- subdata -->
                                        <?php foreach ($modelsuppliercontacts as $i => $modelothercontacts) : ?>
                                            <div class="supplierItem">
                                                <button type="button" class="pull-right remove-supplier btn btn-danger btn-xs" style="position:absolute; right: 100px; margin-top:35px;"><i class="glyphicon glyphicon-minus"></i>
                                                </button>

                                                <div class="box-body">
                                                    <div class="clearfix">
                                                    </div>
                                                    <?php
                                                    // necessary for update action.
                                                    if (!$modelothercontacts->isNewRecord) {
                                                        echo Html::activeHiddenInput($modelothercontacts, "[{$i}]id");
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-sm-4">
                                                            <?= $form->field($modelothercontacts, "[{$i}]assigned_dept")->textInput(['maxlength' => true]) ?>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <?= $form->field($modelothercontacts, "[{$i}]contact_person")->textInput(['maxlength' => true]) ?>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <?= $form->field($modelothercontacts, "[{$i}]contact_no")->textInput(['maxlength' => true]) ?>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php DynamicFormWidget::end(); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <center>
                            <?= Html::submitButton($modelothercontacts->isNewRecord ? 'Create' : 'Update', ['class' => 'btn btn-primary']) ?>
                        </center>
                    </div>
                </div>
                </p>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>