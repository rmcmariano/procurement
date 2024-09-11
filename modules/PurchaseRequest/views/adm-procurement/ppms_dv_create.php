<?php

use app\modules\PurchaseRequest\models\AdditionalServices;
use app\modules\PurchaseRequest\models\AddservicesLists;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\DeductionLists;
use app\modules\PurchaseRequest\models\PrItems;
use dosamigos\datepicker\DatePicker;
use kartik\grid\DataColumn;
use kartik\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseOrder */
/* @var $form yii\widgets\ActiveForm */

// var_dump($dataProvider2);die;

?>

<div class="modal-header dv">
    <h5 class="modal-title" id="modal-dv-label"> Create Disbursement Voucher
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </h5>
</div>

<div class="dv-form">

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
                        <td style=" text-align: left;"> <?= $purchaseOrder->supplierdisplay->supplier_name ?></td>
                    </tr>
                    <tr>
                        <td style=" text-align: left; width: 20%"> <strong> Mode of Procurement: </strong></td>
                        <td style=" text-align: left;"> <?= $purchaserequest->procurementmode->mode_name ?> </td>
                        <td style=" text-align: left; width: 20%"> <strong> Address: </strong></td>
                        <td style=" text-align: left;"> <?= $purchaseOrder->supplierdisplay->supplier_address  ?> </td>
                    </tr>
                    <tr>
                        <td style=" text-align: left; width: 20%"> <strong> Charge To: </strong></td>
                        <td style=" text-align: left;"><?= $purchaserequest->chargedisplay->project_title ?></td>
                        <td style=" text-align: left; width: 20%"> <strong> TIN No.: </strong></td>
                        <td style=" text-align: left;"><?= $purchaseOrder->supplierdisplay->tin_no ?></td>
                    </tr>
                </table>
                <hr style="border-bottom: 1px solid #77ccff;" />

                <div class="box-header with-border">
                    <left>
                        <h5><i><strong>Purchase Order / Work Order Details </strong></i></h5>
                    </left>
                    <table class="table table-responsive">
                        <tr>
                            <td style=" text-align: left; width: 20%"><strong> P.O. / W.O. No.: </strong></td>
                            <td style=" text-align: left;"> <?= $purchaseOrder->po_no ?> </td>
                            <td style=" text-align: left; width: 20%"> <strong> P.O. / W.O. Date: </strong></td>
                            <td style=" text-align: left;"> <?= $purchaseOrder->po_date_created ?></td>
                        </tr>
                        <tr>
                            <td style=" text-align: left; width: 20%"> <strong> ORS/BURS Number:: </strong></td>
                            <td style=" text-align: left;"> <?= $purchaseOrder->ors_burs_num ?> </td>
                            <td style=" text-align: left; width: 20%"> <strong> ORS/BURS Date: </strong></td>
                            <td style=" text-align: left;"> <?= $purchaseOrder->date_ors_burs  ?> </td>
                        </tr>
                    </table>
                    <hr style="border-bottom: 1px solid #77ccff;" />

                    <left>
                        <h5><i><strong> Item/Equipment Details </strong></i></h5>
                    </left>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?= GridView::widget([
                                'dataProvider' => $dataProvider,
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
                                        'value' => function ($model) {
                                            $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                                            $items = PrItems::find()->where(['id' => $bidding->item_id])->one();
                                            return $items->unit;
                                        },
                                    ],
                                    [
                                        'attribute' => 'item_remarks',
                                        'format' => 'ntext',
                                        'options' => ['style' => 'width:40%'],
                                        'hAlign' => 'center',
                                        'contentOptions' => ['style' => 'text-align: left'],
                                        'header' => 'ITEM DESCRIPTION',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'value' => function ($model) {
                                            $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                                            $items = PrItems::find()->where(['id' => $bidding->item_id])->one();
                                            return $items->item_name;
                                        },
                                    ],
                                    [
                                        'attribute' => 'unit_cost',
                                        'header' => 'UNIT COST',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'value' => function ($model) {
                                            $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                                            $items = PrItems::find()->where(['id' => $bidding->item_id])->one();
                                            return $bidding->supplier_price;
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
                                        'value' => function ($model) {
                                            $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                                            $items = PrItems::find()->where(['id' => $bidding->item_id])->one();
                                            return $items['quantity'];
                                        },
                                    ],
                                    [
                                        'attribute' => 'supplier_totalprice',
                                        'header' => 'TOTAL PRICE',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'value' => function ($model) {
                                            $bidding = BiddingList::find()->where(['id' => $model->bid_id])->one();
                                            $items = PrItems::find()->where(['id' => $bidding->item_id])->one();
                                            return $items['quantity'] * $bidding['supplier_price'];
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

                    <hr style="border-bottom: 1px solid #77ccff;" />

                    <left>
                        <h5><i><strong> Additional Charges </strong></i></h5>
                    </left>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?= GridView::widget([
                                'id' => 'grid-id',
                                'dataProvider' => $dataProvider3,
                                'showFooter' => true,
                                'showPageSummary' => true,
                                'options' => [
                                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                                ],
                                'columns' => [
                                    [
                                        'class' => 'kartik\grid\SerialColumn',
                                        'options' => ['style' => 'width:3%'],
                                    ],
                                    [
                                        'attribute' => 'add_service_id',
                                        'header' => 'ADDITIONAL CHARGES',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'hAlign' => 'center',
                                        'value' => function ($model) {
                                            // $addServicename = AddservicesLists::find()->where(['id' => $model->add_service_id])->one();
                                            if ($model == NULL) {
                                                return '-';
                                            }
                                            
                                            // return $addServicename->service_name;
                                        },
                                    ],
                                    [
                                        'class' => DataColumn::class,
                                        'attribute' => 'additional_amount',
                                        'header' => 'AMOUNT',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'format' => [
                                            'decimal', 2
                                        ],
                                        'hAlign' => 'right',
                                        'pageSummary' => true,
                                    ],
                                ]
                            ]);
                            ?>
                        </div>
                    </div>

                    <hr style="border-bottom: 1px solid #77ccff;" />

                    <left>
                        <h5><i><strong> LESS DEDUCTION </strong></i></h5>
                    </left>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <?= GridView::widget([
                                'id' => 'grid-id',
                                'dataProvider' => $dataProvider2,
                                'showFooter' => true,
                                'showPageSummary' => true,
                                'options' => [
                                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                                ],
                                'columns' => [
                                    [
                                        'class' => 'kartik\grid\SerialColumn',
                                        'options' => ['style' => 'width:3%'],
                                    ],
                                    [
                                        'attribute' => 'deduction_id',
                                        'header' => 'Less Deduction',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'hAlign' => 'center',
                                        'value' => function ($model) {
                                            $deductionName = DeductionLists::find()->where(['id' => $model->deduction_id])->one();
                                            return $deductionName->code;
                                        },
                                    ],
                                    [
                                        'class' => DataColumn::class,
                                        'attribute' => 'deduction_amount',
                                        'header' => 'AMOUNT',
                                        'headerOptions' => ['style' => 'color:#337ab7'],
                                        'format' => [
                                            'decimal', 2
                                        ],
                                        'hAlign' => 'right',
                                        'pageSummary' => true,
                                    ],
                                ]
                            ]);
                            ?>
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
    .dv-form {
        font-size: smaller;
    }

    .dv-Form {
        padding-bottom: 1rem;
        padding: 1rem;
        border-radius: 1rem;
        background-color: white;
        display: inline-block;
        width: 100%;
    }

    .dv {
        border-top-left-radius: 20px;
        border-top-right-radius: 20px;
        background-color: #18B6D5;
    }
</style>