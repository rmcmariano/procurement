<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use app\models\profile\Profile;
use app\modules\document\models\Document;
use app\modules\document\models\TypeOfAction;
?>

<div class="pr-view">
  <?= DetailView::widget([
    'model' => $model,
    'attributes' => [
      [
        'label' => 'Purchase Request Number',
        'attribute' => 'pr_no'
      ],
      [
        'label' => 'Created By',
        'attribute' => 'created_by',
        'value' => function ($model) {
          $profile = Profile::find()->where(['user_id' => $model->created_by])->one();
          $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';

          return $profile ? $created_by : "";
        }
      ],
      [
        'label' => 'Date Created',
        'attribute' => 'date_of_pr'
      ],
    ],
  ]) ?>
  <h3>Recieve Form</h3>
  <?php $form = ActiveForm::begin(); ?>
  <div class="row">
    <div class="col-sm-3">
      <h5><b>Purchace Request Number:</b></h5>
      <h5><b><?= $model->pr_no ?></b></h5>
    </div>
    <div class="col-sm-3">
      <h5><b>Type / Action Done:</b></h5>
      <?= $form->field($pr_tracking, 'type')->dropDownList(
        ArrayHelper::map(TypeOfAction::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
        [
          'prompt' => 'Select...',
        ]
      )->label(false) ?>
    </div>
    <div class="col-sm-3">
      <h5><b>Date Recieved:</b></h5>
      <?= $form->field($pr_tracking, 'date_time')->textInput(['type' => 'date', 'max' => date('Y-m-d'), 'required' => true, 'id' => 'disable-keyboard'])->label(false) ?>
    </div>
    <div class="col-sm-3">
      <h5><b>Time Recieved:</b></h5>
      <?= $form->field($pr_tracking, 'time')->textInput(['type' => 'time', 'required' => true])->label(false) ?>
    </div>
    <div class="col-sm-3">
      <h5><b>Action:</b></h5>
      <?= Html::submitButton('Recieve', ['class' => 'btn btn-success save-button']) ?>
    </div>
  </div>
  <?php ActiveForm::end(); ?>

  <h3>Received Logs</h3>
  <?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
      [
        'label' => 'Type / Action Done',
        'attribute' => 'type',
        'value' => function ($model) {
          $typeOfAction = TypeOfAction::find()->where(['id' => $model->type])->one();

          return $typeOfAction ? $typeOfAction->name : 'No Action';
        }
      ],
      [
        'label' => 'Date Recieved',
        'attribute' => 'date_time',
        'value' => function ($model) {

          if ($model && $model->date_time) {
            $dateString = $model->date_time;
            list($date, $time) = explode(' ', $dateString);

            $date_time = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
            return $date_time;
          }

          return 'No Action';
        }
      ],
      [
        'label' => 'Received By',
        'attribute' => 'action_by',
        'value' => function ($model) {
          $profile = Profile::find()->where(['user_id' => $model->action_by])->one();
          $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';

          return $profile ? $created_by : "";
        }
      ],
    ],
  ]) ?>
</div>
<?php
$this->registerJs(
  <<<JS

      $('#disable-keyboard').on('keydown', function(e) {
          e.preventDefault();
      });

    JS
);
?>
<style>
  .pr-view {
    padding-bottom: 1rem;
    padding: 1rem;
    border-radius: 1rem;
    background-color: white;
    display: inline-block;
    width: 100%;
  }
</style>