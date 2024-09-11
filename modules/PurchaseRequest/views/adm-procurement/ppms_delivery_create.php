<?php

use dosamigos\datepicker\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseOrder */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="delivery-form">

  <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

  <div class="panel panel-default">
    <div class="panel-body">
      <div class="row">
        <div class="col-md-4 col-sm-12">
          <?= $form->field($purchaseOrder, 'actual_date_delivery')->widget(
            DatePicker::className(),
            [
              'inline' => false,
              'options' => ['id' => 'date4'],
              'clientOptions' => [
                'autoclose' => true,
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => true,
              ],
            ]
          )->label('Date Delivery:'); ?>
        </div>
      </div>

      <!-- Dynamic Form - Start -->
      <div class="form-group">
        <div class="row">
          <div class="panel-body">

            <?php DynamicFormWidget::begin([
              'widgetContainer' => 'dynamicform_wrapper2',
              'widgetBody' => '.container-items2',
              'widgetItem' => '.item2',
              'limit' => 6,
              'min' => 1,
              'insertButton' => '.add-item2',
              'deleteButton' => '.remove-item2',
              'model' => $model_delivery[0],
              'formId' => 'dynamic-form',
              'formFields' => [
                'members_id',
              ],
            ]); ?>


            <div class="panel panel-default">
              <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
                <h3 class="panel-title pull-left">Add Delivery</h3>
                <button type="button" class="pull-right add-item2 btn btn-success btn-xs" style="position:relative; left: -4px; margin-top:5px;"><i class="glyphicon glyphicon-plus"></i>ADD</button>
                <div class="clearfix"></div>
              </div>


              <!-- subdata -->
              <div class="container-items2">
                <?php foreach ($model_delivery as $i => $modeldelivery) : ?>
                  <div class="item2">
                    <button type="button" class="pull-right remove-item2 btn btn-danger btn-xs" style="position:absolute; right: 60px; margin-top:35px;"><i class="glyphicon glyphicon-minus"></i>
                    </button>

                    <div class="box-body">
                      <div class="clearfix">
                      </div>
                      <?php
                      // necessary for update action.
                      if (!$modeldelivery->isNewRecord) {
                        echo Html::activeHiddenInput($modeldelivery, "[{$i}]id");
                      }
                      ?>
                      <div class="row">
                        <div class="col-md-3 col-sm-24">
                          <?= $form->field($modeldelivery, "[{$i}]actual_date_delivery")->textInput(['type' => 'date', 'style' => 'width:200px; height: 35px; font-size: 13px; border-radius: 5px;'])->label('Actual Date Delivery') ?>
                        </div>
                        <div class="col-md-3 col-sm-24">
                          <?= $form->field($modeldelivery, "[{$i}]type_delivery")->dropDownList(
                            ['a' => 'Select', 'b' => 'Partial', 'c' => 'Completed']
                          ); ?>
                        </div>
                        <div class="col-md-4 col-sm-24">
                          <?= $form->field($modeldelivery, "[{$i}]remarks")->textarea(['maxlength' => true]) ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
              <?php DynamicFormWidget::end(); ?>
            </div>
          </div>
        </div>

        <div class="form-group">
          <div style="text-align:center">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>

        <?php ActiveForm::end(); ?>

      </div>
    </div>
  </div>



  <?php
  $this->registerJs(
    <<<JS

$('.enableBtn').on('click', function() {
    var idToSubmit = $(this).val();
    console.log(idToSubmit);
    swal({
        title: "Do you want to approved?",
        icon: "warning",
        buttons: true,
        safeMode: true,
      })
      
      .then((willSubmit) => {
        if (willSubmit) {
          swal("Approved", {
            icon: "success",
          }).then((value) => {
            location.reload();
          });

          $.ajax({
            url: "create",
            type: "get",
            data: {
              delId: $(this).val()
            },
            
          }); console.log(data);
        } else {
          swal("Canceled", {
            icon: "error",
          }).then((value) => {
            location.reload();
          });
        }
      });
  });


JS
  );
