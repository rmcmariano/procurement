<?php

use kartik\grid\GridView;
use app\modules\user\models\Profile;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */

$this->params['breadcrumbs'][] = ['label' => 'Home', 'url' => Yii::$app->request->referrer];
$this->params['breadcrumbs'][] = $this->title;


?>

<div class="purchase-request-index">
  <div class="box box-primary">
    <div class="box-header with-border">
      <i>PR Number:</i>
      <h2 style="text-align: left"> <?= ((in_array($model->status, ['1', '8', '9', '3'])) ? $model->temp_no : $model->pr_no); ?> </h2>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

      <center>
        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Generate History Report', ['generate-report/prlogs-report', 'id' => $model->id,], ['class' => 'btn btn-warning']) . ' '; ?>
      </center>
      <p>
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
          'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
        ],
        'striped' => true,
        'hover' => true,
        'export' => false,
        'panel' => ['type' => 'default',],
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => '5'],
        'columns' => [

          [
            'attribute' => 'action_date',
            'header' => 'PROCESS DATE',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'options' => ['style' => 'width:15%'],
            'value' => function ($historylog) {
              return Yii::$app->formatter->asDatetime(strtotime($historylog->action_date), 'php:d-M-Y | h:i');
            },
          ],
          [
            'attribute' => 'action_user_id',
            'header' => 'ACTION BY',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'value' => function ($v, $model) {
              $name = Profile::find()->where(['user_id' => $v->action_user_id])->one();
              return ($name == NULL ? '' : $name->fname . ' ' .  $name->lname);
            },
            'options' => ['style' => 'width:20%'],
          ],
          [
            'attribute' => 'action_status',
            'header' => 'STATUS',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'value' => 'trackstatus.status',
            'options' => ['style' => 'width:20%'],
          ],
          [
            'attribute' => 'remarks',
            'header' => 'REMARKS',
            'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
            'options' => ['style' => 'width:30%'],
          ],
        ]
      ]);
      ?>
    </div>
  </div>
</div>