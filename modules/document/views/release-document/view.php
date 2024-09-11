<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;

$this->title = 'For-Release Document Details';
if ($_GET['type'] == 'IC'){
$this->params['breadcrumbs'][] = ['label' => 'For-Release Document', 'url' => ['index?type=IC']];
}
if ($_GET['type'] == 'I'){
$this->params['breadcrumbs'][] = ['label' => 'For-Release Document', 'url' => ['index?type=I']];
}
if ($_GET['type'] == 'O'){
$this->params['breadcrumbs'][] = ['label' => 'For-Release Document', 'url' => ['index?type=O']];
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
        </table>
      </div>
    </div>
    <hr style="margin-top: -5px !important; border-bottom: 2px solid #fbc02d;"/>

      <div class="form-group">
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($send, 'section')->widget(Select2::classname(),[
            'name' => 'kv_theme_bootstrap_2',
            'data' => $section,
            // 'theme' => Select2::THEME_BOOTSTRAP,
            'showToggleAll' => false,
            'options' => [
              'placeholder' => 'Select Group/s', 'multiple' => true, 'autocomplete' => 'on', 'style' => 'width: auto'
              ],
              'pluginOptions' => [
                  'allowClear' => true
              ],

        ]);?>
        <?= $form->field($send, 'individual')->widget(Select2::classname(),[
            'name' => 'kv_theme_bootstrap_2',
            'data' => $individual,
            // 'theme' => Select2::THEME_BOOTSTRAP,
            'showToggleAll' => false,
            'options' => [
              'placeholder' => 'Select Recipient/s', 'multiple' => true, 'autocomplete' => 'on', 
              ],
              'pluginOptions' => [
                  'allowClear' => true,

              ],
        ]);?>

        <?= $form->field($files, 'attachment[]')->fileInput(['multiple' => true]) ?>

        <?= $form->field($send, 'remarks')->textInput(['autofocus' => true])->textarea(['rows' => 6]) ?>

        <center><?= Html::submitButton('Release Document', 
                    [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'confirm' => 'Click OK to confirm your action.',
                            'method' => 'post',
                        ],
                    ])?>
        </center>

        <?php $form = ActiveForm::end(); ?>
    </div>
  </div>

</div>
