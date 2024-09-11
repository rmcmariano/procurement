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


<div class="infra-invitation-to-bid-pdf" style="font-family:Arial, Helvetica, sans-serif; font-size: 12pt">
	<div style="text-align:center; text-transform: uppercase">
		<strong>INVITATION TO BID FOR <br><br> <?= $items->item_name ?> </strong>
	</div>

	<br>
	<div style="text-align:justify; ">
		1. The <strong><i> INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE,</i></strong>, through "<?= $purchaserequest->chargedisplay->project_title ?>" intends to apply the amount of <strong> <?= NumberToWords::transformNumber('en', $items->total_cost) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? 'pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->total_cost, 2) ?>) </strong> being the Approved Budget for the Contract (ABC) to payments under the contract for the <strong> “<?= $items->item_name ?> ” </strong>.<br><br>
		<i>Bids received in excess of the ABC shall be automatically rejected at bid opening.</i>
	</div>
	<br>

	<div style="text-align:justify">
		2. The <strong><i> Industrial Technology Development Institute (ITDI)</i> </strong> now invites bids for the <strong> “<?= $items->item_name ?> ” </strong>. Location is at ITDI-DOST Compound Gen. Santos Bicutan, Taguig. Completion of work is required in <strong><?= $purchaserequest->delivery_period ?></strong> upon issuance of Notice to Proceed.
		<br><br>

		3. Bidders should have completed, within five (5) years from the date of submission and receipt of bids, a contract similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II. Instructions to Bidders. <br><br>

		4. Bidding will be conducted through open competitive bidding procedures using non-discretionary pass/fail criterion as specified in the Implementing Rules and Regulations (IRR) of Republic Act 9184 (RA 9184), otherwise known as the “Government Procurement Reform Act”. <br><br>

		5. Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least seventy five percent (75%) interest or outstanding capital stock belonging to citizens of the Philippines. <br><br>

		7. A complete set of Bidding Documents may be acquired by interested Bidders from April 27, 2022 (Posting date) until May 17, 2022 (Opening) at the ITDI BAC Office, Second Floor Metrology Bldg. upon payment of the applicable fee for Bidding Documents, pursuant to the latest Guidelines issued by the GPPB, in the amount of <?= NumberToWords::transformNumber('en', $items->bidding_docs_fee) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? 'pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->bidding_docs_fee, 2) ?>)<br><br>

		It may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS), provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.
	</div>
	<br>


	<div style="text-align:justify">

		8. The ITDI-BAC will hold a <strong>Pre-Bid Conference</strong> on <strong> <?= ($prebid->option_date == NULL ? '' :  Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'php:M d, Y')) ?></strong>, will start at <strong>8:00 AM</strong>, at the <strong>Metrology Conference Room 2nd Floor ITDI Metrology Building, DOST Compound Gen. Santos Ave. Bicutan, Taguig City</strong> <br><br>

		9. Bids must be delivered on <strong><?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'php:M d, Y') ?> between <?= Yii::$app->formatter->asDatetime(strtotime(isset($submissionTime) ?  $submissionTime : ''), 'H:i a') ?> - <?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'H:i a') ?> </strong> at the Metrology Conference Room, 2nd Floor., ITDI Metrology Building, DOST Compound Bicutan, Taguig City. All bids must be accompanied by a bid security in any of the acceptable forms and in the amount stated in ITB Clause 18. <br><br>

		10. Bid opening shall be on <strong><?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'php:M d, Y') ?> and will start at <?= Yii::$app->formatter->asDatetime(strtotime(isset($openingTime) ?  $openingTime : ''), 'H:i a') ?> </strong> at the Metrology Conference Room, 2nd Floor ITDI Metrology Building, DOST Compound Bicutan, Taguig City. Bids will be opened in the presence of the bidders’ representatives who choose to attend. Bids submitted after 9:00 AM. on May 17, 2022 shall not be accepted. <br><br>

		All bidders are requested to observe the following during the pre-bid and opening of bids:<br>
		&nbsp;&nbsp;&nbsp; a. Only one (1) representative from each company shall be allowed to participate in the bidding; <br>
		&nbsp;&nbsp;&nbsp; b. Opening of bids will be done per project. Only representative participating in that project will be <br>&nbsp;&nbsp;&nbsp; allowed to enter the conference room; <br>
		&nbsp;&nbsp;&nbsp; c. Bidders will be given a designated waiting area; <br>
		&nbsp;&nbsp;&nbsp; d. Maximum of ten (10) persons will be allowed to stay inside the conference room; <br>
		&nbsp;&nbsp;&nbsp; e. Representatives shall be required to wear face mask and observed the minimum safety protocols at <br> &nbsp;&nbsp;&nbsp; all times; <br>
		&nbsp;&nbsp;&nbsp; f. Social/Physical distancing maintaining at least one (1) meter apart.
		<br><br>

		11. The Industrial Technology Development Institute (ITDI) reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 41 of RA 9184 and its IRR, without thereby incurring any liability to the affected bidder or bidders.<br><br>

		12. The Bidders shall submit three (3) copies of their duly accomplished eligibility requirements, Technical and Financial Proposal into one (1) sealed envelope.<br><br>

		13. The participating bidders/contractors should have completed similar project of at least 50% of the ABC, and have the key personnel and equipment, (listed in the eligibility forms) available for the prosecution of the contract. The BAC will use non-discretionary pass and fail criteria in the eligibility Check/Screening as well as the examination of the bids. The BAC will conduct post qualification of the lowest calculated bid..<br><br>

		14. The ITDI reserves the right to reject any and all bids, declare a failure of bidding, and not to award the contract if 1.) there is a prima facie evidence of collusion between appropriate public officers or employee, or between the BAC and any of the bidders themselves, or between the bidders and a third party, including any act which restricts, suppresses, or nullifies or tends to restrict , suppress or nullify competition; 2.) the BAC is found to have failed in the prescribed bidding procedures; or 3) for any justifiable and reasonable ground where the award of the contract will not redound to the benefit of the government as defined in the IRR. <br><br><br> For further information, please refer to: <br><br><br><br><br><br>

			<strong>ENGR. APOLLO VICTOR O. BAWAGAN</strong><br>
			BAC Chairperson – Sub.Com. for Infrastructure<br>
			Industrial Technology Development Institute<br>
			Telefax 837-20-71 local 2221<br>

	</div>
	<br><br><br>

	<div style="text-align:left; padding-left: 50%">
		Approved for posting by: <br><br><br>
		<strong><i>DR.ANNABELLE V. BRIONES</i></strong><br>
		Director
	</div>
	<br>


</div>