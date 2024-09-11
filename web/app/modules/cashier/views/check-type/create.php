<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\cashier\models\CheckType */

$this->title = 'Create Check Type';
$this->params['breadcrumbs'][] = ['label' => 'Check Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="check-type-create">

    <?= $this->render('_form', [
    'model' => $model,
    ]) ?>

</div>
