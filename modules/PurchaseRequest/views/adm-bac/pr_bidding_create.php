<?php

use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\Supplier;
use app\modules\user\models\Profile;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;


?>

<div class="bidding-list-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'dynamic-form']]) ?>

    <?= $form->field($model, 'id')->hiddenInput(['value' => $_GET['id']])->label(false); ?>

    <!-- Dynamic Form - Start -->
    <div class="form-group">
        <?php DynamicFormWidget::begin([
            'widgetContainer' => 'dynamicform_wrapper2',
            'widgetBody' => '.container-items2',
            'widgetItem' => '.item2',
            'limit' => 6,
            'min' => 1,
            'insertButton' => '.add-item2',
            'deleteButton' => '.remove-item2',
            'model' => $modelBidding[0],
            'formId' => 'dynamic-form',
            'formFields' => [
                'stock',
                'unit',
                'item',
                'quantity',
                'unit_cost',
                'total_cost',
            ],
        ]); ?>


        <div class="panel panel-default">
            <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 40px; font-family:Arial, Helvetica, sans-serif;">
                <h1 class="panel-title pull-left" style="font-size: large; margin-top: 3px"></h1>
                <button type="button" class="pull-right add-item2 btn btn-success btn-xs" style="position:relative; left: -4px; margin-top:px;"><i class="glyphicon glyphicon-plus"></i>ADD</button>
                <div class="clearfix"></div>
            </div>
            <p>

                <!-- add bidding dynamic -->
            <div class="container-items2">
                <?php foreach ($modelBidding as $i => $modelotherbidding) : ?>
                    <div class="item2">
                        <button type="button" class="pull-right remove-item2 btn btn-danger btn-xs" style="position:absolute; right: 50px; margin-top:35px;"><i class="glyphicon glyphicon-minus"></i>
                        </button>

                        <div class="box-body">

                            <?php
                            // necessary for update action.
                            if (!$modelotherbidding->isNewRecord) {
                                echo Html::activeHiddenInput($modelotherbidding, "[{$i}]id");
                            }
                            ?>

                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($modelotherbidding, "[{$i}]item_id")->dropDownList(ArrayHelper::map(PrItems::find()->where(['pr_id' => $model['id']])->andWhere(['status' => ['10', '11', '12', '13', '15', '36', '38', '47']])->asArray()->all(), 'id', function ($model) {
                                        $item = explode(PHP_EOL, trim($model['item_name']));
                                        return $item[0];
                                    }), ['prompt' => 'Select item', 'id' => 'item_select', 'class' => 'form-control item-select'],)->label('Item/Equipment') ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($modelotherbidding, "[{$i}]supplier_id")->dropDownList(
                                        ArrayHelper::map(Supplier::find()->all(), 'id', 'supplier_name'),
                                        ['prompt' => 'Select supplier',]
                                    ) ?>
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelotherbidding, "[{$i}]bid_bond")->textInput(['placeholder' => "Enter Bid bond"]) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-4">
                                    <?= $form->field($modelotherbidding, "[{$i}]supplier_price")->textInput(['style' => 'text-align: right', 'placeholder' => "Enter Amount"])->label('BID Price') ?>
                                </div>
                                <div class="col-sm-4">
                                    <?= $form->field($modelotherbidding, "[{$i}]assign_twg")->dropDownList(
                                        ArrayHelper::map(Profile::find()->all(), 'user_id', 'fullname'),
                                        ['prompt' => 'Select..',]
                                    ) ?>

                                 
                                </div>
                                <div class="col-sm-3">
                                    <?= $form->field($modelotherbidding, "[{$i}]supplier_payment_term")->textInput(['placeholder' => "Enter Payment Term"]) ?>
                                </div>
                            </div>
                            <!-- <div class="row"> -->
                            <!-- <div class="col-sm-11">
                                    <input type="checkbox" class="check_item" /> <span style="color: red;"><strong> <i>Check if the item has different Bid Offers</i></strong></span>
                                </div> -->
                            <!-- </div> -->
                            <!-- <div class="row item_bid"> -->
                            <!-- <div class="col-sm-11">
                                    <= $form->field($modelotherbidding, "[{$i}]item_remarks")->textarea(['placeholder' => "Enter Bid Offer", 'id' => 'bid_offer', 'class' => 'form-control bid-offer'])->label('BID Offer') ?>
                                </div> -->
                            <!-- </div> -->
                            <hr style="border-bottom: 1px solid #77ccff;">
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php DynamicFormWidget::end(); ?>
    </div>

    <div class="form-group">
        <p>
        <div style="text-align:center">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
        </p>
    </div>
    <?php ActiveForm::end(); ?>
</div>


<?php
$this->registerJs(
    <<<JS

// $('body').on('change', '.check_item', function(e){
//     var item = $(e.target).closest('.box-body').find('.item-select').val();
//     var txtBid = $(e.target).closest('.box-body').find('.bid-offer');
//     // var item = $('#item_select').val();
//     var bidValue = txtBid.val();

//     let toBeHidden = $(e.target).closest('.box-body').find('.item_bid');
    
//     if ($(e.target).prop("checked") == true){
//             toBeHidden.hide();
//             txtBid.val(item);
//     }else {
//         toBeHidden.show();
//         txtBid.val(bid);
//     }
// });

// $('body').on('change', '.check_item', function(e){
//     var item = $(e.target).closest('.box-body').find('.item-select').val();
//     var txtBid = $(e.target).closest('.box-body').find('.bid-offer');
//     var item = $('#item_select').val();
//     var bidValue = txtBid.val();

//     if ($(e.target).prop("checked") == true){
//         txtBid.val(bidValue);
//     } else {
//         txtBid.val(item);
//     }
// });


JS
);
?>

<style>
    .bidding-list-form {
        font-size: smaller;
    }

    .yourclass {
        width: 100px;
        height: auto;
    }
</style>