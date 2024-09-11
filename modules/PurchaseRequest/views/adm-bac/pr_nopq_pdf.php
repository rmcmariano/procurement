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


<div class="ntp" style="font-size: 12pt;">
	<div style="text-align:center">
		<strong>NOTICE OF POST-QUALIFICATION</strong>
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
		Upon careful examination, validation and verification of the eligibility, technical and financial requirements that you have submitted last <?= Yii::$app->formatter->asDatetime(strtotime(isset($openDate->option_date) ?  $openDate->option_date : ''), 'php:F d, Y') ?> on the bidding for the
		" <?= $description->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $description->unit ?> <?= $description->item_name ?>" please be informed that your company has passed post qualification.
		<br><br>
		We will furnish you the Notice of Award as soon as it is available.
	</div>

	<br><br>
	<div>
		Very truly yours,
	</div>

	<div style="padding-top: 50px">
		<i><strong>DR. JANET F. QUIZON</strong></i><br>
		<div span style=" font-size: 12pt;">BAC-Chairperson </div>
	</div>

	<br><br>
	<div>
		<left>
		Received by the Bidder: <br><br>
		_____________________________________ <br><br>
			<!-- <hr style="width: 40%;"><br><br> -->
			Date: ________________________________ <br><br>
		</left>
	</div>
</div>