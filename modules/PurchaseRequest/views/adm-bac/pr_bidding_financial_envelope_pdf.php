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


<div class="invitation-to-bid-pdf" style="font-family:'Times New Roman', Times, serif; font-size: 12pt">
	<div style="text-align:center; text-transform: uppercase">
		<strong>CHECKLIST OF FINANCIAL REQUIREMENTS FOR PROCUREMENT OF <?= $items->bid_title ?> </strong>
	</div>

	<br>
	<div style="text-align:justify; width: 100% ">
		<table>
			<tr>
				<td style="width: 20%;"><strong>Title of Project</strong></td>
				<td><strong> : </strong></td>
				<td style="padding-top: 3%;">________________________________________________________________________</td>
			</tr>
			<tr>
				<td><strong>Name of Bidder</strong></td>
				<td><strong> : </strong></td>
				<td style="padding-top: 3%;">________________________________________________________________________</td>
			</tr>
			<tr>
				<td><strong>Date</strong></td>
				<td><strong> : </strong></td>
				<td style="padding-top: 3%;">________________________________________________________________________</td>
			</tr>
		</table>
	</div>
	<br>

	<div style="text-align:justify; font-size: 10pt">
		PLEASE SUBMIT THE FOLLOWING DOCUMENTS ARRANGED IN ORDER AND WITH LABEL USING THE PRESCRIBED BIDDING FORMS:
	</div>
	<br>

	<div style="text-align:justify">
		<strong>Envelope II:</strong><br><br>
		<span style="font-size: 10pt;"> The <strong><u>FINANCIAL COMPONENT</u></strong> shall contain the following information/documents and shall be opened only if the bidder has complied with the requirements in the Legal and Technical Envelope (Envelope I)</span>
	</div>

	<div style="font-size: 10pt">
		<ol>
			<li style="padding: 1% "> Duly signed bid form </li>
			<li style="padding: 1%"> Duly Signed Bid Prices in the Bill of Quantities for Procurement of Goods, or Scope of Work for Procurement of Services (Quotation)</li>
			<li style="padding: 1%"> Detailed Breakdown of the Contract Cost (Main Equipment, Accessories, Training, Installation Cost, etc.)</li>
			<li style="padding: 1%"> Recurring and maintenance cost, if applicable</li>
			<li style="padding: 1%"> Quotation</li>
		</ol>
	</div>

	<div style="text-align:justify; font-size: 11pt">
		<strong>NOTE:</strong>
	</div>

	<div style="text-align:justify; font-size: 8pt; font-family: Arial, Helvetica, sans-serif">
		<ol>
			<li> All pages of Eligibility requirements whether original or cert. true copy, including the duplicate copies and all pages of the bidders Technical Proposal whether original or cert. true copy, including the brochures (if any) and the duplicate copies shall likewise be countersigned by the prospective bidder or his/her duly authorized representative. The countersigned must appear at the lower portion of each of the pages in the bidding documents using any color of pen except BLACK. </li>
			<li> The notarization of document, if requires, shall comply with the 2004 Rules on Notarial Practice.</li>
			<li> Any missing or unsigned document or Non-compliance in the above-mentioned checklist and instructions shall be a ground for outright disqualification.</li>
		</ol>
	</div>

	<div style="text-align:justify; padding-top: 30%; font-size: 8pt">
		<table style="font-size: 8pt; width: 100%;">
			<tr>
				<td style="width: 20%;"><strong>REMARKS:</strong></td>
				<td style="width: 50%;"><strong>( ) Eligible ( ) Ineligible</strong></td>
				<td style="width: 30%;"><strong>Checked by: _____________________</strong></td>
			</tr>
		</table>
	</div>
</div>