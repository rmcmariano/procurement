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

  <br>
  <?php if (Yii::$app->controller->action->id == 'receive-view') { ?>
    <h3>Recieve Form</h3>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
      <div class="col-sm-2">
        <h5><b>Purchace Request Number:</b></h5>
        <?= $form->field($model, 'pr_no')->textInput(['required' => true, 'readOnly' => true])->label(false) ?>
      </div>
      <div class="col-sm-3">
        <h5><b>Type / Action Done</b></h5>
        <?= $form->field($pr_tracking, 'type_id')->dropDownList(
          ArrayHelper::map(TypeOfAction::find()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
          [
            'prompt' => 'Select...',
          ]
        )->label(false) ?>
      </div>
      <div class="col-sm-2">
        <h5><b>Date Received</b></h5>
        <?= $form->field($pr_tracking, 'date_time_received')->textInput(['type' => 'date', 'max' => date('Y-m-d'), 'required' => true, 'id' => 'disable-keyboard'])->label(false) ?>
      </div>
      <div class="col-sm-2">
        <h5><b>Time Received</b></h5>
        <?= $form->field($pr_tracking, 'time')->textInput(['type' => 'time', 'required' => true])->label(false) ?>
      </div>
      <div class="col-sm-3">
        <h5><b>Action</b></h5>
        <?= Html::submitButton('Receive', ['class' => 'btn btn-success save-button']) ?>
      </div>
    </div>
    <?php ActiveForm::end(); ?>

    <br>

    <h3>Received Logs</h3>
    <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'columns' => [
        [
          'label' => 'Type / Action Done',
          'attribute' => 'type_id',
          'value' => function ($model) {
            $typeOfAction = TypeOfAction::find()->where(['id' => $model->type_id])->one();

            return $typeOfAction ? $typeOfAction->name : 'No Action';
          }
        ],
        [
          'label' => 'Date Received',
          'attribute' => 'date_time_received',
          'value' => function ($model) {

            if ($model && $model->date_time_received) {
              $dateString = $model->date_time_received;
              list($date, $time) = explode(' ', $dateString);

              $date_time_received = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
              return $date_time_received;
            }

            return 'No Action';
          }
        ],
        [
          'label' => 'Received By',
          'attribute' => 'received_by',
          'value' => function ($model) {
            $profile = Profile::find()->where(['user_id' => $model->received_by])->one();
            $created_by = $profile->lname . ', ' . $profile->fname . ' ' . $profile->mi . '.';

            return $profile ? $created_by : "";
          }
        ],
      ],
    ]) ?>
  <?php } else {
  } ?>
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