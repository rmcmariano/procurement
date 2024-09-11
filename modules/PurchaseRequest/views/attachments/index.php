<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attachments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attachments-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Attachments', ['create'], ['class' => 'btn btn-success']) ?>
    </p>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pr_no',
            'attach_rfq',
            'attach_abstract',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
