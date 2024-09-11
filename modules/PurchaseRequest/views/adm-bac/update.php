<?php

use yii\helpers\Html;

?>
<div class="purchase-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modeldescription' =>$modeldescription,
        'modelAttachmensts' => $modelAttachments,
    ]) ?>

</div>
