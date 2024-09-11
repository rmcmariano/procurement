<?php

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


<div class="invitation-to-bid-pdf" style="font-family:Arial, Helvetica, sans-serif; font-size: 10pt; text-align:justify;">
	<div style="text-align:center; text-transform: uppercase">
		<strong>CHECKLIST OF ELIGIBILITY AND TECHNICAL REQUIREMENTS FOR PROCUREMENT <?= $items->bid_title ?> </strong>
	</div>
	<div style="text-align:justify; width: 100%; padding-top: 1%; ">
		<table>
			<tr>
				<td style="width: 20%;"><strong>Title of Project</strong></td>
				<td><strong> : </strong></td>
				<td style="padding-top: 1%;">________________________________________________________________________</td>
			</tr>
			<tr>
				<td><strong>Name of Bidder</strong></td>
				<td><strong> : </strong></td>
				<td style="padding-top: 2%;">________________________________________________________________________</td>
			</tr>
			<tr>
				<td><strong>Date</strong></td>
				<td><strong> : </strong></td>
				<td style="padding-top: 2%;">________________________________________________________________________</td>
			</tr>
		</table>
	</div>

	<div style="text-align:justify; font-size: 10pt; font-family: Arial Narrow, Arial, sans-serif; padding-top:1%">
		<span style="font-size: 12pt"><strong>Envelope I</strong><br></span>
		THE BIDDER SHALL SUBMIT THE FOLLOWING DOCUMENTS <u>ARRANGED IN ORDER AND WITH LABEL</u>, USING THE PRESCRIBED BIDDING FORMS:
	</div>
	<br>

	<div style="text-align:justify;">
		<strong>I. &nbsp;&nbsp; <u>LEGAL DOCUMENTS</u></strong>
	</div>

	<div style="font-size: 10pt; padding-left:5%">
		<ol>
			<li>PhilGEPS Certificate – Platinum Membership </li>
		</ol>
	</div>

	<div style="text-align:justify; font-size: 10pt">
		<strong>NOTE: </strong> If Joint Venture, Valid and duly notarized Joint Venture Agreement (JVA) in case joint venture is already in existence or duly notarized statements from all potential joint venture partners stating that they will enter into and abide by the provisions of JVA in the instance that the bid is successful.
	</div>
	<br>

	<div style="text-align:justify;">
		<strong>II. &nbsp;&nbsp; <u>TECHNICAL DOCUMENTS</u></strong>
	</div>

	<div style="font-size: 10pt; padding-left:5%">
		<ol>
			<li> Statement of all on going government and private contracts within the period of two (2) years from submission of bids, including contracts awarded but not yet started if any, whether similar or not similar in nature and complexity to the contract to be bid. This statement shall be supported with any of the ff:</li>
			<br>
			<ul style="list-style-type: disc">
				<li>Notice of Award and/or Contract, or, Notice to proceed issued by the owner or the agency;</li>
			</ul><br>
			<li> Statement of all completed government and private contracts which are similar in nature within the period of five (5) years. Only the bidder’s single largest completed contract that is similar in nature to this project shall be supported with any of the following:</li>
			<br>
			<ul style="list-style-type: disc">
				<li>Certificate of Completion or Sales Invoice or official receipt;</li>
			</ul><br>
			The ITDI BAC, however, reserves the right to examine other documents pertaining to the bidder’s statement during the post evaluation stage
		</ol>
	</div>

	<div style="text-align:justify;">
		<strong>III. &nbsp;&nbsp; <u>FINANCIAL DOCUMENTS</u></strong>
	</div>

	<div style="font-size: 10pt; padding-left:5%">
		<ol>
			<li> Audited Financial Statements, showing, among others, the prospective bidder’s total and current assets and liabilities stamped “received” by the BIR or its duly accredited and authorized institution for the calendar year 2019 and 2020 or 2020 and 2021;</li>
			<li> Computation of Net Financial Contracting Capacity (NFCC) </li>
		</ol>
	</div>

	<div style="text-align:justify; padding-top: 20%; font-size: 8pt">
		<table style="font-size: 8pt; width: 100%;">
			<tr>
				<td style="width: 20%;"><strong>REMARKS:</strong></td>
				<td style="width: 50%;"><strong>( ) Eligible ( ) Ineligible</strong></td>
				<td style="width: 30%;"><strong>Checked by: _____________________</strong></td>
			</tr>
		</table>
	</div>
</div>