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
  <?php if (Yii::$app->controller->action->id == 'release-view' && $pr_tracking->status == 1) { ?>
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
            'disabled' => true,
          ]
        )->label(false) ?>
      </div>
      <div class="col-sm-2">
        <h5><b>Date Released</b></h5>
        <?= $form->field($pr_tracking, 'date_time_released')->textInput(['type' => 'date', 'max' => date('Y-m-d'), 'required' => true, 'id' => 'disable-keyboard'])->label(false) ?>
      </div>
      <div class="col-sm-2">
        <h5><b>Time Released</b></h5>
        <?= $form->field($pr_tracking, 'time')->textInput(['type' => 'time', 'required' => true])->label(false) ?>
      </div>
      <div class="col-sm-3">
        <h5><b>Action</b></h5>
        <?= Html::submitButton('Release', ['class' => 'btn btn-success save-button']) ?>
      </div>
    </div>
    <?php ActiveForm::end(); ?>

    <br>
  <?php } else {
  } ?>
  <h3>Released Logs</h3>
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
        'label' => 'Date Released',
        'attribute' => 'date_time_released',
        'value' => function ($model) {

          if ($model && $model->date_time_released) {
            $dateString = $model->date_time_released;
            list($date, $time) = explode(' ', $dateString);

            $date_time_released = date('F j, Y', strtotime($date)) . ' - ' . date('g:i A', strtotime($time));
            return $date_time_released;
          }

          return 'No Action';
        }
      ],
      [
        'label' => 'Released By',
        'attribute' => 'released_by',
        'value' => function ($model) {
          $profile = Profile::find()->where(['user_id' => $model->released_by])->one();
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