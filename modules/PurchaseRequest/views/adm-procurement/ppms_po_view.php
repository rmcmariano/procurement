<?php


use kartik\form\ActiveForm;
use yii\helpers\Html;

// $this->params['breadcrumbs'][] = ['label' => 'Home', 'url' => ['procurement-prindex']];

?>

<div class="modal-header design">
  <h5 class="modal-title" id="modal-bulletin-label"> View
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </h5>
</div>

<div class="purchaseorder-view">
  <?php $form = ActiveForm::begin(); ?>
  <left>
    <h4><i><strong> Details</strong></i></h4>
  </left>
  <table class="table table-responsive">
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Date of P.O. / W.O.: </i></strong></td>
      <td style=" text-align: left;"> <?= Yii::$app->formatter->asDatetime(strtotime($modelPurchaseorder->po_date_created), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> P.O. No. / W.O. No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_no ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> P.R. No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaserequest->pr_no ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> ORS / BURS No.: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->ors_burs_num ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Date of ORS / BURS: </i></strong></td>
      <td style=" text-align: left;"> <?= Yii::$app->formatter->asDatetime(strtotime($modelPurchaseorder->date_ors_burs), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Supplier Name: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->supplierdisplay->supplier_name ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Place of Delivery: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->place_delivery ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Date of Delivery: </i></strong></td>
      <td style=" text-align: left;"> <?= Yii::$app->formatter->asDatetime(strtotime($modelPurchaseorder->date_delivery), 'php: M d, Y') ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Payment Term: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->payment_term ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Status: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->po_status ?> </td>
    </tr>
    <tr>
      <td style=" text-align: left; width: 35%"><strong><i> Created By: </i></strong></td>
      <td style=" text-align: left;"> <?= $modelPurchaseorder->created_by ?> </td>
    </tr>
  </table>

  <?php ActiveForm::end(); ?>
</div>


<style>
  .purchaseorder-view {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
    font-size: smaller;
  }

  .design {
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;
    background-color: #12B359;
  }
</style>