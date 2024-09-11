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


<div class="page2-pdf" style="font-family:'Times New Roman', Times, serif; font-size: 10pt; text-align:justify;">
	<div style="text-align:center; text-transform: uppercase">
		<strong>CHECKLIST OF ELIGIBILITY AND TECHNICAL REQUIREMENTS FOR PROCUREMENT <?= $items->bid_title ?> </strong>
	</div>

	<div style="font-size: 10pt;">
		<ol>
			<li></li>
			<li></li>
			<li></li>
			<li> THE TECHNICAL COMPONENT SHALL CONTAIN THE FOLLOWING:
				<ol>
					<strong>4.1. Bid Securing Declaration, or any form of Bid Security,</strong> in an amount not less than the required percentage of Approved Budget of the Contract and in the prescribed form and validity period. <br><br>
					<ul style="list-style-type: disc">
						<li> 2% - Cash or cashier’s/manager’s check issued by a Universal or Commercial Bank</li>
						<li> 2% - Bank Draft/Guarantee or Irrevocable Letter of Credit issued by a Universal or Commercial Bank: Provided, however, that it shall be confirmed or authenticated by a Universal or Commercial Bank, if issued by a foreign bank.</li>
						<li> 5% - Surety bond callable upon demand issued by a surety or insurance company duly certified by the Insurance Commission</li>
						<li> Any combination of the foregoing</li>
					</ul>
			</li><br>

			<strong>4.2 Technical specifications, should include the ff:</strong>
			<ul style="list-style-type: disc">
				<li>production/delivery schedule</li>
				<li>manpower requirements</li>
				<li>warranty certificate</li>
				<li>brochures</li>
				<li>certificate of training of service engineers</li>
				<li>authorization from manufacturer</li>
				<li>country of origin</li>
				<li>Certificate of availability of parts within five (5) years</li>
				<li>Certificate of Exclusive Distributorship, if applicable</li>
				<li>after sales service/parts</li>
			</ul><br>

			<strong>4.3 Omnibus Sworn Statement by the prospective bidder or its duly authorized representative in the form prescribed by the GPPB as to the following:</strong>
			<ol type="a">
				<li> It is not “blacklisted” or barred from bidding by the GOP or any of its agencies, offices, corporations or LGUs, including foreign government/foreign or international financing institution whose blacklisting rules have been recognized by the GPPB.</li>
				<li> Each of the documents submitted in satisfaction of the bidding requirements is an authentic copy of the original, complete, and all statements and information provided therein are true and correct;</li>
				<li> It is authorizing the Head of the Procuring Entity or his duly authorized representative/s to verify all documents submitted;</li>
				<li> The signatory or his duly authorized representative of the prospective bidder, and granted full power and authority to do, execute and perform any and all acts necessary and/or to represent the prospective bidder in the bidding, with the duly notarized secretary’s certificate attesting to such fact, if the prospective bidder is a corporation, partnership or joint venture;</li>
				<li> A sworn affidavit of the bidder that it is not related to the Head of the Procuring, members of the BAC, the TWG and the BAC secretariat, the Head of the PMO or the end-user unit, and the project consultants by consanguinity or affinity up to the third civil degree;</li>
				<li> It complies with the responsibilities of a prospective or eligible bidder provided in the PBDs; and;</li>
				<li> It complies with all existing labor laws and standards.</li>
				<li> That the bidder did not give or pay directly or indirectly, any commission, amount, fee, or any form of consideration, pecuniary or otherwise, to any person or official, personnel or representative of the government in relation to any procurement project or activity.</li>
			</ol>
		</ol>
		</ol>
	</div>

	<div style="text-align:justify; font-size: 8pt; font-family:'Times New Roman', Times, serif">
		<strong>NOTE:</strong>
		<ol>
			<li> All pages of Eligibility requirements whether original or cert. true copy, including the duplicate copies and all pages of the bidders Technical Proposal whether original or cert. true copy, including the brochures (if any) and the duplicate copies shall likewise be countersigned by the prospective bidder or his/her duly authorized representative. The countersigned must appear at the lower portion of each of the pages in the bidding documents using any color of pen except BLACK. </li>
			<li> The notarization of document, if requires, shall comply with the 2004 Rules on Notarial Practice.</li>
			<li> Any missing or unsigned document or Non-compliance in the above-mentioned checklist and instructions shall be a ground for outright disqualification.</li>
		</ol>
	</div>

	<div style="text-align:justify; padding-top: 7%; font-size: 8pt">
		<table style="font-size: 8pt; width: 100%;">
			<tr>
				<td style="width: 20%;"><strong>REMARKS:</strong></td>
				<td style="width: 50%;"><strong>( ) Eligible ( ) Ineligible</strong></td>
				<td style="width: 30%;"><strong>Checked by: _____________________</strong></td>
			</tr>
		</table>
	</div>
</div>