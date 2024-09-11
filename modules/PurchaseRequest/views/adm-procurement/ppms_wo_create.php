<?php

use app\modules\PurchaseRequest\models\AddservicesLists;
use app\modules\PurchaseRequest\models\DeductionLists;
use app\modules\PurchaseRequest\models\PrItems;
use dosamigos\datepicker\DatePicker;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseOrder */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="purchase-order-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="panel panel-default">
        <div class="box box-primary">
            <div class="box-header with-border">
                <table class="table table-responsive">
                    <tr>
                        <td colspan="2">
                            <h5><i><strong> Purchase Request Details </strong></i></h5>
                        </td>
                        <td colspan="2">
                            <h5><i><strong> Supplier's Information </strong></i></h5>
                        </td>
                    </tr>
                    <tr>
                        <td style=" text-align: left; width: 20%"><strong> Purchase Request No.: </strong></td>
                        <td style=" text-align: left;"> <?= $purchaserequest->pr_no ?> </td>
                        <td style=" text-align: left; width: 20%"> <strong> Supplier's Name: </strong></td>
                        <td style=" text-align: left;"> <?= $bidding->supplierdisplay->supplier_name ?></td>
                    </tr>
                    <tr>
                        <td style=" text-align: left; width: 20%"> <strong> Mode of Procurement: </strong></td>
                        <td style=" text-align: left;"> <?= $purchaserequest->procurementmode->mode_name ?> </td>
                        <td style=" text-align: left; width: 20%"> <strong> Address: </strong></td>
                        <td style=" text-align: left;"> <?= $bidding->supplierdisplay->supplier_address  ?> </td>
                    </tr>
                    <tr>
                        <td style=" text-align: left; width: 20%"> <strong> End-user: </strong></td>
                        <td style=" text-align: left;"><?= $purchaserequest->enduser ?></td>
                        <td style=" text-align: left; width: 20%"> <strong> TIN No.: </strong></td>
                        <td style=" text-align: left;"><?= $bidding->supplierdisplay->tin_no ?></td>
                    </tr>
                </table>
                <hr style="border-bottom: 1px solid #77ccff;" />

                <div class="box-header with-border">
                    <left>
                        <h5><i><strong> Input Purchase Order Details </strong></i></h5>
                    </left>

                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <?= $form->field($purchaseOrder, 'pr_id')->hiddenInput(['value' => Yii::$app->request->get('id')])->label(false); ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($purchaseOrder, 'date_created')->widget(
                                DatePicker::className(),
                                [
                                    // inline too, not bad
                                    'inline' => false,
                                    // modify template for custom rendering
                                    // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                                    'options' => ['id' => 'date4', 'value' => ''],
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                        'todayHighlight' => true,
                                    ],
                                ]
                            ); ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($purchaseOrder, 'po_no')->textInput(['value' => $purchaseOrder->po_no, 'readonly' => true])->label('P.O./W.O. Number:') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($purchaseOrder, 'place_delivery')->textInput(['max' => true])->label('Place of Delivery:') ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($purchaserequest, 'delivery_period')->textInput(['max' => true])->label('Delivery Term:') ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($purchaseOrder, 'date_delivery')->widget(
                                DatePicker::className(),
                                [
                                    // inline too, not bad
                                    'inline' => false,
                                    // modify template for custom rendering
                                    // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                                    // 'options' => ['id' => 'date4', 'value' => ''],
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'd-M-yyyy',
                                        'todayHighlight' => true,
                                    ],
                                ]
                            )->label('Date of Delivery'); ?>
                    </div>

                    <div class="row">
                       
                        <div class="col-md-6 col-sm-12">
                            <?= $form->field($purchaseOrder, 'payment_term')->textInput(['max' => true])->label('Payment Term') ?>
                        </div>
                    </div>

                    <!-- <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <= $form->field($purchaseOrder, 'ors_burs_num')->textInput(['value' => $purchaseOrder->ors_burs_num, 'readonly' => true])->label('ORS/BURS Number') ?>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <= $form->field($purchaseOrder, 'date_ors_burs')->widget(
                                DatePicker::className(),
                                [
                                    // inline too, not bad
                                    'inline' => false,
                                    // modify template for custom rendering
                                    // 'template' => '<div class="well well-sm" style="background-color: #fff; width:250px">{input}</div>',
                                    // 'options' => ['id' => 'date4', 'value' => ''],
                                    'clientOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                        'todayHighlight' => true,
                                    ],
                                ]
                            ); ?>
                        </div> -->
                </div>


                <hr style="border-bottom: 1px solid #77ccff;" />

                <left>
                    <h5><i><strong> Item/Equipment Details </strong></i></h5>
                </left>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <?= GridView::widget([

                            'dataProvider' => $dataProvider2,
                            'showFooter' => true,
                            'options' => [
                                'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                            ],
                            //'filterModel' => $searchModel,
                            'columns' => [
                                [
                                  'class' => 'kartik\grid\SerialColumn',
                                  'options' => ['style' => 'width:3%'],
                                ],
                                [
                                    'attribute' => 'unit',
                                    'header' => 'UNIT',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'value' => 'prItemsdisplay.unit',
                                    'hAlign' => 'center',
                                ],

                                // [
                                //     'attribute' => 'item_remarks',
                                //     'header' => 'BID OFFER',
                                //     'headerOptions' => ['style' => 'color:#337ab7; text-align: center'],
                                //     'format' => 'raw',
                                //     'value' => function ($url, $model, $index) {
                                //         return Html::activeTextarea(new BiddingList(), "[$index]item_remarks" , ['style'=> 'width:100%; height: 80px']) . Html::activeHiddenInput(new BiddingList, "[$index]item_id", ['value' => $url->id]);
                                //     },
                                //     'options' => ['style' => 'width: 60%'],
                                // ],
                                [
                                    'attribute' => 'item_remarks',
                                    'format' => 'ntext',
                                    'options' => ['style' => 'width:40%'],
                                    'hAlign' => 'center',
                                    'contentOptions' => ['style' => 'text-align: left'],
                                    'header' => 'ITEM DESCRIPTION',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'value' => function ($model) {
                                        $items = PrItems::find()->where(['id' => $model->item_id])->one();
                           
                                        if ($model->item_remarks == $items->id) {
                                            return $items->item_name;
                                        }
                                        return $model->item_remarks;
                                    },

                                ],
                                [
                                    'attribute' => 'unit_cost',
                                    'header' => 'UNIT COST',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'value' => function ($model) {

                                        $test = PrItems::find()->where(['id' => $model['item_id']])->one();
                                        return $model['supplier_price'];
                                    },
                                    'format' => [
                                        'decimal', 2
                                    ],
                                    'hAlign' => 'right',
                                ],
                                [
                                    'attribute' => 'quantity',
                                    'header' => 'QTY',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'value' => 'prItemsdisplay.quantity',
                                    'hAlign' => 'center',
                                ],
                                [
                                    'attribute' => 'supplier_totalprice',
                                    'header' => 'TOTAL PRICE',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'value' => function ($model) {
                                        $test = PrItems::find()->where(['id' => $model['item_id']])->one();

                                        return $test['quantity'] * $model['supplier_price'];
                                    },
                                    'format' => [
                                        'decimal', 2
                                    ],
                                    'hAlign' => 'right',
                                ],
                            ]
                        ]);
                        ?>
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
                                'model' => $modelDeductions[0],
                                'formId' => 'dynamic-form',
                                'formFields' => [
                                    'members_id',
                                ],
                            ]); ?>


                            <div class="panel panel-default">
                                <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
                                    <h3 class="panel-title pull-left">TAX Deductions</h3>
                                    <button type="button" class="pull-right add-item2 btn btn-success btn-xs" style="position:relative; left: -4px; margin-top:1px;"><i class="glyphicon glyphicon-plus"></i>ADD</button>
                                    <div class="clearfix"></div>

                                </div>

                                <!-- subdata -->
                                <div class="container-items2">
                                    <?php foreach ($modelDeductions as $i => $modelLessDeductions) : ?>
                                        <div class="item2">
                                            <button type="button" class="pull-right remove-item2 btn btn-danger btn-xs" style="position:absolute; right: 100px; margin-top:35px;"><i class="glyphicon glyphicon-minus"></i>
                                            </button>

                                            <div class="box-body">
                                                <div class="clearfix">
                                                </div>
                                                <?php

                                                // necessary for update action.
                                                if (!$modelLessDeductions->isNewRecord) {
                                                    echo Html::activeHiddenInput($modelLessDeductions, "[{$i}]id");
                                                }
                                                ?>
                                                <div class="row">
                                                    <div class="col-md-6 col-sm-12">
                                                        <?= $form->field($modelLessDeductions, "[{$i}]deduction_id")->dropDownList(
                                                            ArrayHelper::map(DeductionLists::find()->all(), 'id', 'code'),
                                                            ['id' => 'deduction_amount', 'prompt' => 'Select',]
                                                        )->label('Deduction') ?>
                                                    </div>
                                                    <div class="col-md-4 col-sm-12">
                                                        <?= $form->field($modelLessDeductions, "[{$i}]deduction_amount")->textInput(['maxlength' => true])->label('Amount') ?>
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

                    <!-- Additional Services Dynamic Form - Start -->
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
                                    'model' => $modelAdditions[0],
                                    'formId' => 'dynamic-form',
                                    'formFields' => [
                                        'members_id',
                                    ],
                                ]); ?>


                                <div class="panel panel-default">
                                    <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
                                        <h3 class="panel-title pull-left">Additional Services</h3>
                                        <button type="button" class="pull-right add-item2 btn btn-success btn-xs" style="position:relative; left: -4px; margin-top:1px;"><i class="glyphicon glyphicon-plus"></i>ADD</button>
                                        <div class="clearfix"></div>

                                    </div>

                                    <!-- subdata -->
                                    <div class="container-items2">
                                        <?php foreach ($modelAdditions as $i => $modelAddservices) : ?>
                                            <div class="item2">
                                                <button type="button" class="pull-right remove-item2 btn btn-danger btn-xs" style="position:absolute; right: 100px; margin-top:35px;"><i class="glyphicon glyphicon-minus"></i>
                                                </button>

                                                <div class="box-body">
                                                    <div class="clearfix">
                                                    </div>
                                                    <?php

                                                    // necessary for update action.
                                                    if (!$modelAddservices->isNewRecord) {
                                                        echo Html::activeHiddenInput($modelAddservices, "[{$i}]id");
                                                    }
                                                    ?>
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12">
                                                            <?= $form->field($modelAddservices, "[{$i}]add_service_id")->dropDownList(
                                                                ArrayHelper::map(AddservicesLists::find()->all(), 'id', 'service_name'),
                                                                ['id' => 'additional_amount', 'prompt' => 'Select',]
                                                            )->label('Additional Services') ?>
                                                        </div>
                                                        <div class="col-md-4 col-sm-12">
                                                            <?= $form->field($modelAddservices, "[{$i}]additional_amount")->textInput(['maxlength' => true])->label('Amount') ?>
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
                    </div>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
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
              rtn: $(this).val()
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
?>

<style>
    .purchase-order-form {
        font-size: smaller;
    }
</style>