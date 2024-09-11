<?php

use app\modules\user\models\Profile;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseOrder */
/* @var $form yii\widgets\ActiveForm */


?>
<div class="modal-header ics">
  <h5 class="modal-title" id="modal-bulletin-label"> Create ICS
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="icsForm">
  <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

  <table class="table table-responsive">
    <tr>
      <td style=" text-align: left; width: 20%"><strong> Purchase Order No.: </strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_no ?> </td>
      <td style=" text-align: left; width: 20%"> <strong> P.O. Dated: </strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_date_created ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 20%"> <strong> Invoice No.: </strong></td>
      <td style=" text-align: left;"> <?= $iarModel == NULL ? '-' : $iarModel->sales_invoice_number?> </td>
      <td style=" text-align: left; width: 20%"> <strong> Invoice Date: </strong></td>
      <td style=" text-align: left;"> <?= $iarModel == NULL ? '-' : $iarModel->sales_invoice_date  ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 20%"> <strong> Fund Cluster:  </strong></td>
      <td style=" text-align: left;"><?= $purchaseRequest->chargedisplay->project_title ?></td>
    </tr>
  </table>
  <hr style="border-bottom: 1px solid #77ccff;" />
  <left>
    <h5><i><strong> Input Details for ICS </strong></i></h5>
  </left>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <?= $form->field($icsModel, 'ics_no')->label('ICS Number:') ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($icsModel, 'date_acquire')->widget(
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
      <?= $form->field($icsModel, 'inventory_item_no')->label('Inventory Item No.:') ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($icsModel, 'estimated_useful_life')->label('Estimated Useful Life:') ?>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 col-sm-12">
      <?= $form->field($icsModel, 'received_from')->dropDownList(
        Profile::filterAdmin(),
        ['id' => 'type', 'prompt' => 'Select',]
      ) ?>
    </div>
    <div class="col-md-6 col-sm-12">
      <?= $form->field($icsModel, 'received_by')->dropDownList(
        Profile::filterAdmin(),
        ['id' => 'type', 'prompt' => 'Select',]
      ) ?>
    </div>
  </div>

  <div style="text-align:center">
    <?= Html::submitButton('Save', ['class' => 'btn btn-info']) ?>
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
  </div>
  <?php ActiveForm::end(); ?>
</div>



<style>
  .icsForm {
    font-size: smaller;
  }

  .icsForm {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }

  .ics {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #18B6D5;
  }
</style>