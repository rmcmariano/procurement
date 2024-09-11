<?php

use app\modules\user\models\Profile;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\BacSignatories */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="bac-signatories-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <?= $form->field($model, 'bid_id')->hiddenInput(['value' => $_GET['id']])->label(false) ?>
                <div class="col-md-6 col-sm-12">
                    <?= $form->field($model, 'chairperson_id')->dropDownList(
                        ArrayHelper::map(Profile::find()->where(['user_id' => $usersWithRole])->all(), 'user_id', 'fullname'),
                        ['id' => 'type', 'prompt' => 'Select Chairperson', 'readonly' => ($model->chairperson_id == '' || NULL ? false : true)]
                    ) ?>
                </div>

                <div class="col-md-6 col-sm-12">
                    <?= $form->field($model, 'co_chairperson_id')->dropDownList(
                        ArrayHelper::map(Profile::find()->where(['user_id' => $usersWithRole])->all(), 'user_id', 'fullname'),
                        ['id' => 'type', 'prompt' => 'Select',]
                    ) ?>
                </div>
            </div>

            <!-- Dynamic Form - Start -->
            <div class="form-group">
                <div class="row">
                    <div class="panel-body">

                        <?php DynamicFormWidget::begin([
                            'widgetContainer' => 'dynamicform_wrapper2',
                            'widgetBody' => '.container-items2',
                            'widgetItem' => '.item2',
                            'limit' => 8,
                            'min' => 1,
                            'insertButton' => '.add-item2',
                            'deleteButton' => '.remove-item2',
                            'model' => $modelmember_signatories[0],
                            'formId' => 'dynamic-form',
                            'formFields' => [
                                'members_id',
                            ],
                        ]); ?>


                        <div class="panel panel-default">
                            <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
                                <h3 class="panel-title pull-left">Add Member Signatories</h3>
                                <button type="button" class="pull-right add-item2 btn btn-success btn-xs" style="position:relative; left: -4px; margin-top:5px;"><i class="glyphicon glyphicon-plus"></i>ADD</button>
                                <div class="clearfix"></div>

                            </div>


                            <!-- subdata -->
                            <div class="container-items2">
                                <?php foreach ($modelmember_signatories as $i => $modelmembersignatories) : ?>
                                    <div class="item2">
                                        <button type="button" class="pull-right remove-item2 btn btn-danger btn-xs" style="position:absolute; right: 100px; margin-top:35px;"><i class="glyphicon glyphicon-minus"></i>
                                        </button>

                                        <div class="box-body">
                                            <div class="clearfix">
                                            </div>
                                            <?php
                                            // necessary for update action.
                                            if (!$modelmembersignatories->isNewRecord) {
                                                echo Html::activeHiddenInput($modelmembersignatories, "[{$i}]id");
                                            }
                                            ?>
                                            <div class="row">
                                                <div class="col-md-9 col-sm-12">
                                                    <?= $form->field($modelmembersignatories, "[{$i}]members_id")->dropDownList(
                                                        ArrayHelper::map(Profile::find()->where(['user_id' => $usersWithRole])->all(), 'user_id', 'fullname'),
                                                        ['id' => 'type', 'prompt' => 'Select',]
                                                    ) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php DynamicFormWidget::end(); ?>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div style="text-align:center">
                        <?= Html::submitButton('<span class="glyphicon glyphicon-print"></span> Abstract', ['target' => '_blank', 'class' => 'btn btn-info btn-sm']) ?>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
                    </div>
                </div>

                <?php ActiveForm::end(); ?>

            </div>
        </div>
    </div>
</div>