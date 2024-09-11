<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\PrItems;
use kartik\grid\GridView;
use app\modules\user\models\Profile;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


$this->params['breadcrumbs'][] = ['label' => ((in_array($purchaserequest->pr_no, [NULL])) ? $purchaserequest->temp_no : $purchaserequest->pr_no), 'url' => Yii::$app->request->referrer, ['id' => $_GET['id']]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="purchase-request-index">
  <div class="box box-primary">
    <div class="box-header with-border">
      <i>PR Number:</i>
      <h2 style="text-align: left"> <?= ((in_array($purchaserequest->status, ['1'])) ? $purchaserequest->temp_no : $purchaserequest->pr_no) ?> </h2>
      <h4 style="text-align: left"><i> Item Name: </i>  &nbsp;&nbsp; <strong><?= ($description->itemexplode); ?></strong> </h4>

      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

      <center>
        <?= Html::a('<span class="glyphicon glyphicon-download-alt"></span> Generate  History Report', ['generate-report/itemlogs-report', 'id' => $description->id,], ['class' => 'btn btn-warning']) . ' '; ?>
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
              'header' => 'ACTION DATE',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'options' => ['style' => 'width:15%'],
              'format' => 'raw',
              'value' => function ($itemHistory) {
                return Yii::$app->formatter->asDatetime(strtotime($itemHistory->action_date), 'php:d-M-Y | h:i');
               
              },
            ],
            [
              'attribute' => 'action_by',
              'header' => 'ACTION BY',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'value' => function ($v, $model) {
                $name = Profile::find()->where(['user_id' => $v->action_by])->one();
                return ($name == NULL ? '' : $name->fname . ' ' .  $name->lname);
              },
              'options' => ['style' => 'width:20%'],
            ],
            [
              'attribute' => 'action_status',
              'header' => 'STATUS',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'value' => 'trackstatusDisplay.status',
              'options' => ['style' => 'width:20%'],
            ],
            [
              'attribute' => 'action_remarks',
              'header' => 'REMARKS',
              'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
              'value' => function ($model) {
                $item = PrItems::find()->where(['id' => $model->item_id])->one();
                $bidding = BiddingList::find()->where(['item_id' => $model->item_id])->andWhere(['status' => 14])->one();
                // $supplier = Supplier::find()->where(['id' => $bidding->supplier_id])->one();
                // var_dump($supplier);die;
                if (isset($model->action_remarks)) {
                  return $model->action_remarks;
                }
                return $model->action_remarks;
              },
              'options' => ['style' => 'width:20%'],
            ],
          ]
        ]);
        ?>
    </div>
  </div>
</div>