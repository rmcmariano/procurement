<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\PurchaseRequest;

$record = "";
$recordLeft = "";
$header = "";
$ctr = 0;

foreach ($items as $prd) {
	$recordLeft = '<td>' . $prd->stock . '</td>
	<td>' . $prd->quantity . '</td>
	<td>' . $prd->unit . '</td>
	<td>' . $prd->item_name . '</td>';

	$recordRight = "";

	foreach ($suppliers as $supplier) {
		if ($ctr < 1) {
			$header .= "<th style='width: 10%'>{$supplier->supplier_name}</th>";
		}
	}
	$ctr++;
	$record .= "<tr>{$recordLeft}</tr>";
}

?>

<div class="evaluation-report">
	<table class="evaluationreport" style="size: 100% ;">
		<thead>
			<tr>
				<th style="text-align: left; width: 30%">Specification</th>
				<?= $header ?>
				<th style="width: 15%">REMARKS</th>
			</tr>
		</thead>

		<tbody>
			<?php
			// foreach ($items as $prd) {
				$pr = PurchaseRequest::find()->where(['id' => $prd->pr_id])->one();
				$itemBidbulletin = ItemSpecification::find()->where(['item_id' => $prd->id])->andWhere(['bidbulletin_status' => 1])->one();

				echo '
				<tr><td style ="text-align: left">' . $prd->itemexplode . '</td></tr>';
				
				foreach ($itemSpecs as $itemspec) {

					foreach ($suppliers as $supplier) {
						$price = BiddingList::find()->where(['item_id' => $prd->id])->andWhere(['supplier_id' => $supplier->id])->andWhere(['status' => ['13']])->orderBy(['id' => SORT_DESC])->one();
						$try = ( '<tr style ="border: 1px"></tr>');
						echo $try;
					}

					echo '<tr>
						<td style ="width: 20%; text-align: left">' . nl2br($itemspec->description) .  '<br><br><strong>Bid Bulletin:</strong> ' .  nl2br($itemspec->bidbulletin_changes) . '</td>';

					foreach ($suppliers as $supplier) {

						$price = BiddingList::find()->where(['item_id' => $prd->id])->andWhere(['supplier_id' => $supplier->id])->andWhere(['status' => ['13']])->orderBy(['id' => SORT_DESC])->one();

						$try = ($price == NULL ? '<td></td>' : '<td style = "text-align: right">' . '</td>');
						echo $try;
					}
					'</tr>';

					echo '<tr>
						<td style ="width: 40%; text-align: left"><strong>Delivery Terms:  </strong>' . $pr->delivery_period .  '</td>';

					foreach ($suppliers as $supplier) {

						$price = BiddingList::find()->where(['item_id' => $prd->id])->andWhere(['supplier_id' => $supplier->id])->andWhere(['status' => ['13']])->orderBy(['id' => SORT_DESC])->one();

						$try = ($price == NULL ? '<td></td>' : '<td style = "text-align: right">' . '</td>');
						echo $try;
					}
					'</tr>';

					echo '<tr>
						<td style ="width: 40%; text-align: left"><strong>Warranty:  </strong>' . $pr->warranty .  '</td>';

					foreach ($suppliers as $supplier) {

						$price = BiddingList::find()->where(['item_id' => $prd->id])->andWhere(['supplier_id' => $supplier->id])->andWhere(['status' => ['13']])->orderBy(['id' => SORT_DESC])->one();

						$try = ($price == NULL ? '<td></td>' : '<td style = "text-align: right">' . '</td>');
						echo $try;
					}
					'</tr>';
				}
			// }
			?>
		</tbody>
	</table>
	<br><br>

	<div style="text-align: left; padding-top: 3%">
		Evaluated By: ______________________________
	</div>

	<div style="text-align: left; padding-top: 3%">
		Date: ______________________________
	</div>


</div>