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


<div class="invitation-to-bid-pdf" style = "font-family:Arial, Helvetica, sans-serif">
	<div style="text-align:center; text-transform: uppercase">
		<strong>INVITATION TO BID FOR <?= $items->bid_title ?> </strong>
	</div>

	<br>
	<div style="text-align:justify; ">
		1. The <strong> Industrial Technology Development Institute </strong>, through "<?= ($purchaserequest->charge_to == 0 ? 'GAA': $purchaserequest->chargedisplay->project_title )  ?>" intends to apply the amount of <strong> <?= NumberToWords::transformNumber('en', $items->total_cost) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? 'pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->total_cost, 2) ?>) </strong> only being the Approved Budget for the Contract (ABC) to payment for the <strong> “<?= $items->bid_title ?> of <?= ucwords(convertNumberToWord($items->quantity)) ?> ( <?= $items->quantity ?> ) <?= $items->unit ?> <?= $items->itemexplode ?> ” </strong>.
	</div>
	<br>

	<div style="text-align:justify">
		2. The <strong> Industrial Technology Development Institute (ITDI) </strong> now invites bids for the above mentioned equipment. Delivery of the Goods is required within <strong> <?= $purchaserequest->delivery_period ?> Calendar Days </strong>. Bidders should have completed, within five (5) years from the date of submission and receipt of bids, a contract similar to the Project. The description of an eligible bidder is contained in the Bidding Documents, particularly, in Section II, Instruction to Bidders.
		<br><br>

		3. Bidding will be conducted through open competitive bidding procedures using a non- discretionary “pass/fail” criterion as specified in the 2016 Revised Implementing Rules and Regulations (IRR) of Republic Act (RA) 9184, otherwise known as the “Government Procurement Reform Act”. <br><br>

		<div style="margin-left: 15px">
			(i) &nbsp;&nbsp; &nbsp; Bidding is restricted to Filipino citizens/sole proprietorships, partnerships, or organizations with at least sixty percent (60%) interest or outstanding capital stock belonging to citizens of the Philippines, and to citizens or organizations of a country the laws or regulations of which grant similar rights or privileges to Filipino citizens, pursuant to RA 5183.
		</div> <br>

		4. Interested bidders may obtain further information and inspect the Bidding Documents at the <strong> ITDI BAC Secretariat, Second Floor Metrology Bldg. </strong> starting <strong> <?= Yii::$app->formatter->asDatetime(strtotime($philgeps->option_date), 'php:M d, Y') ?> </strong> from <strong> <?= Yii::$app->formatter->asDatetime(strtotime($philgeps->option_date), 'php: H:i A') ?> – 4:00 PM. </strong> <br><br>

		5. A complete set of Bidding Documents may be acquired by interested Bidders from <strong> <?= Yii::$app->formatter->asDatetime(strtotime($philgeps->option_date), 'php:M d, Y') ?> </strong> until <strong> <?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php:M d, Y') ?> </strong> at the ITDI BAC Office, Second Floor Metrology Bldg. upon payment of the applicable fee for Bidding Documents amounting to <strong> <?= ucwords(convertNumberToWord($items->bidding_docs_fee)) ?> ( Php <?= number_format($items->bidding_docs_fee, 2) ?>) </strong>, pursuant to the latest Guidelines issued by the GPPB. <br><br>

		It may also be downloaded free of charge from the website of the Philippine Government Electronic Procurement System (PhilGEPS), provided that Bidders shall pay the applicable fee for the Bidding Documents not later than the submission of their bids.

	</div>
	<br>


	<div style="text-align:justify">

		6. The ITDI-BAC will hold a <strong>Pre-Bid Conference</strong> on <strong> <?= ($prebid->option_date == NULL ? '' :  Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'php:M d, Y')) ?></strong>, will start at <strong>8:00 AM</strong>, at the <strong>Metrology Conference Room 2nd Floor ITDI Metrology Building, DOST Compound Gen. Santos Ave. Bicutan, Taguig City</strong>, discussion of the Technical Specifications will be according to Item Nos. <br><br>

		7. Bid must be duly received by ITDI-BAC Secretariat at the <strong>Metrology Conference Room, 2nd Floor, ITDI Metrology Building, DOST Compound Bicutan Taguig City</strong> on <strong><?= ($openbid->option_date == NULL ? '' :  Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php:M d, Y')) ?></strong> between <strong>8:00 AM - 9:00 AM</strong>. All Bids must be accompanied by a bid security in any of the acceptable forms and in the amount stated in ITB Clause 18. <br><br>

		8. Bid Opening shall be on <strong> <?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'php:M d, Y') ?> </strong>and will start at <strong>9:00 AM</strong> at the <strong>Metrology Conference Room, 2nd floor, ITDI, Metrology Building, Bicutan, Taguig City</strong>. Bids will be opened in the presence of the bidders,representatives who choose to attend. Laye bids shall not be accepted. <br><br>

		9. All bidders are requested to observe the following during the pre-bid and opening of bids:<br>
		&nbsp;&nbsp;&nbsp; a. Only one (1) representative from each company shall be allowed to participate in the bidding; <br>
		&nbsp;&nbsp;&nbsp; b. Pre-bid and opening of bids will be done per project. Only representative participating in that project <br> &nbsp;&nbsp;&nbsp; will be allowed to enter the conference room; <br>
		&nbsp;&nbsp;&nbsp; c. Bidders will be given a designated waiting area; <br>
		&nbsp;&nbsp;&nbsp; d. Maximum of ten (10) persons will be allowed to stay inside the conference room; <br>
		&nbsp;&nbsp;&nbsp; e. Representatives shall be required to wear face mask and observed the minimum safety protocols at <br> &nbsp;&nbsp;&nbsp; all times; <br>
		&nbsp;&nbsp;&nbsp; f. Social/Physical distancing maintaining at least one (1) meter apart.
		<br><br>

		10. <strong> The Industrial Technology Development Institute (ITDI) </strong> reserves the right to reject any and all bids, declare a failure of bidding, or not award the contract at any time prior to contract award in accordance with Section 41 of RA 9184 and its IRR, without thereby incurring any liability to the affected bidder or bidders.

		10. For further information, please refer to: <br><br><br>
		<strong><i>DR. JANET F. QUIZON</i></strong><br>
		ITDI BAC Chairperson <br>
		Industrial Technology Development Institute <br>
		Telefax No.: 837-20-71 local 2221 <br>
		E-mail add: admbac@itdi.dost.gov.ph <br>
	</div>
	<br><br><br>

	<div style="text-align:center">
		Approved for posting by: <br><br><br>
		<strong><i>DR.ANNABELLE V. BRIONES</i></strong><br>
		Director
	</div>
	<br>


</div>