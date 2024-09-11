<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\BacSignatories */


$this->params['breadcrumbs'][] = ['label' => 'Bac Signatories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bac-signatories-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelmember_signatories' => $modelmember_signatories,
        
       
    ]) ?>

</div>
