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
		<strong>NOTICE OF LOWEST CALCULATED BID</strong>
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
		Please be informed that your bid proposal for the "<?= $description->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $description->unit ?> <?= $description->item_name ?>" have been considered by the Bids and Awards Committee as the lowest calculated bid. In this connection, your company is hereby required to submit the certified true copies of the following documents within three (3) calendar days from the receipt of this letter.
		<br>
		<ul style="padding-left: 10%;">
            <li>Latest Income and Business Tax Return</li>
            <li>SEC/DTI Certificate of Registration</li>
            <li>Valid mayor’s permit</li>
			<li>Tax Clearance</li>
        </ul>
		
		Failure to submit the above requirements on time shall be a ground for the forfeiture of the bid security and disqualification for award.
		<br><br>
		To facilitate the authentication of the foregoing documents, please have your representative present the original copies thereof to the ITDI-BAC Secretariat, Second Floor Metrology Building, ITDI, DOST Comp. Gen. Santos Ave., Bicutan, Taguig City. Likewise, present the original copies of the following eligibility documents that your company submitted last <?= Yii::$app->formatter->asDatetime(strtotime(isset($openDate->option_date) ?  $openDate->option_date : ''), 'php:F d, Y') ?> during the opening of bids.
		
		<ul style="padding-left: 10%;">
            <li>Audited financial statement stamped “Received” by BIR for CY 2019-2020 or 2020-2021</li>
            <li>Original Government I.D of your company’s authorized representative</li>
        </ul>

		Thank you.
	</div>

	<br><br>
	<div>
		Very truly yours,
	</div>

	<div style="padding-top: 50px">
		<i><strong>DR. JANET F. QUIZON</strong></i><br>
		<div span style=" font-size: 12pt;">BAC-Chairperson </div>
	</div>
</div>