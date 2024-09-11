<?php

use app\modules\user\models\Profile;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseOrder */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="modal-header iar">
  <h5 class="modal-title" id="modal-bulletin-label"> Create IAR
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="iarForm">

  <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

  <table class="table table-responsive">
    <tr>
      <td style=" text-align: left; width: 20%"><strong> Purchase Order No.: </strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_no ?> </td>
      <td style=" text-align: left; width: 20%"> <strong> Supplier's Name: </strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->supplierdisplay->supplier_name ?></td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 20%"> <strong> P.O. Dated: </strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_date_created ?> </td>
      <td style=" text-align: left; width: 20%"> <strong> Address: </strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->supplierdisplay->supplier_address  ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 20%"> <strong> Requisitioning Office/Dept.: </strong></td>
      <td style=" text-align: left;"><?= $purchaseRequest->sectiondisplay->section_code ?></td>
      <td style=" text-align: left; width: 20%"> <strong> TIN No.: </strong></td>
      <td style=" text-align: left;"><?= $modelPurchaseorder->supplierdisplay->tin_no ?></td>
    </tr>
  </table>
  <hr style="border-bottom: 1px solid #77ccff;" />
  <left>
    <h5><i><strong> Input Details for IAR </strong></i></h5>
  </left>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <?= $form->field($iarModel, 'iar_number')->label('IAR Number:') ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($iarModel, 'iar_date')->widget(
        DatePicker::className(),
        [
          // inline too, not bad
          'inline' => false,
          // modify template for custom rendering
          // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
          'options' => ['id' => 'date4', 'value' => ''],
          'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
          ],
        ]
      ); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <?= $form->field($iarModel, 'sales_invoice_number')->label('DR Number:') ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($iarModel, 'sales_invoice_date')->widget(
        DatePicker::className(),
        [
          // inline too, not bad
          'inline' => false,
          // modify template for custom rendering
          // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
          // 'options' => ['id' => 'date4', 'value' => ''],
          'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
          ],
        ]
      )->label('DR Date:'); ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <?= $form->field($iarModel, 'inspector_id')->dropDownList(
        Profile::filterAdmin(),
        ['id' => 'type', 'prompt' => 'Select',]
      ) ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($iarModel, 'inspection_date')->widget(
        DatePicker::className(),
        [
          // inline too, not bad
          'inline' => false,
          // modify template for custom rendering
          // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
          // 'options' => ['id' => 'date4', 'value' => ''],
          'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'todayHighlight' => true,
          ],
        ]
      ); ?>
    </div>
  </div>

  <div style="text-align:center">
    <?= Html::submitButton('Save', ['class' => 'btn btn-info']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
  <?php ActiveForm::end(); ?>
</div>



<style>
  .iarForm {
    font-size: smaller;
  }

  .iarForm {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }

  .iar {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #18B6D5;
  }
</style>