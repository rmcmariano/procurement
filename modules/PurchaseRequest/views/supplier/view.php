<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\Supplier */

$this->title = $model->supplier_name;
$this->params['breadcrumbs'][] = ['label' => 'Suppliers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<div class="supplier-view">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h1><?= Html::encode($this->title) ?></h1>
            <hr style="margin-top: -5px !important; border-bottom: 2px solid #F4BC1C;" />

            <div style="text-align:center"> &nbsp;&nbsp;

                <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post',
                    ],
                ]) ?>
            </div>
            <br>

            <table class="table table-responsive" style="text-transform: uppercase">
                <tr>
                    <td style="width: 25%; text-align: left">SUPPLIERS NAME:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->supplier_name ?></span></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">SUPPLIERS ADDRESS:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->supplier_address ?></span></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">OWNERS NAME:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->owner_name ?></span></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">TIN NO.:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->owner_name ?></span></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">TEL NO.:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->tel_no ?></span></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">ACCOUNT NO.:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->account_no ?></span></td>
                </tr>
                <tr>
                    <td style="width: 25%; text-align: left">CLASSIFICATION:</td>
                    <td><span style="font-size: 17px; font-weight: bold"><?= $model->classification_philgeps ?></span></td>
                </tr>
            </table>
        </div>

        <div style="padding: 20px">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'options' => [
                    'style' => 'overflow: auto; word-wrap: break-word; width: 100%;'
                ],
                'striped' => true,
                'hover' => true,
                'export' => false,
                'showPageSummary' => true,
                'pjax' => true,
                'panel' => ['type' => 'info',],
                'floatHeader' => true,
                'floatHeaderOptions' => ['scrollingTop' => '5'],
                'columns' => [

                    [
                        'class' => 'kartik\grid\SerialColumn',
                        'options' => ['style' => 'width:5%'],
                    ],
                    [
                        'attribute' => 'assigned_dept',
                        'options' => ['style' => 'width:25%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: center'],
                        'header' => 'DEPARTMENT',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'contact_person',
                        'options' => ['style' => 'width:45%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: left'],
                        'header' => 'CONTACT PERSON',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                    [
                        'attribute' => 'contact_no',
                        'options' => ['style' => 'width:20%'],
                        'hAlign' => 'center',
                        'contentOptions' => ['style' => 'text-align: right'],
                        'header' => 'CONTACT NO.',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                    ],
                ]
            ]);
            ?>
        </div>
    </div>
</div>