<?php

use app\modules\PurchaseRequest\models\LessDeductions;
use app\modules\PurchaseRequest\models\PrItems;
use NumberToWords\NumberToWords;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */


function convertNumberToWord($num = false)
{
	$num = str_replace(array(',', ' '), '', trim($num));
	if (!$num) {
		return false;
	}

	// Split the number into whole and fractional parts
	$parts = explode('.', $num);
	$wholePart = (int)$parts[0];
	$fractionalPart = isset($parts[1]) ? (int)str_pad($parts[1], 2, '0', STR_PAD_RIGHT) : 0;

	$words = [];

	$list1 = array(
		'', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
		'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
	);
	$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
	$list3 = array(
		'', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
		'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
		'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
	);

	// Convert the whole part to words
	$num_length = strlen($wholePart);
	$levels = (int) (($num_length + 2) / 3);
	$max_length = $levels * 3;
	$wholePart = substr('00' . $wholePart, -$max_length);
	$num_levels = str_split($wholePart, 3);
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
	}

	// Convert the fractional part to words
	if ($fractionalPart > 0) {
		$fractionalWords = [];
		if ($fractionalPart < 20) {
			$fractionalWords[] = $list1[$fractionalPart];
		} else {
			$tens = (int)($fractionalPart / 10);
			$units = $fractionalPart % 10;
			$fractionalWords[] = $list2[$tens];
			if ($units > 0) {
				$fractionalWords[] = $list1[$units];
			}
		}
		$fractionalWords[] = 'centavos';
	}

	$commas = count($words);
	if ($commas > 1) {
		$commas = $commas - 1;
	}

	// Combine the whole part and fractional part
	$wholeWords = implode(' ', $words) . ' Pesos';
	if ($fractionalPart > 0) {
		$wholeWords .= ' and ' . implode(' ', $fractionalWords);
	}

	return trim($wholeWords);
}

?>


<div class="purchase-order">
	<div style="text-align:center">
		<strong>PURCHASE ORDER </strong>
		<br> INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE
	</div>
	<br>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%;  font-size:8pt">
		<tr>
			<td style="border-right: 1px solid;" colspan="3"> Supplier: <strong><u> <?= $bidding->supplierdisplay->supplier_name ?> </u></strong></td>
			<td colspan="3"> P.O./W.O. No.:<strong> <u>
						<?php
						if ($purchaseOrder->project_type_series_id == 0 || $purchaseOrder->project_type_series_id === NULL) {
							echo $purchaseOrder->po_no;
						} else if ($purchaseOrder->project_type_series_id == 1) {
							if ($purchaseOrder->item_type_series_id == 1) {
								echo 'GIA' . 'E-' . $purchaseOrder->po_no;
							} else if ($purchaseOrder->item_type_series_id == 2) {
								echo 'GIA' . 'S-' . $purchaseOrder->po_no;
							} else if ($purchaseOrder->item_type_series_id == 3) {
								echo 'GIA' . 'C-' . $purchaseOrder->po_no;
							} else if ($purchaseOrder->item_type_series_id == NULL) {
								echo 'GIA-' . $purchaseOrder->po_no;
							}

						} else if ($purchaseOrder->project_type_series_id == 2) {
							if ($purchaseOrder->item_type_series_id == 1) {
								echo 'GAA' . 'E-' . $purchaseOrder->po_no;
							} else if ($purchaseOrder->item_type_series_id == 2) {
								echo 'GAA' . 'S-' . $purchaseOrder->po_no;
							} else if ($purchaseOrder->item_type_series_id == 3) {
								echo 'GAA' . 'C-' . $purchaseOrder->po_no;
							}  else if ($purchaseOrder->item_type_series_id == NULL) {
								echo 'GAA-' . $purchaseOrder->po_no;
							}
						}
						?>

					</u></strong>
			</td>
		</tr>
		<tr>
			<td style="width: 65%;border-right: 1px solid;" colspan="3"> Address: <?= $bidding->supplierdisplay->supplier_address ?></td>
			<td colspan="3"> Date: <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->po_date_created), 'php:M d, Y') ?> </td>
		</tr>
		<tr>
			<td style="width: 65%; border-right: 1px solid;" colspan="3"> TIN: <?= $bidding->supplierdisplay->tin_no ?></td>
			<td colspan="3"> Mode of Procurement: <?= $purchaserequest->procurementmode->mode_name ?></td>
		</tr>
		<tr>
			<td colspan="6" style="width: 65%; border-top: 1px solid;"> Gentlemen:</td>
		</tr>
		<tr>
			<td colspan="6"> Please furnish this Office the following articles subject to the terms and conditions contained herein:</td>
		</tr>
		<tr>
			<td style="width: 65%; border-top: 1px solid;  border-right: 1px solid;" colspan="3"> Place of Delivery: <?= $purchaseOrder->place_delivery ?> </td>
			<td style="width: 35%;  border-top: 1px solid;" colspan="3"> Delivery Term: <?= $purchaserequest->delivery_period ?> </td>
		</tr>
		<tr>
			<td style="width: 65%; border-right: 1px solid;" colspan="3"> Date of Delivery: <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->date_delivery), 'php:M d, Y') ?> </td>
			<td colspan="3"> Payment Term: <?= $purchaseOrder->payment_term ?></td>
		</tr>
		<tr>
			<td style="width: 10%; border: 1px solid; text-align: center"><strong>Stock/<br>Property<br> No.</strong></td>
			<td style="width: 7%; border:1px solid; text-align: center "><strong> Unit </strong></td>
			<td style="width: 43%; border: 1px solid; text-align: center "><strong> Item Description </strong></td>
			<td style="width: 10%; border: 1px solid; text-align: center "><strong> Qty </strong></td>
			<td style="width: 12%; border: 1px solid; text-align: center "><strong> Unit Cost </strong></td>
			<td style="width: 13%; border: 1px solid; text-align: center"><strong>Amount </strong></td>
		</tr>



		<?php
		$amount = 0;
		foreach ($biddingLists as $biddingOne) {

			$testDescription = PrItems::find()->where(['id' => $biddingOne->item_id])->one();
			$testDeductions = LessDeductions::find()->where(['po_id' => $biddingOne->po_id])->all();

			$itemPrice = $testDescription->quantity * $biddingOne->supplier_price;
			$amount += $itemPrice;

			echo
			'<tr style = "font-size: small; border: 1px solid; width: 100% ">
					<td style = "width: 10%; padding-top: 2%; text-align: center; font-size: small; border-right: 1px solid;">' . $testDescription->stock . '</td>
					<td style = "width: 7%; text-align: center; font-size: small;  border-right: 1px solid;">' . $testDescription->unit . '</td>
					<td style = "width: 43%; text-align: left; font-size: small; border-right: 1px solid;">' . '<br>' . ($biddingOne->item_remarks == $testDescription->id ? $testDescription->item_name : $biddingOne->item_remarks) . '</td>
					<td style = "width: 10%; text-align: center; font-size: small; border-right: 1px solid;">' . $testDescription->quantity . '</td>
					<td style = "width: 12%; text-align: right; font-size: small;  border-right: 1px solid;">' . number_format($biddingOne->supplier_price, 2) . '</td>
					<td style = "width: 13%; text-align: right; font-size: small;  border-right: 1px solid;">' .  number_format($testDescription['quantity'] * $biddingOne['supplier_price'], 2) . '</td>	
			</tr>';

			foreach ($itemSpecs as $spec) {
				echo
				'<tr>
					<td style = "width: 10%; border-right: 1px solid; border-bottom: 1px solid;  font-size: small">' . '</td>
					<td style = "width: 7%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . '</td>
					<td style = "width: 43%; border-right: 1px solid;  font-size: small; border-bottom: 1px solid; text-align: left;">' . nl2br($spec->description) . '</td>
					<td style = "width: 10%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . $spec->quantity . '</td>
					<td style = "width: 12%; text-align: right; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . '</td>
					<td style = "width: 13%; text-align: right; border-right: 1px solid; border-bottom: 1px solid; font-size: small;">' . '</td> 
			</tr>';
			}
		}
		?>

		<tr style=" border-top: 1px solid; width: 100%">
			<td style="width:10%; border: 1px solid;  font-size: small;"></td>
			<td style="width: 7%; border:1px solid; font-size: small;"></td>
			<td style="width: 43%; border: 1px solid; font-size: small;"></td>
			<td style="width: 10%; border: 1px solid; font-size: small;"></td>
			<td style="width: 12%; border: 1px solid; font-size: small;"></td>
			<td style=" width: 13%;text-align: right; font-size: small; padding-top: 2%"><strong> <?php echo number_format($amount, 2); ?> </strong></td>
		</tr>

		<tr style=" border:1px solid; width: 100%; ">
			<td colspan="3" style="font-size: small; text-align: center; padding-top: 2%">End User: <?= $purchaserequest->enduser ?></td>
			<td style="width: 10%; border: 1px solid; font-size: small;"></td>
			<td style="width: 12%; border: 1px solid; font-size: small;"></td>
			<td style="width: 13%; border: 1px solid; font-size: small;"></td>
		</tr>

		<?php

		$totalAmount = 0;
		$totalVat = 0;
		$totalVatamount = 0;
		$totalEwt = 0;
		$totalEwtAmount = 0;
		$totalDeduct = 0;

		foreach ($itemEwts as $ewt) {

			$totalewt = $ewt->deduction_amount;
			$totalEwt += $totalewt;
		}

		foreach ($itemVats as $itemVat) {
			// var_dump($itemVats);die;
			$totalvat = $itemVat->deduction_amount;
			$totalVat += $totalvat;
		}

		foreach ($orderItems as $orderItem) {

			$total = $orderItem->deduction_amount;
			$totalAmount += $total;
			$totalDeduct = $amount - $total;
		}

		echo
		'<tr style = "font-size: small;  border:1px solid; width: 100%;">
					<td style="width:10%; border: 1px solid;  font-size: small;"></td>
					<td style="width: 7%; border:1px solid; font-size: small;"></td>
					<td colspan = "2"  style = " padding-top: 2%; text-align: right; font-size: small; border-right: 1px solid; padding-right: 2% ">less VAT ' . ':</td>
					<td style = "width: 12%; padding-top: 2%; text-align: right; font-size: small;  border-right: 1px solid;">' . number_format($totalVat, 2) . '</td>;
					<td style="width: 13%; border: 1px solid; font-size: small;"></td>
			</tr>';

		echo
		'<tr style = "font-size: small;  border:1px solid; width: 100%;">
						<td style="width:10%; border: 1px solid;  font-size: small;"></td>
						<td style="width: 7%; border:1px solid; font-size: small;"></td>
						<td colspan = "2"  style = " padding-top: 2%; text-align: right; font-size: small; border-right: 1px solid; padding-right: 2% ">less EWT ' . ':</td>
						<td style = "width: 12%; padding-top: 2%; text-align: right; font-size: small;  border-right: 1px solid;">' . number_format($totalEwt, 2) . '</td>;
						<td style="width: 13%; border: 1px solid; font-size: small;"></td>
				</tr>';
		?>

		<tr style=" border: 1px solid;">
			<td style="width:10%; border: 1px solid;  font-size: small;"></td>
			<td style="width: 7%; border:1px solid; font-size: small;"></td>
			<td colspan="2" style=" text-align: right; border-right: 1px solid; font-size: small; padding-top: 2% ; padding-right: 2% ">Total Less: </td>
			<td colspan="2" style=" text-align: right; font-size: small; padding-top: 2% ">- <?php echo number_format($totalAmount, 2); ?></td>
		</tr>

		<?php
		$totalAdditional = 0;
		$totalAddamount = 0;
		$finaldeductAmount = 0;
		$finalAmount = 0;

		foreach ($addServices as $addService) {

			// var_dump($addService->addservicesLists == NULL);
			// die;
			$total = $addService->additional_amount;
			$totalAdditional += $total;

			$finaldeductAmount = $amount - $totalAmount;
			$finalAmount = $finaldeductAmount + $totalAdditional;


			echo
			'<tr style = "font-size: small;  border:1px solid; width: 100%;">
							<td style="width:10%; border: 1px solid;  font-size: small;"></td>
							<td style="width: 7%; border:1px solid; font-size: small;"></td>
							<td colspan = "2"  style = " padding-top: 2%; text-align: right; font-size: small; border-right: 1px solid; padding-right: 2% ">Add ' . ($addService->addservicesLists == NULL ? '' : $addService->addservicesLists->service_name) . ':</td>
							<td style = "width: 12%; padding-top: 2%; text-align: right; font-size: small;  border-right: 1px solid;">' .  ($addService == NULL ? '-' : number_format($addService->additional_amount, 2)) . '</td>;
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
			<td colspan="6" style=" text-align: left; font-size: small;"><strong>Total Amount in Word: </strong> <?= ucwords(convertNumberToWord($finalAmount)) ?> <?= ($biddingOne->totalCostDecimal != 0 && $biddingOne->totalCostDecimal != null) ? ' pesos and ' . ucwords(convertNumberToWord($biddingOne->totalCostDecimal)) . ' centavos' : '' ?></td>
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
			<td colspan="3" style="padding-top: 5%;text-align: left; text-transform: uppercase;"> <?= ($purchaserequest->approved_by == NULL ? '-' :  $purchaserequest->approvedBy->fname . ' ' . $purchaserequest->approvedBy->lname) ?>
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