<?php

use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Quotation;
use dosamigos\datetimepicker\DateTimePicker;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>


<div class="pr-disapproval">
  <?php $form = ActiveForm::begin([]); ?>
  <div class="panel panel-default">
    <div style="padding: 20px">
      <div class="panel-body">
        <div class="col-md-12 col-sm-12">
          <?= $form->field($bidbulletin, 'id')->hiddenInput(['value' => Yii::$app->request->get('id')])->label(false); ?>
        </div>

        <div class="row">
          <div class="col-md-6 col-sm2">
            <?= $form->field($bidbulletin, 'bidbulletin_no')->textInput()->label('Enter Bid Bulletin No.:') ?>
          </div>

          <div class="col-md-6 col-sm-12">
            <?= $form->field($bidbulletin, 'date_posted')->widget(DateTimePicker::className(), [
              'size' => 'ms',
              'inline' => false,
              'clientOptions' => [
                'autoclose' => true,
                'format' => "yyyy-mm-dd H:ii P",
                'startDate' => date('now'),
                'minuteStep' => 10,
                'todayBtn' => true,
                'showMeridian' => true,
                'todayHighlight' => true,
                'keyboardNavigation' => true,
                'changeYear' => true,
              ],
            ])->label('Date Prepared:'); ?>
          </div>
        </div>

        <div class="panel panel-default">
          <div style="padding: 20px">
            <?= GridView::widget([
              'dataProvider' => $dataProvider,
              'options' => [
                'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
              ],
              'columns' => [
                [
                  'class' => 'kartik\grid\SerialColumn',
                  'options' => ['style' => 'width:3%'],
                ],
                [
                  'attribute' => 'pr_no',
                  'header' => 'PR #',
                  'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                  'options' => ['style' => 'width:10%'],
                  'value' => function ($model) {
                    $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                    return $pr['pr_no'];
                  },
                ],
                [
                  'attribute' => 'option_date',
                  'header' => 'DATE OPENING',
                  'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                  'options' => ['style' => 'width:10%'],
                  'value' => function ($model) {
                    $pr = PurchaseRequest::find()->where(['id' => $model['pr_id']])->one();
                    $quote = Quotation::find()->where(['pr_id' => $pr['id']])->andWhere(['option_id' => '4'])->one();
                    return $quote['option_date'];
                  },
                ],
              ]
            ]);
            ?>
          </div>
        </div>

        <div class="form-group">
          <p>
            <center>
              <?= Html::submitButton('Save', ['id' => $bidbulletin->id, 'class' => 'btn btn-success']) ?>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </center>
          </p>
        </div>

        <?php ActiveForm::end(); ?>
      </div>
    </div>
  </div>
</div>