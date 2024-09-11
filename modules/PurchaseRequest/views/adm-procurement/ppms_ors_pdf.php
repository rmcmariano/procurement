<?php

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\LessDeductions;
use app\modules\PurchaseRequest\models\PrItems;

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
	<div style="text-align:center; font-size: small">
		<i>Republic of the Philippines</i><br>
		Department of Science and Technology<br>
		<strong>INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE</strong><br>
		DOST Cpd., General Santos Ave., Bicutan, Taguig City<br>
		Tel. Nos.: 837-2071 to 82 (DOST Trunklines) Telefax Nos.: 837-2071 local 2220<br>
	</div>
	<br>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 65%; border-right: 1px solid; font-size: small; text-align: center"> <strong>OBLIGATION REQUEST AND STATUS</strong></td>
			<td style="width: 35%; font-size: small;"> Serial No:</td>
		</tr>
		<tr>
			<td style="width: 65%;border-right: 1px solid;  font-size: small; text-align: center">INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE</td>
			<td style="width: 35%;  font-size: small"> Date: <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->date_ors_burs), 'php:M d, Y') ?> </td>
		</tr>
		<tr>
			<td style="width: 65%; border-right: 1px solid;  font-size: small;  text-align: center"> Entity Name</td>
			<td style="width: 35%;  font-size: small"> Fund Cluster:</td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 20%; border-right: 1px solid; font-size: small; padding-top: 3% "> Payee</td>
			<td style="width: 80%; border-right: 1px solid; font-size: small; padding-top: 3%  "><strong> <?= $bidding->supplierdisplay->supplier_name ?> </strong></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 20%; border-right: 1px solid; font-size: small; padding-top: 1% "> Office</td>
			<td style="width: 80%; border-right: 1px solid; font-size: small; padding-top: 1%  "></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 20%; border-right: 1px solid; font-size: small; padding-top: 1% "> Address</td>
			<td style="width: 80%; border-right: 1px solid; font-size: small; padding-top: 1.5%  "> <?= $bidding->supplierdisplay->supplier_address ?></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 100%; text-align:center; font-size: small ">
		<tr>
			<td style="width:20%; border: 1px solid;  font-size: small;padding-top: 1%">Responsibility Center</td>
			<td style="width: 35%; border: 1px solid; font-size: small;padding-top: 1%"> Particulars</td>
			<td style="width: 10%; border: 1px solid; font-size: small;padding-top: 1%">MFO/PAP </td>
			<td style="width: 17.5%; border: 1px solid; font-size: small;padding-top: 1%">UACS Object<br>Code</td>
			<td style="width: 17.5%; border: 1px solid; font-size: small;padding-top: 1%">Amount </td>
		</tr>

		<?php
		$amount = 0;

		foreach ($biddingLists as $ordItem) {

			$testDescription = PrItems::find()->where(['id' => $ordItem->item_id])->one();
			$testDeductions = LessDeductions::find()->where(['po_id' => $ordItem->po_id])->all();

			$itemPrice = $testDescription->quantity * $ordItem->supplier_price;
			$amount += $itemPrice;
		}


		// Build the dynamic PO/WO No. part
		$poNo = '';

		if ($purchaseOrder->project_type_series_id == 0 || $purchaseOrder->project_type_series_id === NULL) {
			$poNo = $purchaseOrder->po_no;
		} else if ($purchaseOrder->project_type_series_id == 1) {
			if ($purchaseOrder->item_type_series_id == 1) {
				$poNo = 'GIA' . 'E-' . $purchaseOrder->po_no;
			} else if ($purchaseOrder->item_type_series_id == 2) {
				$poNo = 'GIA' . 'S-' . $purchaseOrder->po_no;
			} else if ($purchaseOrder->item_type_series_id == 3) {
				$poNo = 'GIA' . 'C-' . $purchaseOrder->po_no;
			} else if ($purchaseOrder->item_type_series_id == NULL) {
				$poNo = 'GIA-' . $purchaseOrder->po_no;
			}
		} else if ($purchaseOrder->project_type_series_id == 2) {
			if ($purchaseOrder->item_type_series_id == 1) {
				$poNo = 'GAA' . 'E-' . $purchaseOrder->po_no;
			} else if ($purchaseOrder->item_type_series_id == 2) {
				$poNo = 'GAA' . 'S-' . $purchaseOrder->po_no;
			} else if ($purchaseOrder->item_type_series_id == 3) {
				$poNo = 'GAA' . 'C-' . $purchaseOrder->po_no;
			} else if ($purchaseOrder->item_type_series_id == NULL) {
				$poNo = 'GAA-' . $purchaseOrder->po_no;
			}
		}

		echo
		'<tr>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 5%"></td>
			<td style="width: 35%; border-right: 1px solid;  font-size: small;padding-top: 1%; padding-left: 1%; text-align:left">PO/WO No.: &nbsp;&nbsp; <strong>' .
					$poNo . '</strong></td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%;  text-align:right">' . number_format($amount, 2) . '</td>
		</tr>';
		?>

		<tr>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 35%; border-right: 1px solid;  font-size: small;padding-top: 1%; text-align:left; padding-left: 1%; "> DATE: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->po_date_created), 'php:M d, Y') ?> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
		</tr>

		<?php
		$amount = 0;

		foreach ($biddingLists as $ordItem) {

			$testDescription = PrItems::find()->where(['id' => $ordItem->item_id])->one();


			echo
			'<tr>
				<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
				<td style="width: 35%; border-right: 1px solid;  font-size: small;padding-top: 1%; padding-left: 1%; text-align:left"> ITEM:  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . ($ordItem->item_remarks == $testDescription->id ? $testDescription->item_name : $ordItem->item_remarks) . '</td>
				<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
				<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
				<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
		</tr>';
		}
		?>

		<tr>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 35%; border-right: 1px solid;  font-size: small;padding-top: 5%;  text-align:left"> End-user: &nbsp;&nbsp;&nbsp;&nbsp; <?= $purchaserequest->enduser ?> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
		</tr>

		<tr>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 35%; border-right: 1px solid;  font-size: small;padding-top: 3%; padding-bottom: 2%; text-align:left"> CHARGE TO: &nbsp;&nbsp;&nbsp; <?= $purchaserequest->chargedisplay->project_title ?> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
		</tr>

		<?php
		$amount = 0;
		foreach ($biddingLists as $ordItem) {

			$testDescription = PrItems::find()->where(['id' => $ordItem->item_id])->one();
			$testDeductions = LessDeductions::find()->where(['po_id' => $ordItem->po_id])->all();

			$itemPrice = $testDescription->quantity * $ordItem->supplier_price;
			$amount += $itemPrice;

			// $itemPrice = $testDescription->quantity * $testBidding->supplier_price;
			// $amount += $itemPrice;
		}
		echo
		'<tr>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 35%; border-right: 1px solid;  font-size: small;padding-top: 1%; padding-left: 1%; text-align:left"></td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 17.5%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
			<td style="width: 17.5%; border-right: 1px solid; border-top: 1px solid;  font-size: small;padding-top: 1%; text-align:right"><strong>' .  number_format($amount, 2) . '</strong></td>
		</tr>';
		?>

		<tr>
			<td colspan="2" style="width: 10%; border-top: 1px solid;border-right: 1px solid;  font-size: small;padding-top: 1%; text-align:left">A. &nbsp;&nbsp; <strong>Certified:</strong> Charges to appropriation/allotment are necessary, lawful and under my direct supervision; and supporting documents valid, proper and legal.</td>
			<td colspan="3" style="width: 10%;border-top: 1px solid;border-right: 1px solid; font-size: small;padding-top: 1%; text-align:left">B. &nbsp;&nbsp; <strong>Certified:</strong>Alloment available and obligated for the purpose/adjustment necessary as indicated above.</td>
		</tr>
		<tr>
			<td colspan="2" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; text-align:left">Signature: </td>
			<td colspan="3" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; text-align:left">Signature:</td>
		</tr>
		<tr>
			<td colspan="2" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; text-align:left">Printed Name: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?= ($purchaserequest->approved_by == NULL ? '-' :  $purchaserequest->approvedBy->fname . ' ' . $purchaserequest->approvedBy->lname) ?></td>
			<td colspan="3" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; text-align:left">Printed Name: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ERIC M. CHARLON</td>
		</tr>
		<tr>
			<td colspan="2" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; text-align:left">Position: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; OIC-NMD <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Head Requesting Office/Authorized Representative</td>
			<td colspan="3" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; text-align:left">Position: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Head Budget Section/FMD</td>
		</tr>
		<tr>
			<td colspan="2" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; padding-bottom: 2%; text-align:left">Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
			<td colspan="3" style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 2%; padding-bottom: 2%; text-align:left">Date: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; </td>
		</tr>
	</table>
	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 100%; text-align:center; font-size: small ">
		<tr>
			<td style="width:5%; border: 1px solid;  font-size: small;padding-top: 3%"></td>
			<td colspan="7" style="border-right: 1px solid; border-top: 1px solid; font-size: small; text-align:left;"></td>
		</tr>
		<tr>
			<td style="width:5%; border: 1px solid;  font-size: small;padding-top: 1%">C.</td>
			<td colspan="7" style="border-right: 1px solid; border-top: 1px solid; font-size: small; text-align:center;"><strong> STATUS OF UTILIZATION </strong> </td>
		</tr>
		<tr>
			<td colspan="3" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> Reference</strong> </td>
			<td colspan="5" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> Amount </strong> </td>
		</tr>
		<tr>
			<td style="width:5%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 22%; border-right: 1px solid; font-size: small;padding: 1%"></td>
			<td style="width: 8%; border-right: 1px solid; font-size: small;padding-top: 1%"> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 20%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td colspan="2" style="width: 10%; border: 1px solid; font-size: small;padding-top: 1%">Balance </td>
		</tr>
		<tr>
			<td style="width:5%; border-right: 1px solid;  font-size: small;padding-top: 1%">Date</td>
			<td style="width:20%;  border-right: 1px solid;  font-size: small;padding-top: 1%">Particulars</td>
			<td style="width: 22%;  border-right: 1px solid; font-size: small;padding: 1%"> BURS/JEV/RCI/RADAI/RTRAI No.</td>
			<td style="width: 8%;  border-right: 1px solid; font-size: small;padding-top: 1%"> Utilization</td>
			<td style="width: 10%;  border-right: 1px solid; font-size: small;padding-top: 1%">Payable</td>
			<td style="width: 20%;  border-right: 1px solid; font-size: small;padding-top: 1%">Payment</td>
			<td style="width: 10%;  border-right: 1px solid; font-size: small;padding-top: 1%">Not Yet <br> Due </td>
			<td style="width: 10%;  border-right: 1px solid; font-size: small;padding-top: 1%">Due and <br> Demandable </td>
		</tr>
		<tr>
			<td style="width:5%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width:20%; border-right: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 22%; border-right: 1px solid; font-size: small;padding: 1%"></td>
			<td style="width: 8%; border-right: 1px solid; font-size: small;padding-top: 1%">(a) </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"> (b)</td>
			<td style="width: 20%; border-right: 1px solid; font-size: small;padding-top: 1%"> (c)</td>
			<td style="width: 10%;  border-right: 1px solid; font-size: small;padding-top: 1%">(a-b) </td>
			<td style="width: 10%;  border-right: 1px solid; font-size: small;padding-top: 1%"> (b-c) </td>
		</tr>
		<tr>
			<td style="width:5%; border: 1px solid;  font-size: small;padding-top: 15%"></td>
			<td style="width:20%; border: 1px solid;  font-size: small;padding-top: 1%"></td>
			<td style="width: 22%; border: 1px solid; font-size: small;padding: 1%"></td>
			<td style="width: 8%; border: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 10%; border: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 20%; border: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 10%;  border: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 10%;  border: 1px solid; font-size: small;padding-top: 1%"></td>
		</tr>



	</table>






</div>