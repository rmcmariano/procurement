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
		<strong>NOTICE TO PROCEED</strong>
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
		Notice is hereby given to <?= $supplier->supplier_name ?> that work may commence on <?= $description->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $description->unit ?> <?= $description->item_name ?> with a Contract Price of <?= NumberToWords::transformNumber('en', $bidding->supplier_price) ?> <?= ($bidding->totalCostDecimal != 0 && $bidding->totalCostDecimal != null) ? ' pesos and ' . NumberToWords::transformNumber('en', $bidding->totalCostDecimal) . ' centavos' : '' ?> (P <?= number_format($bidding->supplier_price, '2') ?>)</strong> only.
		<br><br>
		Upon receipt of this notice, you are responsible for performing the services under the terms and conditions of the Agreement and in accordance with the Implementation Schedule.
		<br><br>
		Please acknowledge receipt and acceptance of this notice by signing both copies in the space provided below. Keep one copy and return the other to the Industrial Technology Development Institute.

	</div>

	<br><br>
	<div>
		Very truly yours,
	</div>

	<div style="padding-top: 50px">
		<i><strong>ANNABELLE V. BRIONES, PhD</strong></i><br>
		<div span style=" font-size: medium; padding-left: 70px">Director </div>
	</div>

	<br><br>
	<div>
		<left>
			I acknowledge receipt of this Notice on _____________________________________ <br><br>
			<!-- <hr style="width: 40%;"><br><br> -->
			Name of the Representative of the Bidder: ___________________________________ <br><br>
			<!-- <hr style="width: 40%; margin-left: 150%"> <br><br> -->
			Authorized Signature: ___________________________________________________ <br><br>
			<!-- <hr style="width: 60%"> <br><br> -->

		</left>
	</div>
</div>