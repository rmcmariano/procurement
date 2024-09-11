<?php

use app\modules\PurchaseRequest\models\BidBulletin;
use app\modules\PurchaseRequest\models\ItemSpecificationSearch;
use kartik\grid\GridView;
use yii\bootstrap\Nav;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Home', 'url' => ['bac-prindex']];
?>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<div>
  <?= Nav::widget([
    'options' => ['class' => 'nav-tabs'],
    'items' => [

      [
        'label' => 'PR DETAILS',
        'url' => ['purchase-request/bac-prview', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'SCHEDULING DETAILS',
        'url' => ['purchase-request/bac-quotationindex', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'BID BULLETIN',
        'url' => ['purchase-request/pr-itemsbidbulletinlist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
        'visible' => in_array($model->mode_pr_id, ['1', '2', '3']),
        'active' => true,
      ],
      [
        'label' => 'SUBMISSION & OPENING OF BIDS',
        'url' => ['bidding/bac-biddingitemlist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'COMPLYING BIDDERS',
        'url' => ['bidding/bac-bidding-complyinglist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
      [
        'label' => 'RESOLUTION',
        'url' => ['bidding/bac-resolutionlist', 'id' => $model->id],
        'options' => ['class' => 'nav-tab'],
      ],
    ],
  ]) ?>
</div>

<div class="pr-disapproval">
  <p>
  <div class="panel panel-default" id="div-bulletin">
    <div style="padding: 20px">
      <left>
        <i>
          <h5>Purchase Request Number:</h5>
        </i>
        <h1><?= $model->pr_no ?></h1>
      </left>
      <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />
      <i>
        <h3>Lists of Bidbulletin:</h3>
      </i>

      <div style="text-align: left;">

        <?= Html::a('See All Accepted Bulletin', ['bidbulletin-acceptedlist'], ['class' => 'btn btn-info btn-sm']) . ' '; ?>
        <?= Html::button('<span class="glyphicon glyphicon-bell"></span> Notify End-User', ['class' => 'btn btn-warning btn-sm notifyBtn', 'value' => $model["id"]]) . ' '; ?>
        </p>
      </div>

      <?= GridView::widget([
        // 'id' => 'grid-id',
        'dataProvider' => $dataProvider,
        'options' => ['style' => 'width:100%'],
        'options' => [
          'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
        ],
        'export' => false,
        'striped' => true,
        'hover' => true,
        'pjax' => true,
        'panel' => ['type' => 'info',],
        'floatHeader' => true,
        'floatHeaderOptions' => ['scrollingTop' => '5'],
        'columns' => [

          [
            'class' => 'kartik\grid\SerialColumn',
            'options' => ['style' => 'width:2%'],
          ],
          [
            'class' => 'kartik\grid\ExpandRowColumn',
            'options' => ['style' => 'width:2%'],
            'value' => function ($model, $key, $index, $column) {
              return GridView::ROW_COLLAPSED;
            },
            'detail' => function ($model, $key, $index, $column) {

              $searchModel = new ItemSpecificationSearch();
              $searchModel->item_id = $model->id;
              $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

              return Yii::$app->controller->renderPartial('pr_itemspecs_expand_bidbulletin', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
                'model' => $model
              ]);
            },
          ],
          [
            'attribute' => 'bidbulletin_no',
            'options' => ['style' => 'width:10%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'BID BULLETIN #',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'value' => function ($model) {
              $bidbulletin = BidBulletin::find()->where(['id' => $model->bidbulletin_id])->one();
              if ($bidbulletin == NULL) {
                return '-';
              }
              return $bidbulletin->bidbulletin_no;
            }
          ],
          [
            'attribute' => 'time_stamp',
            'options' => ['style' => 'width:10%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'DATE POSTED',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'value' => function ($model) {
              $bidbulletin = BidBulletin::find()->where(['id' => $model->bidbulletin_id])->one();
              if ($bidbulletin == NULL) {
                return '-';
              }
              return Yii::$app->formatter->asDatetime(strtotime($bidbulletin->date_posted), 'php:d-M-Y | h:i A');
            }
          ],
          [
            'attribute' => 'unit',
            'options' => ['style' => 'width:5%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'UNIT',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'item_name',
            'format' => 'ntext',
            'options' => ['style' => 'width:30%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: left'],
            'header' => 'ITEM DESCRIPTION',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'unit_cost',
            'format' => [
              'decimal', 2
            ],
            'options' => ['style' => 'width:10%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: right'],
            'header' => 'UNIT COST',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'quantity',
            'options' => ['style' => 'width:5%'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: center'],
            'header' => 'QUANTITY',
            'headerOptions' => ['style' => 'color:#337ab7'],
          ],
          [
            'attribute' => 'total_cost',
            'format' => [
              'decimal', 2
            ],
            'value' => function ($model) {
              if ($model->status == 18) {

                return '';
              }
              return $model['total_cost'];
            },
            'options' => ['style' => 'width:10%; text-align: right'],
            'hAlign' => 'center',
            'contentOptions' => ['style' => 'text-align: right'],
            'header' => 'TOTAL COST',
            'headerOptions' => ['style' => 'color:#337ab7'],

          ],
        ]
      ]);
      ?>
    </div>
  </div>
  </p>
</div>

<?php
$this->registerJs(
  <<<JS

        $('.notifyBtn').on('click', function() {
            var remarks = "";
            swal({
                title: "Notify End-user for Additional Details of Items for Bidbulletin",
                icon: "info",
                buttons: {
                    confirm: {
                        text: "Yes",
                        value: true,
                    },
                    cancel: true,
                },
                text: 'Remarks:',
                content: "input",
                closeOnClickOutside: false,
                closeOnEsc: false,
            })
            .then((willDisapprove) => {
                if (willDisapprove != null) {
                    swal("Success.", {
                        icon: "success",
                    }).then((value) => {
                        $.ajax({
                            url: "https://procurement.itdi.ph/PurchaseRequest/purchase-request/bidbulletin-notify-enduser",
                            type: 'post',
                            data: {
                                "remarks": willDisapprove,
                                id: $(this).val()
                            }
                        }); 
                        console.log(willDisapprove);
                        location.reload();
                    });       
                }
                else{
                    swal("Canceled", {
                        icon: "warning",
                        closeOnClickOutside: false,
                        closeOnEsc: false,
                    });
                }
            });
        });

JS
);
?>

<style>
  .nav-tabs li a {
    background-color: #5F9EA0;
    color: #000000;
    font-weight: bold;
    border-top-right-radius: 16px 16px;
  }

  .nav-tabs li.active {
    height: 40px;
    line-height: 40px;
    width: 300px;
    background: #5F9EA0;
    border-top-left-radius: 16px 16px;
    border-top-right-radius: 16px 16px;
    color: #5F9EA0;
    margin-right: 5px;
    font-weight: bold;

  }

  .nav-tabs li.active:after {
    content: "";
    display: block;
    position: absolute;
    border-left: 35px #5F9EA0;
    left: 145px;
    border-top: 35px solid transparent;
    bottom: 0;
  }
</style>