<?php

use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\BiddingList */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="modal-header itemDetails">
    <h5 class="modal-title" id="modal-label"> Item Details
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h5>
</div>

<div class="itemDetailsForm">

    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <left>
                <h5><i><strong> Item/Equipment Details</strong></i></h5>
            </left>
            <table class="table table-responsive">
                <tr>
                    <td style=" text-align: left; width: 35%"><strong><i> Item/Equipment Name: </i></strong></td>
                    <td style=" text-align: left;"> <?= $description->itemexplode ?> </td>
                </tr>
                <tr>
                    <td style=" text-align: left; width: 35%"><strong><i> Project Charge: </i></strong></td>
                    <td style=" text-align: left;"> <?= ($purchaserequest->charge_to == 0 ? 'GAA': $purchaserequest->chargedisplay->project_title ) ?> </td>
                </tr>
                <tr>
                    <td style=" text-align: left; width: 35%"><strong><i> Approved Budget for the Contract (ABC): </i></strong></td>
                    <td style=" text-align: left;"> ₱ <?= number_format($description->total_cost, '2') ?> </td>
                </tr>
            </table>

            <hr style="border-bottom: 1px solid #77ccff;" />
            <left>
                <h5><i><strong> Input Additional Details of Items</strong></i></h5>
            </left>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-9 col-sm-12">
                        <?= $form->field($description, 'bid_title')->textInput(['placeholder' => 'Item BID Title'])->label('BID TITLE:') ?>
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <?= $form->field($purchaserequest, 'delivery_period')->textInput(['value' => $purchaserequest->delivery_period, 'readonly' => false])->label('DELIVERY PERIOD:') ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($preprocCondition, 'option_date')->widget(DateTimePicker::className(), [
                            // 'pickButtonIcon' => 'glyphicon glyphicon-time',
                            'inline' => false,
                            'options' => ['readonly' => ($preprocCondition->option_date == '' || NULL ? false : true)],
                            'clientOptions' => [
                                // 'minView' => 0,
                                // 'maxView' => 4,
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
                        ])->label('DATE PRE-PROCUREMENT:'); ?>
                    </div>


                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($openbidCondition, 'option_date')->widget(DateTimePicker::className(), [
                            // 'pickButtonIcon' => 'glyphicon glyphicon-time',
                            'inline' => false,
                            'options' => ['readonly' => ($openbidCondition->option_date == '' || NULL ? false : true)],
                            'clientOptions' => [
                                // 'minView' => 0,
                                // 'maxView' => 4,
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
                        ])->label('DATE OPENING:'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($philgepsCondition, 'option_date')->widget(DateTimePicker::className(), [

                            // 'pickButtonIcon' => 'glyphicon glyphicon-time',
                            'inline' => false,
                            // 'value' => $philgeps->option_date == NULL ? false : $philgeps->option_date,
                            'clientOptions' => [
                                // 'minView' => 0,
                                // 'maxView' => 4,
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
                        ])->label('DATE PhilGEPS POSTING:'); ?>
                    </div>

                    <div class="col-md-6 col-sm-12">
                        <?= $form->field($prebidCondition, 'option_date')->widget(DateTimePicker::className(), [

                            'inline' => false,
                            // 'value' => $prebid->option_date == NULL ? false : $prebid->option_date,
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
                        ])->label('DATE PRE-BID CONFERENCE:'); ?>
                    </div>
                </div>
            </div>

            <hr style="border-bottom: 1px solid #77ccff;" />
            <left>
                <h5><i><strong> Input Bidding Documents Fee</strong></i></h5>
            </left>
            <div class="form-group">
                <div class="row">
                    <div class="col-md-4 col-sm-12">
                        <?= $form->field($description, 'bidding_docs_fee')->textInput(['style' => 'text-align: right', 'placeholder' => 'PhP'])->label('Bidding Documents Price:') ?>
                    </div>
                </div>
            </div>
        </div>
        &nbsp;


        <div class="form-group">
            <div style="text-align:center">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
        </p>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $('.close').on("click", function() {
        location.reload();
    });
</script>

<style>
    .itemDetailsForm {
        font-size: smaller;
    }

    .itemDetailsForm {
        padding-bottom: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background-color: white;
        display: inline-block;
        width: 100%;
    }

    .itemDetails {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background-color: #12B359;
    }
</style>