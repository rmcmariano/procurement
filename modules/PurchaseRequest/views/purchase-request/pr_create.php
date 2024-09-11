<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use kartik\select2\Select2;
use yii\widgets\Pjax;
use kartik\date\DatePicker;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\Info;
use app\modules\PurchaseRequest\models\Position;
use app\modules\user\models\Profile;

$projectType = [
    1 => 'Projects',
    2 => 'Non-Projects',
];

?>

<div class="purchaserequest-form">
    <?php $form = Pjax::begin(); ?>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data', 'id' => 'dynamic-form', 'autocomplete' => 'off']]) ?>

    <div class="box box-primary">
        <div class="box-header with-border">
            <p>
                <left>
                    <h2>CREATE NEW PURCHASE REQUEST</h2>
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
                                    'options' => [
                                        'placeholder' => 'Select date',
                                        'required' => true
                                    ],
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
                                [
                                    'id' => 'type',
                                    'prompt' => 'Select..',
                                ]
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

                        <div class="col-md-3 col-sm-12" id="fund">
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
                                    Info::find()
                                        ->where(['approve_status' => '1'])
                                        ->andWhere(['>=', 'daterange_to', date('Y-m-d')])
                                        ->orderBy(['project_title' => SORT_ASC])
                                        ->all(),
                                    'id',
                                    'project_title'
                                ),
                                'options' => [
                                    'prompt' => 'Select Charge To',
                                    'id' => 'charge-to',
                                ],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                ],
                            ])->label('Charge to <span style="color:red">*</span>');  ?>
                        </div>

                        <div class="col-md-3 col-sm-12" id="requested-depdrop">
                            <?= $form->field($model, 'requested_by1')->widget(DepDrop::classname(), [
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
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'requested_by',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Profile::find()->all(), 'id', 'fullname'),
                                    'options' => [
                                        'id' => 'requested-id2',
                                        'placeholder' => 'Select..',
                                        'multiple' => false,
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

                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($modelEnduser, 'user_id')->widget(
                                Select2::classname(),
                                [
                                    'name' => 'kv_theme_bootstrap_2',
                                    'attribute' => 'user_id',
                                    'size' => Select2::SMALL,
                                    'data' =>  ArrayHelper::map(Profile::find()->all(), 'id', 'fullname'),
                                    'options' => [
                                        'placeholder' => 'Select..',
                                        'multiple' => true
                                    ],
                                ]
                            ); ?>
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
                                        'multiple' => false,
                                    ],
                                ]
                            )->label('Position of Approved By <span style="color:red">*</span>'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 col-sm-12">
                            <?= $form->field($model, 'warranty')->textInput(['maxlength' => true,  'placeholder' => "Enter Warranty"])->label('Warranty: <span style="color:red">*</span>'); ?>
                        </div>
                        <div class="col-md-3     col-sm-12" id='period'>
                            <?= $form->field($model, 'delivery_period')->textInput(['maxlength' => true, 'placeholder' => "Enter Delivery period"])->label('Delivery Period: <span style="color:red"><i> * (Please use default "in calendar days")</i></span>') ?>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <?php DynamicFormWidget::begin([
                                'widgetContainer' => 'dynamicform_wrapper1',
                                'widgetBody' => '.container-items1',
                                'widgetItem' => '.item1',
                                'limit' => 5,
                                'min' => 1,
                                'insertButton' => '.add-item1',
                                'deleteButton' => '.remove-item1',
                                'model' => $modelAttach[0],
                                'formId' => 'dynamic-form',
                                'formFields' => [
                                    'files'
                                ],
                            ]); ?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4> Upload Attachments <button type="button" class="pull-right add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button> </h4>
                                </div>
                                <div class="box-body">
                                    <div class="container-items1">
                                        <?php foreach ($modelAttach as $i => $attach) : ?>
                                            <br>
                                            <div class="item1">
                                                <div class="clearfix">
                                                    <?php
                                                    if (!$attach->isNewRecord) {
                                                        echo Html::activeHiddenInput($attach, "[{$i}]id");
                                                    }
                                                    $initialPreview = [];
                                                    ?>
                                                    <div class="col-sm-11">

                                                        <?= $form->field($attach, "[{$i}]file_name")->widget(FileInput::classname(), [
                                                            'options' => [
                                                                'id' => 'imageFile',
                                                                'multiple' => false,
                                                                'accept' => 'image/*, .pdf, .docx'
                                                            ],
                                                            'pluginOptions' => [
                                                                'initialPreviewAsData' => true,
                                                                'showPreview' => false,
                                                                'showCaption' => true,
                                                                'showRemove' => false,
                                                                'showUpload' => false,
                                                                'browseLabel' => '',
                                                                'removeLabel' => '',
                                                            ],
                                                        ])->label(false) ?>
                                                    </div>
                                                    <button type="button" class="pull-right remove-item1 btn btn-danger btn-xs" style="position:relative; left: -20px; margin-top:5px;"><i class="glyphicon glyphicon-minus"></i></button>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <?php DynamicFormWidget::end(); ?>
                        </div>
                    </div>


                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper',
                        'widgetBody' => '.container-items',
                        'widgetItem' => '.parent-item',
                        'limit' => 100,
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
                            <div class="panel-heading" style="background-color: #1E80C1; color: white; height: 50px; font-family:Arial, Helvetica, sans-serif;">
                                <h4> ITEM/EQUIPMENT DETAILS<button type="button" class="pull-right add-items btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button> </h4>
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
                                            <?= $form->field($modelotherdescription, "[{$i}]unit_cost")->textInput([
                                                'maxlength' => true,
                                                'step' => '0.01',
                                                'onkeyup' => 'totales($(this))',
                                                'style' => 'text-align: right',
                                                'placeholder' => "Php"
                                            ]) ?>
                                        </td>
                                        </td>
                                        <td class="vcenter" style="width: 5%;">
                                            <?= $form->field($modelotherdescription, "[{$i}]quantity")->textInput(['maxlength' => true, 'onkeyup' => 'totales($(this))', 'style' => 'text-align: center']) ?>
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
                </div>
                </p>
            </div>
            <?php DynamicFormWidget::end(); ?>

            <div class="form-group">
                <center>

                    <?= Html::submitButton('Save', ['class' => 'btn btn-info btn-sm', 'value' => '0', 'name' => 'save']) ?>
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-success btn-sm', 'value' => '1', 'name' => 'submit_save']) ?>
                    <?= Html::submitButton($modelotherdescription->isNewRecord ? 'Cancel' : 'Cancel', ['class' => 'btn btn-danger btn-sm']) ?>

                </center>
            </div>
        </div>
        </p>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
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
            return n.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",")
        }

        function formatCurrency(input, blur) {

            var input_val = input.val();

            if (input_val === "") {
                return;
            }

            var original_len = input_val.length;
            var caret_pos = input.prop("selectionStart");

            if (input_val.indexOf(".") >= 0) {
                var decimal_pos = input_val.indexOf(".");
                var left_side = input_val.substring(0, decimal_pos);
                var right_side = input_val.substring(decimal_pos);

                left_side = formatNumber(left_side);
                right_side = formatNumber(right_side);

                if (blur === "blur") {
                    right_side += "00";
                }

                right_side = right_side.substring(0, 2);
                input_val = left_side + "." + right_side;

            } else {
                input_val = formatNumber(input_val);
                input_val = input_val;

                if (blur === "blur") {
                    input_val += ".00";
                }
            }

            input.val(input_val);
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
                //$('#period').hide();
                $('#sdo').show();
                $('#requested').show();

            }
            else if(type == 2) {
               // $('#period').hide();
                $("#sdo").hide();
            }
            else {
                $("#sdo").hide();
                $('#period').show();
            }
        });
                $("#sdo").hide();
                $('#period').show();
    });

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