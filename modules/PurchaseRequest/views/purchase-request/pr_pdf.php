<?php

use app\modules\PurchaseRequest\models\ItemSpecification;
use app\modules\PurchaseRequest\models\PrItems;

?>


<div class="control" style="font-size: 10pt;">
	<div class="pr" id="pr">
		<div style="text-align: center">
			<div style="text-align: right"><i>Appendix 60</i></div>
			<h4><strong>PURCHASE REQUEST</strong></h4>
			<table style="font-size:10pt; font-family: Arial, Helvetica, sans-serif; width: 100% ">
				<tr>
					<td style="width: 70%">
						<strong>Entity Name: INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE</strong>
					</td>
					<td style="width: 30%">
						<strong>Fund Cluster: ___________</strong>
					</td>
				</tr>
			</table>

		</div>
	</div>

	<table style="border-collapse: collapse; border: 1px solid; font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:center;  ">
		<tr>
			<td rowspan="2" style="width:20%; border-right: 1px solid black; font-size: small; text-align: initial"><strong>Office/Section:</strong><br><?= $model->section == NULL ? '' : $model->sectiondisplay->section_code ?></td>
			<td style="width:56%; border-right: 1px solid black; font-size: small; text-align: left"><strong>PR No.: </strong>
				<?php
				if ($model->revised_series_no == 0 || $model->revised_series_no === NULL) {
					echo $model->pr_no;
				} else {
					switch ($model->revised_series_no) {
						case 1:
							echo $model->pr_no . '-A';
							break;
						case 2:
							echo $model->pr_no . '-B';
							break;
						case 3:
							echo $model->pr_no . '-C';
							break;
						case 4:
							echo $model->pr_no . '-D';
							break;
						case 5:
							echo $model->pr_no . '-E';
							break;
						default:
							// Handle other cases if needed
							break;
					}
				}
				?>
			</td>
			<td style="width:24%; border-right: 1px solid black; font-size: small; text-align: left"><strong>Date: </strong> <?= Yii::$app->formatter->asDatetime(strtotime($model->date_of_pr), 'php:F d, Y')  ?> </td>
		</tr>

		<tr>
			<td style="width:56%; border-right: 1px solid; font-size: small; text-align:left; padding-bottom:1%"><strong> Responsibility Center Code: </strong> </td>
			<td style="width:24%; border-right: 1px solid; font-size: small;"> </td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:center;">
		<tr>
			<th style="width: 10%; border: 1px solid black; font-size: small">Stock/<br>Property No.</th>
			<th style="width: 10%; border: 1px solid black; font-size: small;">Unit</th>
			<th style="width: 50%; border: 1px solid black; font-size: small;">Item Description</th>
			<th style="width: 6%;  border: 1px solid black; font-size: small;">Qty</th>
			<th style="width: 12%; border: 1px solid black; font-size: small;">Unit Cost</th>
			<th style="width: 12%; border: 1px solid black; font-size: small;">Total Cost</th>
		</tr>

		<!-- Other description Dynamic Form - START -->
		<?php
		$prItems = PrItems::find()->where(['pr_id' => $_GET['id']])->all();
		$itemNo = 1;

		// for item Name (pritems tbl)
		foreach ($prItems as $prItem) {
			$specs = ItemSpecification::find()->where(['item_id' => $prItem['id']])->all();
			$itemTotal = $prItem->quantity * $prItem->unit_cost;

			echo
			'<tr>
					<td style = "width: 10%; border: 1px solid black; font-size: small">' . $itemNo++ . '</td>
					<td style = "width: 8%;  border: 1px solid black; font-size: small; text-align: center;">' . $prItem->unit . '</td>
					<td style = "width: 42%; border: 1px solid black; font-size: small; text-align: left;">' . nl2br($prItem->item_name) . '</td>
					<td style = "width: 6%;  border: 1px solid black; font-size: small; text-align: center;">' . $prItem->quantity . '</td>
					<td style = "width: 12%; border: 1px solid black; font-size: small; text-align: right;">' . number_format($prItem->unit_cost, 2)  . '</td>
					<td style = "width: 12%; border: 1px solid black; font-size: small; text-align: right;">' . number_format($itemTotal, 2) . '</td> 
			</tr>';

			// for item_specification (item_specification tbl)
			foreach ($specs as $spec) {
				echo
				'<tr class="break-inside-avoid">
					<td style="width: 10%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
					<td style="width: 8%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;"></td>
					<td style="width: 42%; border-right: 1px solid; font-size: small; border-bottom: 1px solid; text-align: left;">' . nl2br($spec->description) . '</td>
					<td style="width: 6%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . $spec->quantity . '</td>
					<td style="width: 12%; text-align: right; border-right: 1px solid; border-bottom: 1px solid; font-size: small;"></td>
					<td style="width: 12%; text-align: right; border-right: 1px solid; border-bottom: 1px solid; font-size: small;"></td>
				</tr>';
			}
		}
		?>
		<tr>
			<td style="width: 10%; border-right: 1px solid; border-bottom: 1px solid; font-size: small; padding: 1.5%"></td>
			<td style="width: 8%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 42%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 6%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
		</tr>
		<tr>
			<td style="width: 10%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 8%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 42%; border-right: 1px solid; border-bottom: 1px solid; font-size: small; text-align: left">Delivery Period: &nbsp;&nbsp; <?= $model->delivery_period ?></td>
			<td style="width: 6%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
		</tr>
		<tr>
			<td style="width: 10%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 8%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 42%; border-right: 1px solid; border-bottom: 1px solid; font-size: small; text-align: left;">Warranty: &nbsp;&nbsp; <?= $model->warranty ?></td>
			<td style="width: 6%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
		</tr>
		<tr>
			<td style="width: 10%; border-right: 1px solid; border-bottom: 1px solid; font-size: small; padding: 1.5%"></td>
			<td style="width: 8%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 42%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 6%;  border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
			<td style="width: 12%; border-right: 1px solid; border-bottom: 1px solid; font-size: small"></td>
		</tr>

		<!-- Other Description Dynamic Form - END -->
		<tr>
			<?php
			$description = PrItems::find()->where(['pr_id' => $_GET['id']])->all();
			$total = 0;
			foreach ($description as $prd) {
				$prdtotal_amount = $prd->quantity * $prd->unit_cost;
				$total += $prdtotal_amount;
			}
			echo
			'<tr>
				<td colspan = "5" style = "width: 10%; border-right: 1px solid;  font-size: small; text-align: right"><strong>TOTAL'  . '</strong></td>
				<td style = "width: 12%; text-align: right; border-right: 1px solid;  font-size: small;"><strong>' . number_format($total, 2) . '</strong></td> 
			</tr>';
			?>
		</tr>

	</table>

	<table style="border: 1px solid; border-collapse: collapse; font-size:10pt; font-family: Arial, Helvetica, sans-serif; width: 100%;">
		<tr>
			<td style="width: 10%; border-bottom: 1px solid ">Purpose:</td>
			<td colspan="5" style="border-right: 1px solid black; border-bottom: 1px solid"><?= ($model->purpose == NULL ? 'N/A' : $model->purpose) ?></td>
		</tr>
		<tr>
			<td style="width: 10%; border-bottom: 1px solid">End-user:</td>
			<td colspan="5" style="border-right: 1px solid black; border-bottom: 1px solid; text-transform:capitalize"><?= ($model->enduser) ?></td>
		</tr>
		<tr>
			<td style="width: 10%; border-bottom: 1px solid">Charge to:</td>
			<td colspan="5" style="border-right: 1px solid black;">
				<!-- <= ($model->pr_type_id == 3 && $model->charge_to == 0 ? 'GAA' : ($model->pr_type_id == 1 && $model->charge_to == 0 ? 'SDO' : $model->chargedisplay->project_title)) ?> -->
				<?= ($model->charge_to == 0 && $model->charge_to == NULL ? 'GAA' : $model->chargedisplay->project_title) ?></span>
			</td>
			</td>
		</tr>
	</table>

	<table style="border: 1px solid; border-collapse: collapse; font-size:10pt; font-family: Arial, Helvetica, sans-serif; width: 100%;">
		<tr>
			<td style="width: 20%; border: 1px solid black;"></td>
			<td style="width: 40%; border: 1px solid black;">Requested By:</td>
			<td style="width: 40%; border: 1px solid black;">Approved By:</td>
		</tr>
		<tr>
			<td style="width: 20%; border: 1px solid black;">Signature:</td>
			<td style="width: 40%; border: 1px solid black;"></td>
			<td style="width: 40%; border: 1px solid black;"></td>
		</tr>
		<tr>
			<td style="width: 20%; border: 1px solid black;">Printed Name:</td>
			<td style="width: 40%; border: 1px solid black;"><strong><?= ($model->requested_by == NULL ? '-' : $model->profile->fname . ' ' . $model->profile->lname) ?></strong></td>
			<td style="width: 40%; border: 1px solid black;"><strong><?= ($model->approved_by == NULL ? '-' :  $model->approvedBy->fname . ' ' . $model->approvedBy->lname) ?></strong></td>
		</tr>
		<tr>
			<td style="width: 20%; border: 1px solid black;">Designation:</td>
			<td style="width: 40%; border: 1px solid black;"><?= ($model->position == NULL ? '-' : $model->position->position_title)  ?></td>
			<td style="width: 40%; border: 1px solid black;"><?= ($model->approvedbyPosition == NULL ? '-' : $model->approvedbyPosition->position_title) ?></td>
		</tr>

	</table>
</div>

<style>
	.break-inside-avoid {
		page-break-inside: avoid;
	}
</style>