<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use kartik\select2\Select2;
use app\modules\user\models\Profile;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\Info;
use app\modules\PurchaseRequest\models\Position;
use kidzen\dynamicform\DynamicFormWidget;

$projectType = [
    1 => 'Projects',
    2 => 'Non-Projects',
];

?>

<div class="purchaserequest-update">
    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']) ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <p>
                <left>
                    <h2>UPDATE PURCHASE REQUEST</h2>
                </left>
                <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 50px; font-family:Arial, Helvetica, sans-serif;">
                    <h1 class="panel-title pull-left" style="font-size: large; margin-top: 8px">PURCHASE REQUEST FORM</h1>
                    <div class="clearfix"></div>
                </div>
                <p>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'date_of_pr')->widget(
                                DatePicker::className(),
                                [
                                    'options' => ['placeholder' => 'Select date'],
                                    'pluginOptions' => [
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd',
                                        'todayHighlight' => true,
                                        'orientation' => 'bottom',
                                    ]
                                ],
                            )->label('Date <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'pr_type_id')->dropDownList(
                                ArrayHelper::map(PrType::find()->all(), 'id', 'type_name'),
                                ['id' => 'type', 'prompt' => 'Select..',]
                            )->label('Type of PR <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="sdo">
                            <?= $form->field($model, 'sdo_officer_id')->widget(
                                Select2::classname(),
                                [
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'sdo_officer_id',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Profile::find()->all(), 'id', 'fullname'),
                                    'options' => [
                                        'placeholder' => 'Select..',
                                        'multiple' => false
                                    ],
                                ]
                            )->label('SDO Officer <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'fund_source_id')->dropDownList(
                                $projectType,
                                [
                                    'id' => 'projType-id',
                                    'prompt' => 'Select Fund Source'
                                    
                                ]
                            )->label('Fund Source <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="cost-type">
                            <?= $form->field($model, 'indirect_direct_cost')->checkbox([
                                'label' => 'Indirect and Direct Cost',
                                'id' => 'direct-cost',
                            ]) ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-12" id="test">
                            <?= $form->field($model, 'charge_to')->widget(Select2::class, [
                                'data' => ArrayHelper::map(
                                    Info::find()->where(['approve_status' => '1'])
                                    ->andWhere(['>=', 'daterange_to', date('Y-m-d')])
                                    ->orderBy(['project_title' => SORT_ASC])->all(),
                                    'id',
                                    'project_title'
                                ),
                                'options' => ['prompt' => 'Select Charge To', 'id' => 'charge-to'],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ])->label('Charge to <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="requested-depdrop">
                            <?= $form->field($model, 'requested_by1')->widget(DepDrop::classname(), [
                                'data' => [$model->requested_by => $modelFullName],
                                'options' => ['prompt' => 'Select', 'id' => 'requested-id'],
                                'pluginOptions' => [
                                    'depends' => ['charge-to'],
                                    'placeholder' => 'Select',
                                    'url' => Url::to(['purchase-request/requestedby'])
                                ]
                            ])->label('Requested By <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="requested-depdrop-direct-cost">
                            <?= $form->field($model, 'requested_by3')->widget(DepDrop::classname(), [
                                'options' => ['prompt' => 'Select', 'id' => 'requested-id3'],
                                'pluginOptions' => [
                                    'depends' => ['charge-to'],
                                    'placeholder' => 'Select',
                                    'url' => Url::to(['purchase-request/requestedbydirectcost'])
                                ]
                            ])->label('Requested By <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="requested">
                            <?= $form->field($model, 'requested_by2')->widget(
                                Select2::classname(),
                                [
                                    'data' => [$model->requested_by => $modelFullName],
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'requested_by',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Profile::find()->all(), 'id', 'fullname'),
                                    'options' => [
                                        'placeholder' => 'Select..',
                                        'multiple' => false
                                    ],
                                ]
                            )->label('Requested By <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="position">
                            <?= $form->field($model, 'requestedby_position')->widget(
                                Select2::classname(),
                                [
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'requestedby_position',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Position::find()->all(), 'id', 'position_title'),
                                    'options' => [
                                        'placeholder' => 'Select..',
                                        'multiple' => false
                                    ],
                                ]
                            )->label('Position of Requested By <span style="color:red">*</span>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'purpose')->textarea(['rows' => 2, 'placeholder' => "Enter text here"])->label('Purpose <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'approved_by')->widget(
                                Select2::classname(),
                                [
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'approved_by',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Profile::find()->all(), 'user_id', 'fullname'),
                                    'options' => [
                                        'placeholder' => 'Select..',
                                        'multiple' => false
                                    ],
                                ]
                            )->label('Approved By <span style="color:red">*</span>'); ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="position">
                            <?= $form->field($model, 'approvedby_position')->widget(
                                Select2::classname(),
                                [
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'approvedby_position',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Position::find()->all(), 'id', 'position_title'),
                                    'options' => [
                                        'placeholder' => 'Select..',
                                        'multiple' => false
                                    ],
                                ]
                            )->label('Position of Approved By <span style="color:red">*</span>'); ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'warranty')->textInput(['maxlength' => true,  'placeholder' => "Enter Warranty"])->label('Warranty: <span style="color:red">*</span>'); ?>
                        </div>
                        <div class="col-md-3 col-sm-12" id='period'>
                            <?= $form->field($model, 'delivery_period')->textInput(['maxlength' => true, 'placeholder' => "Enter Delivery period"])->label('Delivery Period: <span style="color:red"><i>(use default in calendar days)</i></span>') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <?= $form->field($model, 'file[]')->widget(FileInput::class, [
                                'options' => [
                                    'multiple' => true,
                                    'accept' => '.pdf,xlsx,image/*',
                                    'id' => 'deletePrev',
                                    'class' => 'deleteDeni',
                                ],
                                'pluginOptions' => [
                                    'initialPreview' => $file_preview,
                                    'initialPreviewConfig' => $file_config,
                                    'initialPreviewAsData' => true,
                                    'overwriteInitial' => false,
                                    'showUpload' => false,
                                    'showRemove' => true,
                                    'removeLabel' => 'Remove recent added file/s',
                                    'allowedFileExtensions' => ["pdf", "png", "jpg", "jpeg"],
                                    'deleteUrl' => Url::to('pr-updatedelete-file'),
                                ],
                            ])->label('Attachments'); ?>
                        </div>
                    </div>

                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper',
                        'widgetBody' => '.container-items',
                        'widgetItem' => '.parent-item',
                        'limit' => 30,
                        'min' => 1,
                        'insertButton' => '.add-items',
                        'deleteButton' => '.remove-items',
                        'model' => $modeldescription[0],
                        'formId' => 'dynamic-form',
                        'formFields' => [
                            // 'stock',
                            'unit',
                            'item',
                            'quantity',
                            'unit_cost',
                            'total_cost',
                        ],
                    ]); ?>

                    <table class="table table-bordered">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4> Item/Equipment Details<button type="button" class="pull-right add-items btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button> </h4>
                            </div>
                            <tbody class="container-items">
                                <?php foreach ($modeldescription as $i => $modelotherdescription) : ?>
                                    <tr class="parent-item">

                                        <?php
                                        // necessary for update action.
                                        if (!$modelotherdescription->isNewRecord) {
                                            echo Html::activeHiddenInput($modelotherdescription, "[{$i}]id");
                                        }
                                        ?>

                                        <td class="vcenter" style="width: 5%;">
                                            <?= $form->field($modelotherdescription, "[{$i}]unit")->textInput(['maxlength' => true]) ?>
                                        </td>
                                        <td class="vcenter" style="width: 30%;">
                                            <?= $form->field($modelotherdescription, "[{$i}]item_name")->textarea(['maxlength' => true, 'rows' => '2', 'placeholder' => "Enter Item Name/Title"])->label('Item Name/Title') ?>
                                        </td>
                                        <td class="vcenter" style="width: 30%;">
                                            <?= $this->render('pr_items_specification_create', [
                                                'form' => $form,
                                                'i' => $i,
                                                'modelSpecifications' => $modelSpecifications[$i],
                                                'modelotherdescription' => $modelotherdescription

                                            ]) ?>
                                        </td>
                                        <td class="vcenter" style="width: 10%;">
                                            <?= $form->field($modelotherdescription, "[{$i}]unit_cost")->textInput(['maxlength' => true, 'onkeyup' => 'totales($(this))', 'style' => 'text-align: right', 'placeholder' => "Php"]) ?>
                                        </td>
                                        </td>
                                        <td class="vcenter" style="width: 5%;">
                                            <?= $form->field($modelotherdescription, "[{$i}]quantity")->textInput(['maxlength' => true, 'onkeyup' => 'totales($(this))', 'type' => 'number', 'style' => 'text-align: center']) ?>
                                        </td>
                                        <td class="vcenter" style="width: 10%;">
                                            <?= $form->field($modelotherdescription, "[{$i}]total_cost")->textInput(['readonly' => true, 'style' => 'text-align: right', 'placeholder' => "Php"]) ?>
                                        </td>

                                        <td class="vcenter" style="width: 2%;">
                                            <button type="button" class="remove-items btn btn-danger btn-xs"><span class="fa fa-minus"></span></button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </div>
                    </table>
                    <?php DynamicFormWidget::end(); ?>
                </div>
                </p>
            </div>
           

            <div class="form-group">
                <div style="text-align:center">

                    <?= Html::submitButton($model->isNewRecord ? 'Update' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-danger' : 'btn btn-primary']) ?> &nbsp;

                </div>
            </div>
        </div>
        </p>
    </div>
    <?php ActiveForm::end(); ?>
</div>



<script>
    function totales(item2) {
        var i = item2.attr("id").replace(/[^0-9.]/g, "");
        var quantity = $('#pritems-' + i + '-quantity').val();
        quantity = quantity == "" ? 0 : Number(quantity.split(",").join(""));
        var unit_cost = $('#pritems-' + i + '-unit_cost').val();
        unit_cost = unit_cost == "" ? 0 : Number(unit_cost.split(",").join(""));
        $('#pritems-' + i + '-total_cost').val(quantity * unit_cost);

        $("input[data-type='currency']").on({
            keyup: function() {
                formatCurrency($(this));
            },
            blur: function() {
                formatCurrency($(this), "blur");
            }
        });


        function formatNumber(n) {
            // format number 1000000 to 1,234,567
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function formatCurrency(input, blur) {
            // appends $ to value, validates decimal side
            // and puts cursor back in right position.

            // get input value
            var input_val = input.val();

            // don't validate empty input
            if (input_val === "") {
                return;
            }

            // original length
            var original_len = input_val.length;

            // initial caret position 
            var caret_pos = input.prop("selectionStart");

            // check for decimal
            if (input_val.indexOf(".") >= 0) {

                // get position of first decimal
                // this prevents multiple decimals from
                // being entered
                var decimal_pos = input_val.indexOf(".");

                // split number by decimal point
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);

                // add commas to left side of number
                left_side = formatNumber(left_side);

                // validate right side
                right_side = formatNumber(right_side);

                // On blur make sure 2 numbers after decimal
                if (blur === "blur") {
                    right_side += "00";
                }

                // Limit decimal to only 2 digits
                right_side = right_side.substring(0, 2);

                // join number by .
                input_val = left_side + "." + right_side;

            } else {
                // no decimal entered
                // add commas to number
                // remove all non-digits
                input_val = formatNumber(input_val);
                input_val = input_val;

                // final formatting
                if (blur === "blur") {
                    input_val += ".00";
                }
            }

            // send updated string to input
            input.val(input_val);

            // put caret back in the right position
            var updated_len = input_val.length;
            caret_pos = updated_len - original_len + caret_pos;
            input[0].setSelectionRange(caret_pos, caret_pos);
        }
    };
</script>

<!-- hide field of delivery period -->
<?php
$this->registerJs(
    <<<JS
   $(document).ready(function() {
        $('#type').change(function() {
            var type = $(this).val();

            if(type == 1){
                $('#period').hide();
                $('#sdo').show();
                $("#pa").hide();
            }
            else if(type == 2) {
                $('#period').hide();
                $('#pa').show();
                $("#sdo").hide();
            }
            else {
                $("#sdo").hide();
                $("#pa").show();
                $('#period').show();
            }
        });
                $("#sdo").hide();
                $("#pa").hide();
                $('#period').show();
    });

    // $(document).ready(function() {
    //     $('#projType-id').change(function() {
    //         var projtype = $('#projType-id').val();

    //         if(projtype == 2){
    //             $('#test').hide();
    //             $('#requested-depdrop').hide();
    //             $('#requested').show();
    //         }
    //         else if(projtype == 1) {
    //             $('#test').show();
    //             $('#requested-depdrop').show();
    //             $('#requested').hide();
    //         }
    //     });
    //     $('#requested').hide();
    //     $('#test').hide();
    // });

    $(document).ready(function() {
        $('#projType-id').change(function() {
            var type = $('#projType-id').val();

            if(type == 2){
                $('#direct-cost').prop('checked', false);
                $('#test').hide();
                $('#test').val(0);
                $('#charge-to').val(null).trigger('change');
                $('#requested-id').val(null).trigger('change');
                // $('#requested-id').removeAttr('required');
                $('#requested-depdrop').hide();
                $('#requested').show();
                $('#cost-type').hide();

            }
            else if(type == 1) {
                $('#test').show();
                $('#charge-to').val(null).trigger('change');
                // $('#requested-id').attr('required', 'required');
                $('#requested-id2').val(null).trigger('change');
                $('#requested-depdrop').show();
                $('#requested').hide();
                $('#cost-type').show();

                $('#direct-cost').change(function() {
                    if ($(this).prop('checked')) {
                    $('#charge-to').val(null).trigger('change');
                    $('#requested-id').val(null).trigger('change');
                    $('#requested-depdrop').hide();
                    $('#requested-depdrop-direct-cost').show();
                    $('#requested-id3').prop('disabled', true);

                }
                else{
                    $('#charge-to').val(null).trigger('change');
                    $('#requested-id3').val(null).trigger('change');
                    $('#requested-depdrop-direct-cost').hide();
                    $('#requested-depdrop').show();
                    $('#requested-id').prop('disabled', true);

                }
                });
            }
            else{
                $('#direct-cost').prop('checked', false);
                $('#requested').hide();
                $('#test').hide();
                $('#test').val(0);
                $('#requested-id').removeAttr('required');
                $('#cost-type').hide();
                $('#requested-depdrop-direct-cost').hide();
                $('#requested-depdrop').hide();
                $('#requested-id').val(null).trigger('change');
                $('#requested-id').prop('disabled', true);

            }
        });
        $("#test").hide();
        $('#test').val(0);
        $('#requested-depdrop').hide();
        $('#requested').hide();
        $('#requested-depdrop-direct-cost').hide();
        $('#cost-type').hide();

    });


JS
);
?>