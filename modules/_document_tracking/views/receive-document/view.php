<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;


$this->title = 'For-Receive Document Details';
if ($_GET['type'] == 'IC'){
$this->params['breadcrumbs'][] = ['label' => 'For-Receive Document', 'url' => ['index?type=IC']];
}
if ($_GET['type'] == 'I'){
$this->params['breadcrumbs'][] = ['label' => 'For-Receive Document', 'url' => ['index?type=I']];
}
if ($_GET['type'] == 'O'){
$this->params['breadcrumbs'][] = ['label' => 'For-Receive Document', 'url' => ['index?type=O']];
}
$this->params['breadcrumbs'][] = $this->title;



?>

<div class="box box-primary">

	<div class="box-body">
    <hr style="margin-top: -5px !important; border-bottom: 2px solid #fbc02d;"/>
		<div style="padding: 10px">
      <div class="calibration-view row">
        <table class="table table-responsive">
          <tr>
            <td style="width: 25%; text-align: right">Tracking No:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->tracking_number?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Title:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->title?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Created By:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $full?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Details:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->details?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Date Created:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->date_created ?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Attachments:</td>
            <td><span style="font-size: 15px; font-weight: bold"></span></td>
          </tr>
          <?php foreach ($file_list as $file) {

          $file_name = explode(':', $file);

          ?>
          <tr>
            <td style="width: 25%; text-align: right"></td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $file_name[0] . ' ' . Html::a('Download File',['../document_tracking/receive-document/download', 'file_id' => $file_name[1]])?></span></td>
          </tr>
          <?php } ?>
        </table>
      </div>
    </div>
    <hr style="margin-top: -5px !important; border-bottom: 2px solid #fbc02d;"/>

      <div class="form-group">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($receive, 'tracking_no')->hiddenInput(['value' => $document_details->tracking_number])->label(false) ?>

        <center><?= Html::submitButton('Receive Document', 
                    [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'confirm' => 'Click OK to confirm your action.',
                            'method' => 'post',
                        ]
                    ])?>
        </center>

        <?php $form = ActiveForm::end(); ?>
    </div>
  </div>

</div>
