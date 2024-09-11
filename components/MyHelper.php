<?php

namespace app\components;

use yii\helpers\Html;
use yii\helpers\Inflector;

class MyHelper
{
    public static function renderLibCells($libName, $columnName, $lib, $discount, $regularCostVar, $discountedCostVar, $fundClusterColumn, $fundClusterItems, $libOptions, $form)
    {
        if (!empty($lib)) : ?>
            <?php foreach ($lib as $key => $val) : ?>
                <tr>
                    <td>
                        <?= $val->$libName ?>
                        <?= Html::activeHiddenInput($val, "[{$val->id}]id", $options = []) ?>
                        <?= Html::hiddenInput('holder-' . Inflector::camelize($val->$libName), ($discount == 0) ? $val->$regularCostVar : $val->$discountedCostVar, ['class' => 'holder']) ?>
                    </td>
                    <?php if ($val->counterpart_id == 1) : ?>

                        <?php if ($val->$fundClusterColumn == 2) : ?>
                            <td class="general-column <?= $columnName ?> client active-column"></td>
                            <td class="general-column <?= $columnName ?> itdi"></td>
                            <td class="trust-column <?= $columnName ?> client active-column"><?= ($discount == 0) ? $val->$regularCostVar : $val->$discountedCostVar ?></td>
                            <td class="trust-column <?= $columnName ?> itdi"></td>
                        <?php else : ?>
                            <td class="general-column <?= $columnName ?> client active-column"><?= ($discount == 0) ? $val->$regularCostVar : $val->$discountedCostVar ?></td>
                            <td class="general-column <?= $columnName ?> itdi"></td>
                            <td class="trust-column <?= $columnName ?> client active-column"></td>
                            <td class="trust-column <?= $columnName ?> itdi"></td>
                        <?php endif; ?>

                    <?php elseif ($val->counterpart_id == 2) : ?>

                        <?php if ($val->$fundClusterColumn == 2) : ?>
                            <td class="general-column <?= $columnName ?> client"></td>
                            <td class="general-column <?= $columnName ?> itdi active-column"></td>
                            <td class="trust-column <?= $columnName ?> client"></td>
                            <td class="trust-column <?= $columnName ?> itdi active-column"><?= ($discount == 0) ? $val->$regularCostVar : $val->$discountedCostVar ?></td>
                        <?php else : ?>
                            <td class="general-column <?= $columnName ?> client"></td>
                            <td class="general-column <?= $columnName ?> itdi active-column"><?= ($discount == 0) ? $val->$regularCostVar : $val->$discountedCostVar ?></td>
                            <td class="trust-column <?= $columnName ?> client"></td>
                            <td class="trust-column <?= $columnName ?> itdi active-column"></td>
                        <?php endif; ?>

                    <?php endif; ?>
                    <td class="text-center"><?= $form->field($val, "[{$val->id}]fundcluster")->dropDownList($fundClusterItems, $libOptions)->label(false) ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
<?php endif;
    }

    public static function getBusinessDay($cutOffHour = 14)
    {
        $sunday = 0;
        $saturday = 6;
        if (date('H') < $cutOffHour && date('w') != $sunday && date('w') != $saturday) {
            return date('Y-m-d');
        } else {
            if (date('w', strtotime('tomorrow')) == $sunday) {
                return date('Y-m-d', strtotime('tomorrow + 1 day'));
            }
            if (date('w', strtotime('tomorrow')) == $saturday) {
                return date('Y-m-d', strtotime('tomorrow + 2 days'));
            }
            return date('Y-m-d', strtotime('tomorrow'));
        }
    }
}
