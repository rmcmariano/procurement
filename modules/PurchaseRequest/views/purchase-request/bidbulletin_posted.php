<?php

use app\modules\PurchaseRequest\models\DateOptions;
use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\grid\GridView;


use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseRequest;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Quotation */
/* @var $form yii\widgets\ActiveForm */



?>


<div class="quotation-philgeps">


    <?php $form = ActiveForm::begin(); ?>

    <div class="panel panel-default">
        <div style="padding: 20px">
            <div class="panel-body">
                <div class="row">
                    <?= $form->field($bulletin, 'date_posted')->widget(
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

                <div class="row">
                    <?= $form->field($bulletin, 'bid_bulletin_no')->textInput(['maxlength' => true])  ?>

                </div>



                &nbsp; &nbsp; &nbsp;
                <div class="row">
                    <div style="text-align:right">
                        <?= Html::submitButton('Save', ['id' => $bulletin->id, 'class' => 'btn btn-success']) ?> &nbsp; &nbsp;
                        <!-- <?= Html::a('Print PDF', ['pdfrfq', 'id' => $bulletin->id], ['class' => 'btn btn-success']) ?> &nbsp; &nbsp; -->
                    </div>
                </div>


                &nbsp; &nbsp; &nbsp;


                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>