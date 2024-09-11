<?php


use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;

?>

<div class="item-specification-form">
    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_inner',
        'widgetBody' => '.container-specs',
        'widgetItem' => '.specs-item',
        'limit' => 30,
        'min' => 1,
        'insertButton' => '.add-specs',
        'deleteButton' => '.remove-specs',
        'model' => $modelSpecifications[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'description'
        ],
    ]); ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Item Specification</th>
                <th class="vcenter" style="width: 1%;">
                    <button type="button" class="add-specs btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
                </th>
            </tr>
        </thead>
        <tbody class="container-specs
        ">
            <?php foreach ($modelSpecifications as $index => $modelotherspec) : ?>
               
                <tr class="specs-item">
                    <?php
                    // necessary for update action.
                    if (!$modelotherspec->isNewRecord) {
                        echo Html::activeHiddenInput($modelotherspec, "[{$i}][{$index}]id");
                    }
                    ?>
                    <td class="vcenter" style="width: 30%;">
                        <?= $form->field($modelotherspec, "[{$i}][{$index}]description")->textarea(['placeholder' => "Enter Item Specification"])->label('') ?>
                    </td>
                    <td class="vcenter" style="width: 5%;">
                        <?= $form->field($modelotherspec, "[{$i}][{$index}]quantity")->textInput(['maxlength' => true, 'style' => 'text-align: center']) ?>
                    </td>
                    <td class="vcenter" style="width: 1%;">
                        <button type="button" class="remove-specs btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php DynamicFormWidget::end(); ?>
</div>