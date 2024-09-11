<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\cashier\models\CheckType */

$this->title = 'Update Check Type: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Check Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="check-type-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
