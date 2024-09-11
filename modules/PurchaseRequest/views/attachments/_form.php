<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Attachments */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="attachments-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading" style="background-color: #3c8dbc; color: white;">
                <h3 class="panel-title pull-left">Attachments</h3>
                <div class="clearfix"></div>
            </div>

            <div class="panel-body">
                <div class="row">

                    <div class="col-md-3 col-sm-12">
                        <?= $form->field($model, 'pr_no')->textInput(['value' => $purchaserequest->pr_no, 'readonly' => true]) ?>
                    </div>
                </div>
                <table style="border: 1px solid black;  border-collapse: collapse; width: 80%;">
                    <tr>
                        <th style="border: 1px solid black; padding: 10px; text-align: center; width: 30%;">FILE NAME</th>
                        <th style="border: 1px solid black; padding: 10px; text-align: center; width: 30%;">ATTACHED FILE</th>
                        <th style="border: 1px solid black; padding: 10px; text-align: center">ACTION</th>
                    </tr>

                </table>
                <table style="border: 1px solid black;  border-collapse: collapse; width: 80%;">
                    <tr>
                        <td style="border: 1px solid black; padding: 10px; text-align: center; width: 30%;">Request For Quotation</td>
                        <td style="border: 1px solid black; padding: 10px; text-align: center; width: 30%;"> <?= $form->field($model, 'attach_rfq')->fileInput(['maxlength' => true]) ?></td>
                        <td style="border: 1px solid black; padding: 10px; text-align: center"> <?= Html::submitButton('PDF', ['class' => 'btn btn-success']) ?> <?= Html::submitButton('Released Document', ['class' => 'btn btn-success']) ?></td>
                    </tr>

                    <tr>
                        <td style="border: 1px solid black; padding: 10px; text-align: center; width: 30%;">Abstract</td>
                        <td style="border: 1px solid black; padding: 10px; text-align: center; width: 30%;"> <?= $form->field($model, 'attach_abstract')->fileInput(['maxlength' => true]) ?></td>
                        <td style="border: 1px solid black; padding: 10px; text-align: center"> <?= Html::submitButton('PDF', ['class' => 'btn btn-success']) ?> <?= Html::submitButton('Released Document', ['class' => 'btn btn-success']) ?></td>
                    </tr>

                </table>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

