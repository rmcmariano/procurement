<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\modules\cashier\models\search\CheckType */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Check Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-type-index box box-primary">
    <?php Pjax::begin(); ?>
    <div class="box-header with-border">
        <?= Html::a('Create Check Type', ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'check_code',
                'description',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
    <?php Pjax::end(); ?>
</div>
