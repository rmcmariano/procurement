<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\LessDeductions;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseOrderItems;
use NumberToWords\NumberToWords;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


function convertNumberToWord($num = false)
{
	$num = str_replace(array(',', ' '), '', trim($num));
	if (!$num) {
		return false;
	}
	$num = (int) $num;
	$words = array();
	$list1 = array(
		'', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
		'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
	);
	$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
	$list3 = array(
		'', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
		'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
		'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
	);
	$num_length = strlen($num);
	$levels = (int) (($num_length + 2) / 3);
	$max_length = $levels * 3;
	$num = substr('00' . $num, -$max_length);
	$num_levels = str_split($num, 3);
	for ($i = 0; $i < count($num_levels); $i++) {
		$levels--;
		$hundreds = (int) ($num_levels[$i] / 100);
		$hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
		$tens = (int) ($num_levels[$i] % 100);
		$singles = '';
		if ($tens < 20) {
			$tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
		} else {
			$tens = (int)($tens / 10);
			$tens = ' ' . $list2[$tens] . ' ';
			$singles = (int) ($num_levels[$i] % 10);
			$singles = ' ' . $list1[$singles] . ' ';
		}
		$words[] = $hundreds . $tens . $singles . (($levels && (int) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
	} //end for loop
	$commas = count($words);
	if ($commas > 1) {
		$commas = $commas - 1;
	}
	return implode(' ', $words);
}

?>


<div class="purchase-order">
	<div style="text-align:center; font-size: smaller">
		<strong>WORK ORDER </strong>
		<br> INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE
	</div>
	<br>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 65%; border-right: 1px solid; font-size: smaller"> Supplier: <strong><u> <?= $bidding->supplierdisplay->supplier_name ?> </u></strong></td>
			<td style="width: 35%; font-size: smaller;"> P.O./W.O. No.:<strong> <u> <?= $purchaseOrder->po_no ?> </u></strong></td>
		</tr>
		<tr>
			<td style="width: 65%;border-right: 1px solid;  font-size: small"> Address: <?= $bidding->supplierdisplay->supplier_address ?></td>
			<td style="width: 35%;  font-size: small"> Date: <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->po_date_created), 'php:M d, Y') ?> </td>
		</tr>
		<tr>
			<td style="width: 65%; border-right: 1px solid;  font-size: small"> TIN: <?= $bidding->supplierdisplay->tin_no ?></td>
			<td style="width: 35%;  font-size: small"> Mode of Procurement: <?= $purchaserequest->procurementmode->mode_name ?></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="font-size: small"> Gentlemen:</td>
		</tr>
		<tr>
			<td style=" padding-left: 8%; padding-bottom: 2%;  font-size: small"> Please furnish this Office the following articles subject to the terms and conditions contained herein:</td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 65%; border-right: 1px solid; font-size: small;padding-right: 24%  "> Place of Delivery: <?= $purchaseOrder->place_delivery ?> </td>
			<td style="width: 35%; font-size: small;"> Delivery Term: <?= $purchaserequest->delivery_period ?> </td>
		</tr>
		<tr>
			<td style="width: 65%; border-right: 1px solid;  font-size: small; padding-right: 24% "> Date of Delivery: </td>
			<td style="width: 35%; font-size: small; padding-right: 15%"> Payment Term: <?= $purchaseOrder->payment_term ?></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 100%; text-align:center; font-size: small ">
		<tr>
			<td style="width:10%; border: 1px solid;  font-size: small;"><strong>Stock/<br>Property<br> No.</strong></td>
			<td style="width: 7%; border:1px solid; font-size: small;"><strong> Unit </strong></td>
			<td style="width: 43%; border: 1px solid; font-size: small;"><strong> Item Description </strong></td>
			<td style="width: 5%; border: 1px solid; font-size: small;"><strong> Qty </strong></td>
			<td style="width: 12%; border: 1px solid; font-size: small;"><strong> Unit Cost </strong></td>
			<td style="width: 13%; border: 1px solid; font-size: small;"><strong>Amount </strong></td>
		</tr>

		<?php
		$amount = 0;

		foreach ($biddingLists as $biddingOne) {
			// $testBidding = BiddingList::find()->where(['id' => $ordItem->bid_id])->one();
			$testDescription = PrItems::find()->where(['id' => $biddingOne->item_id])->one();
			$totalVat = PurchaseOrderItems::find()->where(['bid_id' => $biddingOne->id])->all();

			$itemPrice = $testDescription->quantity * $biddingOne->supplier_price;
			$amount += $itemPrice;

			echo
			'<tr style = "font-size: small; border: 1px solid; width: 100% ">
					<td style = "width: 10%; text-align: center; font-size: small; border-right: 1px solid;">' . $testDescription->stock . '</td>
					<td style = "width: 7%; text-align: center; font-size: small;  border-right: 1px solid;">' . $testDescription->unit . '</td>
					<td style = "width: 43%; text-align: left; font-size: small; border-right: 1px solid;">' . '<br>' . ($biddingOne->item_remarks == $testDescription->id ? $testDescription->item_name : $biddingOne->item_remarks) . '</td>
					<td style = "width: 5%; text-align: center; font-size: small; border-right: 1px solid;">' . $testDescription->quantity . '</td>
					<td style = "width: 12%; text-align: right; font-size: small;  border-right: 1px solid;">' . number_format($biddingOne->supplier_price, 2) . '</td>
					<td style = "width: 13%; text-align: right; font-size: small;  border-right: 1px solid;">' .  number_format($testDescription['quantity'] * $biddingOne['supplier_price'], 2) . '</td>	
			</tr>';

			foreach ($itemSpecs as $spec) {
				echo
				'<tr>
					<td style = "width: 10%; border-right: 1px solid; border-bottom: 1px solid;  font-size: small">' . '</td>
					<td style = "width: 7%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . '</td>
					<td style = "width: 43%; border-right: 1px solid;  font-size: small; border-bottom: 1px solid; text-align: left;">' . nl2br($spec->description) . '</td>
					<td style = "width: 5%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . $spec->quantity . '</td>
					<td style = "width: 12%; text-align: right; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . '</td>
					<td style = "width: 12%; text-align: right; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . '</td> 
			</tr>';
			}

			$totalDeduct = 0;
			$totalAmount = 0;
			$totalDeductperItem = 0;
			$totalDeductionAmount = 0;
			$finalDeduct = 0;
			$totalAmountAll = 0;

			foreach ($orderItems as $orderItem) {

				$totalAll = $orderItem->deduction_amount;
				$totalAmountAll += $totalAll;
				$finalDeduct = $amount - $totalAmountAll;
			}

			foreach ($totalVat as $orderItemone) {

				$total = $orderItemone->deduction_amount;
				$totalDeduct += $total;
				$totalDeductperItem = $itemPrice - $totalDeduct;
				$totalDeductionAmount = $amount - $totalDeduct ;

				echo
				'<tr style = "font-size: small;  border:1px solid; width: 100%;">
								<td style="width:10%; border: 1px solid;  font-size: small;"></td>
								<td style="width: 7%; border:1px solid; font-size: small;"></td>
								<td colspan = "2"  style = " padding-top: 2%; text-align: right; font-size: small; border-right: 1px solid; padding-right: 2% ">less ' . $orderItemone->deductionList->code . ':</td>
								<td style = "width: 12%; padding-top: 2%; text-align: right; font-size: small;  border-right: 1px solid;"></td>;
								<td style = "width: 12%; padding-top: 2%; text-align: right; font-size: small;  border-right: 1px solid;">' . number_format($orderItemone->deduction_amount, 2) . '</td>;
						</tr>';
				
			}
			echo
				'<tr style = "font-size: small;  border:1px solid; width: 100%;">
				<td style="width:10%; border: 1px solid;  font-size: small;"></td>
				<td style="width: 7%; border:1px solid; font-size: small;"></td>
				<td colspan="2" style=" text-align: right; border-right: 1px solid; font-size: small; padding-top: 2% ; padding-right: 2% "></td>
				<td colspan="2" style=" text-align: right; font-size: small; padding-top: 2% "><strong>'. number_format($totalDeductperItem, 2) . '</strong></td>
				</tr>';
		}
		?>

		<tr style=" border-top: 1px solid; width: 100%">
			<td style="width:10%; border: 1px solid;  font-size: small;"></td>
			<td style="width: 7%; border:1px solid; font-size: small;"></td>
			<td style="width: 43%; border: 1px solid; font-size: small;"></td>
			<td style="width: 5%; border: 1px solid; font-size: small;"></td>
			<td style="width: 12%; border: 1px solid; font-size: small;"></td>
			<!-- <td style=" width: 13%;text-align: right; font-size: small; padding-top: 2%"><strong> <php echo number_format($amount, 2); ?> </strong></td> -->
			<td style=" width: 13%;text-align: right; font-size: small; padding-top: 2%"><strong> <?php echo number_format($finalDeduct, 2); ?> </strong></td>
		</tr>
		
		<tr style=" border:1px solid; width: 100%; ">
			<td colspan="3" style="font-size: small; text-align: center; padding-top: 2%">End User: <?= $purchaserequest->enduser ?></td>
			<td style="width: 5%; border: 1px solid; font-size: small;"></td>
			<td style="width: 12%; border: 1px solid; font-size: small;"></td>
			<td style="width: 13%; border: 1px solid; font-size: small;"></td>
		</tr>

		<?php
		$totalAdditional = 0;
		$totalAddamount = 0;
		$finalAmount = 0;

		foreach ($addServices as $addService) {

			$total = $addService->additional_amount;
			$totalAdditional += $total;
			// $totalAddamount = $totalAmount + $total;
			$finalAmount = $finalDeduct + $totalAdditional;

			echo
			'<tr style = "font-size: small;  border:1px solid; width: 100%;">
							<td style="width:10%; border: 1px solid;  font-size: small;"></td>
							<td style="width: 7%; border:1px solid; font-size: small;"></td>
							<td colspan = "2"  style = " padding-top: 2%; text-align: right; font-size: small; border-right: 1px solid; padding-right: 2% ">Add ' .($addService->addservicesLists == NULL ? '' : $addService->addservicesLists->service_name). ':</td>
							<td style = "width: 12%; padding-top: 2%; text-align: right; font-size: small;  border-right: 1px solid;">' . number_format($addService->additional_amount, 2) . '</td>;
							<td style="width: 13%; border: 1px solid; font-size: small;"></td>
					</tr>';
		}
		?>
		<tr style=" border: 1px solid;">
			<td style="width:10%; border: 1px solid;  font-size: small;"></td>
			<td style="width: 7%; border:1px solid; font-size: small;"></td>
			<td colspan="2" style=" text-align: right; border-right: 1px solid; font-size: small; padding-top: 2% ; padding-right: 2% ">Total Additional Services: </td>
			<td colspan="2" style=" text-align: right; font-size: small; padding-top: 2% "> + <?php echo number_format($totalAdditional, 2); ?></td>
		</tr>

		<tr style=" border: 1px solid;">
			<td style="width:10%; border: 1px solid;  font-size: small;"></td>
			<td style="width: 7%; border:1px solid; font-size: small;"></td>
			<td style="width: 43%; border: 1px solid; font-size: small;"></td>
			<td style="width: 5%; border: 1px solid; font-size: small;"></td>
			<td style="width: 12%; border: 1px solid; font-size: small;"></td>
			<td style="width: 13%; text-align: right; font-size: small; padding-top: 2%; padding-bottom: 2% "><strong><?php echo number_format($finalAmount, 2); ?></strong></td>
		</tr>

		<tr style=" border: 1px solid;">
			<td colspan="6" style=" text-align: left; font-size: small;"><strong>Total Amount in Word: </strong> <?= NumberToWords::transformNumber('en', $finalAmount) ?> <?= ($biddingOne->totalCostDecimal != 0 && $biddingOne->totalCostDecimal != null) ? ' pesos and ' . NumberToWords::transformNumber('en', $biddingOne->totalCostDecimal) . ' centavos' : '' ?></td>
		</tr>

		<tr>
			<td colspan="6" style="font-size: small;padding-top: 2%; padding-bottom: 2%; text-align: left; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;In case of failure to make the full delivery within the time specified above, a penalty of one-tenth (1/10) of one percent for every day of delay shall be imposed on the undelivered item/s.</td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: small; text-align: left; padding-left: 10%">Conforme: </td>
			<td colspan="3" style="font-size: small;text-align: left;">Very truly yours, </td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top: 5%; padding-left: 10%; text-align: left; text-transform: uppercase;">
				<hr style="text-align: left;width: 55%;">
			</td>
			<td colspan="3" style="padding-top: 5%;text-align: left; text-transform: uppercase;"> <?= $purchaserequest->approvedChief->fullname ?>
				<hr style="text-align: left;width: 95%">
			</td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: x-small; text-align: left; padding-left: 10%">Signature over Printed Name of Supplier <br> <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->date_ors_burs), 'php:M d, Y') ?> </td>
			<td colspan="3" style="font-size: x-small;text-align: center;">Signature over Printed Name of Authorized <br> OIC Deputy Director, R&D <br> Designation </td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: small; text-align: left; padding-top: 2%; border-top: 1px solid; border-right: 1px solid">Fund Cluster: </td>
			<td colspan="3" style="font-size: small;text-align: left; padding-top: 2%; border-top: 1px solid; ">ORS/BURS No.: <u> <?= $purchaseOrder->ors_burs_num ?> </u></td>
		</tr>
		<tr>
			<td colspan="3" style="font-size: small; text-align: left; padding-top: 2%; border-right: 1px solid">Funds Available: <u> <?= $purchaserequest->chargedisplay->project_title ?></u> </td>
			<td colspan="3" style="font-size: small;text-align: left; padding-top: 2%">Date of the ORS/BURS: <u> <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->date_ors_burs), 'php:M d, Y') ?> </u></td>
		</tr>
		<tr>
			<td colspan="3" style="padding-top: 5%; padding-left: 10%; text-align: left; text-transform: uppercase; border-right: 1px solid">RAISA A. TONGSON
				<hr style="text-align: left;width: 80%;">
			</td>
			<td colspan="3" style="font-size: small;text-align: left; padding-top: 2%">Amount: <u> Php <?= number_format($amount, 2) ?> </u></td>

		</tr>

		<tr>
			<td colspan="3" style="font-size: x-small; text-align: left; padding-left: 10%; border-right: 1px solid">Signature over Printed Name of Chief Accountant/Head of <br> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;Accounting Division/Unit </td>

		</tr>

	</table>




</div>