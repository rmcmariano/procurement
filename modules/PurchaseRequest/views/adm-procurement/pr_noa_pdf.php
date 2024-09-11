<?php


/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */

use NumberToWords\NumberToWords;

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


<div class="ntp">
	<div style="text-align:center">
		<strong>NOTICE TO AWARD</strong>
	</div>
	<br>

	<div style="text-align:left">
		<div style="text-transform: uppercase"><strong>MR./ MS. <?= $supplier->owner_name ?></strong></div>
		Authorized Representative
		<div><strong><?= $supplier->supplier_name ?> </strong></div>
		<div style="padding-right: 900px;"><?= $supplier->supplier_address ?></div>
		<br>
		<br>
		<div>Dear MR./ MS. <?= $supplier->owner_name ?>:</div>
	</div>
	<br>

	<div style="text-align:justify">
		Please be informed that your bid proposal for the <?= $description->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $description->unit ?> <?= $description->item_name ?>, with a total bid amount of <?= NumberToWords::transformNumber('en', $bidding->supplier_price) ?> <?= ($bidding->totalCostDecimal != 0 && $bidding->totalCostDecimal != null) ? ' pesos and ' . NumberToWords::transformNumber('en', $bidding->totalCostDecimal) . ' centavos' : '' ?> (P <?= number_format($bidding->supplier_price, '2') ?>)</strong> is hereby accepted.
		<br><br>
		In this regard, you are hereby required to accept the award and to submit within ten (10) calendar days the performance security taken from the categories below the amount of which shall not be less than the required percentage of the total contract price in accordance with the following schedule:
	</div>
	
	<br>
	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; font-size: small; text-align:justify; ">
		<tr>
			<td style="width:60%; border: 1px solid; font-size: small; text-align:center; ">Forms of Performance Security</td>
			<td style="width: 40%; border:1px solid; font-size: small; text-align:center; ">Amount of Performance Security <br> (Not less than the required Percentage of the Total Contract Price)</td>
		</tr>
		<tr>
			<td style="width:60%; border: 1px solid; font-size: small; text-align:justify; padding-left: 3%">a) Cash of Cashier's/manager's check issued by a Universal or Commercial Bank.</td>
		</tr>
		<tr>
			<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding-left: 3%">b) Bank draft /guarantee or irrevocable letter of credit issued by a Universal or commercial Bank: Provided, however, that it shall be confirmed or authenticated by a Universal or Commercial Bank, if issued by a foreign bank.</td>
			<td rowspan="1" style="width:15%;  font-size: small; text-align:left;">Goods - Five Percent (5%)</td>
		</tr>
		<tr>
			<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding-left: 3%">c) Surety bond callable upon demand issued by a surety or insurance company duly certified by the Insurance Commission as authorized to issue such security. </td>
			<td style="width:15%; border: 1px solid; font-size: small; text-align:left;">Thirty Percent (30%)</td>
		</tr>
	</table>

	<div class="page-break">
		<div>
			Failure to provide the performance security shall constitute sufficient ground for cancellation of the award and forfeiture of the bid security.
			<br><br><br>
			Very truly yours,
		</div>

		<div style="padding-top: 50px">
			<i><strong>ANNABELLE V. BRIONES, PhD</strong></i><br>
			<div span style=" font-size: medium; padding-left: 70px">Director </div>
		</div>

		<div style="padding-top: 80px">
			Conforme:
			<br><br><br>
			<div style="text-transform: uppercase;"><strong><?= $supplier->owner_name ?></strong> </div>
			<div>Date: </div>
		</div>
	</div>



</div>