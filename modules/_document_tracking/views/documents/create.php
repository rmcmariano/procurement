<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Create Document';
// if ($_GET['type'] == 'IC'){
// $this->params['breadcrumbs'][] = ['label' => 'Internal Communication Documents', 'url' => ['index?type=IC']];
// }
// if ($_GET['type'] == 'I'){
// $this->params['breadcrumbs'][] = ['label' => 'Incoming Documents', 'url' => ['index?type=I']];
// }
// if ($_GET['type'] == 'O'){
// $this->params['breadcrumbs'][] = ['label' => 'Outgoing Documents', 'url' => ['index?type=O']];
// }
$this->params['breadcrumbs'][] = $this->title;

?>  
<div class="box box-primary">
    <div class="box-header with-border">
        <strong><h4>Please enter Document Details below:</h4></strong>

        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
            <div class="row">
                <div class="col-xs-4">
                    <?= $form->field($document_details, 'document_type')->dropDownList(['1' => 'Internal Communication', '2' => 'Incoming', '3' => 'Outgoing'], ['prompt' => 'Select document type']); ?>
                </div>
                <div class="col-xs-8">
                    <?= $form->field($document_details, 'title') ?>
                </div>
            </div>
            <?= $form->field($document_details, 'details')->textInput(['autofocus' => true])->textarea(['rows' => 6]) ?>
            <?= Html::submitButton('Submit' , 
                [
                    'class' => 'btn btn-primary',
                    'data' => [
                        'confirm' => 'Click OK to confirm your action.',
                        'method' => 'post',
                    ]
                ])?>
        <?php $form = ActiveForm::end(); ?>

    </div>
</div>


