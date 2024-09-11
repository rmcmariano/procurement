<?php

use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use app\modules\PurchaseRequest\models\PrItems;
use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;


?>

<div class="modal-header pardesign">
  <h5 class="modal-title" id="modal-par-label"> View
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="par-view">
  <?php $form = ActiveForm::begin(); ?>

  <div>
    <h4><i><strong> Property Acknowledgement Receipt</strong></i>
  </div>

  <table class="table table-responsive; font-size: small; ">
  <tr>
      <td style=" text-align: left; width: 30%"><strong><i> PAR No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPar == NULL ? ' - ' : $modelPar->par_no ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date Acquire: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPar == NULL ? ' - ' : Yii::$app->formatter->asDatetime(strtotime($modelPar->date_acquire), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> IAR No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->iar_number ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of IAR: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : Yii::$app->formatter->asDatetime(strtotime($modelIar->iar_date), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> P.O. No. / W.O. No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_no ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of P.O. / W.O.: </i></strong></td>
      <td style=" text-align: left;"> <?= Yii::$app->formatter->asDatetime(strtotime($modelPurchaseorder->po_date_created), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Supplier Name: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->supplierdisplay->supplier_name ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Sales Invoice Number: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->sales_invoice_number ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Date of Sale Invoice: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : Yii::$app->formatter->asDatetime(strtotime($modelIar->sales_invoice_date), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Estimated Useful Life: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPar == NULL ? ' - ' : $modelPar->estimated_useful_life ?> </td>
     </tr>
    <tr>
      <td style=" text-align: left; width: 30%"><strong><i> Status: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelIar == NULL ? ' - ' : $modelIar->iar_status ?> </td>
      <td style=" text-align: left; width: 30%"><strong><i> Created By: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->created_by ?> </td>
    </tr>
  </table>

  <?php ActiveForm::end(); ?>
</div>


<style>
  .par-view {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
    font-size: small;
  }

  .pardesign {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #12B359;
  }

  .modal-content {
    border-radius: 20px;
  }
</style>

