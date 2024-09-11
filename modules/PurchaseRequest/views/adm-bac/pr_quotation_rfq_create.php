<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\grid\GridView;
use app\modules\PurchaseRequest\models\PrItems;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Quotation */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="modal-header rfq">
    <h5 class="modal-title" id="modal-label"> Canvass Form
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h5>
</div>

<div class="rfq-create">
    <?php $form = ActiveForm::begin(); ?>

    <table class="table table-responsive">
        <tr>
            <td style="text-align: left">Date Created:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= Yii::$app->formatter->asDatetime(strtotime($quotation->time_stamp), 'php:F d, Y')  ?></span></td> &nbsp;&nbsp;&nbsp;
        </tr>
        <tr>
            <td style="text-align: left">Quotation number:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $quotation->quotation_no ?></span></td> &nbsp;&nbsp;&nbsp;
        </tr>
        <tr>
            <td style="text-align: left">PR number:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $quotation->purchaseRequest->pr_no ?></span></td> &nbsp;&nbsp;&nbsp;
        </tr>
        <tr>
            <td style="text-align: left">End User:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $purchaserequest->enduser ?></span></td> &nbsp;&nbsp;&nbsp;
        </tr>
        <tr>
            <td style="text-align: left">Charge to:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= ($purchaserequest->charge_to == 0 ? 'GAA' : $purchaserequest->chargedisplay->project_title) ?></span></td> &nbsp;&nbsp;&nbsp;
        </tr>
        <tr>
            <td style="text-align: left"><?= $quotation->optionsdisplay->options ?>:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:F d, Y') ?></span></td> &nbsp;&nbsp;&nbsp;
        </tr>

    </table>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [

            [
                'class' => 'kartik\grid\SerialColumn',
                'options' => ['style' => 'width: 3%']
            ],
            [
                'attribute' => 'unit',
                'options' => ['style' => 'width:5%'],
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'item_name',
                'format' => 'ntext',
                'options' => ['style' => 'width:40%'],
            ],
            [
                'attribute' => 'quantity',
                'options' => ['style' => 'width:5%'],
                'hAlign' => 'center',
            ],
            [
                'attribute' => 'unit_cost',
                'format' => [
                    'decimal', 2
                ],
                'options' => ['style' => 'width:10%'],
                'hAlign' => 'right',
            ],
            [
                'attribute' => 'total_cost',
                'format' => [
                    'decimal', 2
                ],
                'options' => ['style' => 'width:10%'],
                'footer' => 'PHP' . ' ' . number_format(PrItems::getTotal($dataProvider->models, 'total_cost'), 2, ".",  ","),
                'hAlign' => 'right',
            ],
        ]
    ]);
    ?>

    <div class="row">
        <div style="text-align:center">
            <?= Html::a('Canvass Form', ['bac-quotation-rfq-pdf', 'id' => $quotation->id], ['target' => '_blank', 'class' => 'btn btn-success', 'data-pjax' => 0]) ?> &nbsp;
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $('.close').on("click", function() {
        location.reload();
    });
</script>


<style>
    .rfq-create {
        font-size: small;
    }

    .rfq-create {
        padding-bottom: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background-color: white;
        display: inline-block;
        width: 100%;
    }

    .rfq {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background-color: #12B359;
    }
</style>