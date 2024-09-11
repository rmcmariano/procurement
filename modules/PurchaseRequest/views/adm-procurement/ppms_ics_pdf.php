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
		<h3><strong>INVENTORY CUSTODIAN SLIP</strong></h3>
	</div>
	<left><strong>Entity Name: </strong>INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE</left>
	<br>
	<left>Fund Cluster: <i><? $purchaserequest->charge_to ?></i></left>
	<right>ICS No.: <i><? $icsModel->ics_no ?></i></right>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 100%; text-align:center; font-size: small ">
		<tr>
			<td style="width: 5%; border: 1px solid;  font-size: small;"><strong>QTY</strong></td>
			<td style="width: 5%; border:1px solid; font-size: small;"><strong> Unit </strong></td>
			<td style="width: 50%; border: 1px solid; font-size: small;"><strong> Item Description </strong></td>
			<td style="width: 17.5%; border: 1px solid; font-size: small;"><strong> Qty </strong></td>
		</tr>

		<?php
		$amount = 0;
		$itemNo = 1;
		foreach ($biddingLists as $biddingOne) {

			$testDescription = PrItems::find()->where(['id' => $biddingOne->item_id])->one();
			$testDeductions = LessDeductions::find()->where(['po_id' => $biddingOne->po_id])->all();

			$itemPrice = $testDescription->quantity * $biddingOne->supplier_price;
			$amount += $itemPrice;

			echo
			'<tr style = "font-size: small;  width: 100% ">
					<td style = "width: 15%; text-align: center; font-size: small; border-right: 1px solid; border-top: 1px solid">' .  $itemNo++ . '</td>
					<td style = "width: 50%; text-align: left; font-size: small; border-right: 1px solid; border-top: 1px solid">'  . ($biddingOne->item_remarks == $testDescription->id ? $testDescription->item_name : $biddingOne->item_remarks) . '</td>
					<td style = "width: 17.5%; text-align: center; font-size: small;  border-right: 1px solid; border-top: 1px solid">' . $testDescription->unit . '</td>
					<td style = "width: 17.5%; text-align: center; font-size: small; border-right: 1px solid; border-top: 1px solid">' . $testDescription->quantity . '</td>
			</tr>';

			foreach ($itemSpecs as $spec) {

				echo
				'<tr>
					<td style = "width: 15%; border-right: 1px solid;  font-size: small">' . '</td>
					<td style = "width: 50%; border-right: 1px solid;  font-size: small; text-align: left;">' . nl2br($spec->description) . '</td>
					<td style = "width: 17.5%; text-align: center; border-right: 1px solid; font-size: small;">' . '</td>
					<td style = "width: 17.5%; text-align: center; border-right: 1px solid;  font-size: small;">' . '</td>
			</tr>';
			}
		}
		?>
		<br><br>
		<tr>
			<td style="width: 15%; border-right: 1px solid;  font-size: small"></td>
			<td style="width: 50%; border-right: 1px solid;  font-size: small; text-align: left;"> <?= $purchaserequest->warranty ?> </td>
			<td style="width: 17.5%; text-align: center; border-right: 1px solid; font-size: small;"></td>
			<td style="width: 17.5%; text-align: center; border-right: 1px solid;  font-size: small;"></td>
		</tr>

	</table>


	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 50%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;"><strong><i>INSPECTION</i></strong></td>
			<td style="width: 50%; text-align: center; border-right: 1px solid; border-bottom: 1px solid; font-size: small;"><strong><i>ACCEPTANCE</i></strong></td>
		</tr>
		<tr>
			<td style="width: 50%; text-align: left; border-right: 1px solid; font-size: small;"><strong>Date Inspected: </strong> &nbsp;&nbsp;&nbsp; <?= Yii::$app->formatter->asDatetime(strtotime($iarModel->inspection_date), 'php:M d, Y') ?></td>
			<td style="width: 50%; text-align: left; border-right: 1px solid; font-size: small;"><strong>Date Received: </strong></td>
		</tr>
		<tr>
			<td style="width: 50%; text-align: left; border-right: 1px solid; font-size: small; padding-left: 1%">
				<br>
				<span class="box">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</span> &nbsp;&nbsp;&nbsp; Inspected, verified and found in order as to quantity and specifications.
			</td>
			<td style="width: 50%; text-align: left; border-right: 1px solid; font-size: small; padding-left: 1%">
				<br>
				<span class="box">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</span> &nbsp;&nbsp;&nbsp; Complete
			</td>
		</tr>
		<tr>
			<td style="width: 50%; text-align: left; border-right: 1px solid; font-size: small; padding-left: 1%">
			</td>
			<td style="width: 50%; text-align: left; border-right: 1px solid; font-size: small; padding-left: 1%">
				<span class="box">
					&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				</span> &nbsp;&nbsp;&nbsp; Partial (pls. specify quantity)
			</td>
		</tr>
		<br><br>
		<tr>
			<td style="width: 50%; text-align: center; border-right: 1px solid; font-size: small;"><strong><u> <?= $inspectorId->profile->fname . ' ' .  $inspectorId->profile->lname  ?></u></strong></td>
			<td style="width: 50%; text-align: center; border-right: 1px solid; font-size: small;"><strong><u>ROCHEEL LEE C. DELUTA</u></strong></td>
		</tr>
		<tr>
			<td style="width: 50%; text-align: center; border-right: 1px solid; font-size: small;">Inspection Officer/Inspection Committee</td>
			<td style="width: 50%; text-align: center; border-right: 1px solid; font-size: small;">Supply and/or Property Custodian</td>
		</tr>
	</table>

</div>