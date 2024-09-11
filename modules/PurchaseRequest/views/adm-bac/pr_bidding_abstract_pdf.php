<?php

use app\modules\user\models\User;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\user\models\Profile;
use yii\helpers\ArrayHelper;


$assignatory = User::find()->where(['id' => $latest_assignatory->chairperson_id])->one();

$co_assignatory = User::find()->where(['id' => $latest_assignatory->co_chairperson_id])->one();

$listData = ArrayHelper::map($members, 'members_id', function ($model) {
    return $model['members_id'];
});

$member_assignatory = User::find()->where(['id' => $listData])->asArray()->all();

$count = BiddingList::find()->select(['supplier_id'])->where(['supplier_id' => $bidId])->distinct();

?>

<div>
    <?php
    $itemNum = 1;
    $itemNums = []; // Initialize item number counter
    $supplierCount = count($suppliers);

    $currentPage = 1; // Track current page number
    $perPage = 5; // Number of suppliers per page
    $startSupplierIndex = 0; // Start index of suppliers for current page

    while ($startSupplierIndex < $supplierCount) { ?>
        <div>
            <div style="padding-bottom: 10%">
                <table class="abstract-items">
                    <thead>
                        <tr>
                            <th rowspan="2">ITEM NO.</th>
                            <th rowspan="2">QTY</th>
                            <th rowspan="2">UNIT</th>
                            <th rowspan="2">DESCRIPTION OF ARTICLE</th>
                            <th colspan=<?= min($perPage, $supplierCount - $startSupplierIndex) ?>>SUPPLIERS</th>
                        </tr>
                        <tr>
                            <?php for ($i = 0; $i < min($perPage, $supplierCount - $startSupplierIndex); $i++) { ?>
                                <th><?= $suppliers[$startSupplierIndex + $i]->supplier_name ?></th>
                            <?php } ?>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($descriptiontest as $prd) {
                            $itemspecs = ItemSpecification::find()->where(['item_id' => $prd->id])->all();
                        ?>
                            <tr>
                                <td style="width: 5%; text-align: center"><?= isset($itemNums[$prd->id]) ? $itemNums[$prd->id] : ($itemNums[$prd->id] = $itemNum++) ?></td>
                                <td style="width: 5%; text-align: center"><?= $prd->quantity ?></td>
                                <td style="width: 5%; text-align: center"><?= $prd->unit ?></td>
                                <td style="width: 40%; text-align: left"><?= $prd->item_name ?></td>

                                <?php for ($i = 0; $i < min($perPage, $supplierCount - $startSupplierIndex); $i++) {
                                    $supplierIndex = $startSupplierIndex + $i;
                                    $supplier = $suppliers[$supplierIndex];
                                    $price = BiddingList::find()
                                        ->where(['item_id' => $prd->id])
                                        ->andWhere(['supplier_id' => $supplier->id])
                                        ->andWhere(['status' => ['13']])
                                        ->orderBy(['id' => SORT_DESC])
                                        ->one(); ?>

                                <?= ($price == NULL ? '<td style ="text-align: center;"> - </td>' : '<td style="max-height: 10mm; overflow: hidden; text-align: center;">' . number_format($price->supplier_price, 2) . '</td>');
                                } ?>
                            </tr>

                            <?php foreach ($itemspecs as $specs) { ?>
                                <tr>
                                    <td style="width: 5%; text-align: center"></td>
                                    <td style="width: 5%; text-align: center"></td>
                                    <td style="width: 5%; text-align: center"></td>
                                    <td style="width: 40%; text-align: left"><?= nl2br($specs->description) ?></td>

                                    <?php for ($i = 0; $i < min($perPage, $supplierCount - $startSupplierIndex); $i++) {
                                        $supplierIndex = $startSupplierIndex + $i;
                                        $supplier = $suppliers[$supplierIndex];
                                        $price = BiddingList::find()
                                            ->where(['item_id' => $prd->id])
                                            ->andWhere(['supplier_id' => $supplier->id])
                                            ->andWhere(['status' => ['13']])
                                            ->orderBy(['id' => SORT_DESC])
                                            ->one(); ?>

                                    <?= ($price == NULL ? '<td></td>' : '<td style="max-height: 10mm; overflow: hidden; text-align: center;"></td>');
                                    } ?>
                                </tr>
                        <?php }
                        } ?>
                        <tr>
                            <td style="width: 5%; text-align: center"></td>
                            <td style="width: 5%; text-align: center"></td>
                            <td style="width: 5%; text-align: center"></td>
                            <td style="width: 40%; text-align: left"> Delivery period: <?= $purchaserequest->delivery_period ?> <br> Warranty:  <?= $purchaserequest->warranty ?> </td>

                            <?php for ($i = 0; $i < min($perPage, $supplierCount - $startSupplierIndex); $i++) {
                                $supplierIndex = $startSupplierIndex + $i;
                                $supplier = $suppliers[$supplierIndex];
                                $price = BiddingList::find()
                                    ->where(['item_id' => $prd->id])
                                    ->andWhere(['supplier_id' => $supplier->id])
                                    ->andWhere(['status' => ['13']])
                                    ->orderBy(['id' => SORT_DESC])
                                    ->one(); ?>

                            <?= ($price == NULL ? '<td style ="text-align: center;">  </td>' : '<td style="max-height: 10mm; overflow: hidden; text-align: center;">' . '</td>');
                            } ?>
                        </tr>
                    </tbody>
                </table>
                <?php
                $startSupplierIndex += $perPage;
                $currentPage++; ?>
            </div>
        </div>
    <?php } ?>
</div>