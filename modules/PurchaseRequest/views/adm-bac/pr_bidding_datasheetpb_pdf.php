<?php

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

<div class="bid-datasheet-pdf">
	<div style="text-align:center; padding-top: 40%; padding-bottom: 90%">
		<h1><strong><i>Section III. Bid Data Sheet</i></strong></h1>
	</div>

	<div style="text-align:center; padding-top: 2%">
		<strong>Bid Data Sheet</strong>
	</div>

	<div style="text-align:justify;">
		<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:justify;  table-layout: fixed;  ">
			<tr style="border: 1px solid black; padding: 1%">
				<th style="width: 10%; border-right: 1px solid;  font-size: small">ITB Clause</th>
				<th style="width: 90%; border-right: 1px solid;  font-size: small"></th>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 5%">The Procuring Entity is <strong><i> INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE </i></strong>
					<br><br>The name of the Contract is: <br><br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; 1. &nbsp;&nbsp; <strong> “<?= $items->bid_title ?> of <br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <?= ucwords(convertNumberToWord($items->quantity)) ?> (<?= $items->quantity ?>) <?= $items->unit ?> <?= $items->itemexplode ?>” </strong>
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;The identification number of the Contract is: <strong> <?= $quot->quotation_no ?>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">1.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 5%">The requirements and references are: <br><br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; 1. &nbsp;&nbsp; <strong> <?= $items->bid_title ?> of <br> &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp; <?= ucwords(convertNumberToWord($items->quantity)) ?> (<?= $items->quantity ?>) <?= $items->unit ?> <?= $items->itemexplode ?> </strong> </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 5%">The Funding Source is: <br><br>
					The Government of the Philippines (GOP) through <strong>“<?= ($purchaserequest->charge_to == 0 ? 'GAA': $purchaserequest->chargedisplay->project_title ) ?> ”</strong> in the total amount of <strong><?= NumberToWords::transformNumber('en', $items->total_cost) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? 'pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->total_cost, '2') ?> ) </strong> </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">3.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">None of the circumstances mentioned in the <strong> ITB </strong> Clause exists in this Project. Foreign bidders, except those falling under <strong> ITB </strong> Clause 5.2(b), may not participate in this Project.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>Bidders must have completed within five (5) years, a single contract that is similar to this Project, equivalent to at least 50% of the ABC.</i> </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.4</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Bidder must submit a computation of its <strong> Net Financial Contracting Capacity (NFCC) </strong>, which must be at least equal to the ABC to be bid, calculated as follows: <br><br>
					NFCC = [(Current assets minus current liabilities) (15)] minus the value of all outstanding or uncompleted portions of the projects under ongoing contracts, including awarded contracts yet to be started coinciding with the contract for this Project. <br><br>
					The values of the domestic bidder’s current assets and current liabilities shall be based on the latest Audited Financial Statements submitted to the BIR. For purposes of computing the foreign bidder’s NFCC, the value of the current assets and current liabilities shall be based on their Audited Financial Statements prepared in accordance with international financial reporting standards. </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">7</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">8.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“Subcontracting is not allowed.” </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">8.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“Not applicable”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">9.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Procuring Entity will hold a <strong> Pre-bid Conference </strong> for this Project on <i> <strong><?= Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'php:M d, Y') ?> at <?= Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'php: H:i A') ?> </strong> at the <strong>Metrology Conference Room </strong> 2nd Floor., Metrology Building, DOST Compound Bicutan Taguig City. </i></td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">10.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Procuring Entity’s address is:
					<br><br><strong>INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE</strong>
					<br><br><i>DOST Comp. Gen. Santos Ave., Bicutan, Taguig City
						<br><br>Tel. No. 8837-20-71 loc. 2221
						<br><br>Telefax: 8837-20-71 loc. 2221
						<br><br>Email address: <strong> admbac@itdi.dost.gov.ph </strong></i>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1(a)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">a) Eligibility Documents –
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; <u>Class “A” Documents:</u>
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;(i) &nbsp;&nbsp; &nbsp;&nbsp; PhilGEPS Certificate under Platinum Membership;
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;(ii) &nbsp;&nbsp; &nbsp;&nbsp; Statement of all its ongoing and completed government and private contracts
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;(ii) &nbsp;&nbsp; &nbsp;&nbsp; Audited financial statements, stamped “received” by the Bureau of Internal Revenue <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; (BIR) or its duly accredited and authorized institutions, for the preceding calendar year, <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; which should not be earlier than two (2) years from bid submission;
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;(iv) &nbsp;&nbsp; &nbsp;&nbsp;
					<strong>NFCC computation; </strong>
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;(v) &nbsp;&nbsp; &nbsp;&nbsp;
					<strong>Tax Clearance per Executive Order 398, Series of 2005, as finally reviewed and <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; approved by the BIR;</strong>
					<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;(vi) &nbsp;&nbsp; &nbsp;&nbsp;
					Those that are indicated in the Checklist provided together with the Bidding <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Documents
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1(a)(i)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"> “No other acceptable proof of registration is recognized.”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1(a)(ii)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The statement of all ongoing government and private contracts shall include all such contracts within two (2) years prior to the deadline for the submission and receipt of bids.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“No additional requirements.”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.1(b)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.1(c)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“No additional requirements.”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Approved Budget for the Contract is:
					<br><br><strong> <?= ucwords(convertNumberToWord($items->total_cost)) ?> (Php <?= number_format($items->total_cost, '2') ?>).
						<br><br>Any bid with a financial component exceeding this amount shall not be accepted.</strong>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">15.4(a)(iv)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“No incidental services are required.”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">15.4(b)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“Not applicable”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">15.5
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>Bid prices shall be fixed.  Adjustable price proposals shall be treated as non-responsive and shall be rejected.</i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">16.1(b)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Bid prices for Goods supplied from outside of the Philippines shall be quoted in Philippine Pesos.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">16.3
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“Not applicable”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">17.1
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Bids will be valid until: <i>120 calendar days from the date of the opening of bids </i></td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">18.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The bid security shall be in the form of a <strong>Bid Securing Declaration </strong> of any of the following forms and amounts:
				<br><br>1. &nbsp;&nbsp;&nbsp;&nbsp;	The amount of not less than 2% of the ABC, if bid security is in cash, cashier’s/manager’s check, bank <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; draft/guarantee or irrevocable letter of credit; or
				<br><br>2. &nbsp;&nbsp;&nbsp;&nbsp;	The amount of not less than 5% of ABC if bid security is in Surety Bond. </td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">18.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The bid security shall be valid until:
				<br><br>120 days from the date of Bid Opening
				<br><br>Callable on Demand </td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">20.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Each Bidder shall submit <i> [1] </i> original and <i> [2] </i> copy of the first and second components of its bid.</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">21</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The address for submission of bids is:
				<br><br>THE INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE (ITDI)
				<br><br><strong>DR. JANET F. QUIZON</strong>
				<br><br>ITDI BAC CHAIRPERSON
				<br><br>ITDI Metrology Conference Room, 2nd Floor, Metrology Building, DOST Comp., General Santos Ave., Bicutan Taguig City
				<br><br>The submission of bids is: 
				<br><br><strong><?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php:M d, Y')?> , <?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php: H:i A')?></strong>
				<br><br>Late bids shall not be accepted.</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">24.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The place of bid opening is:
				<br><br>ITDI Metrology Conference Room, 2nd Floor, Metrology Building, DOST Comp., General Santos Ave., Bicutan Taguig City
				<br><br>The date and time of bid opening is:
				<br><br><strong><?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php:M d, Y')?> , <?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php: H:i A')?>
				<br><br>All bidders are requested to observe the following during the submission and opening of bids:</strong>
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; a. &nbsp;	Only one (1) representative from each company shall be allowed to participate in the bidding;
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; b. &nbsp;	Opening of bids will be done per item. Only representative participating in that item will be allowed to <br>  &nbsp;&nbsp;&nbsp;&nbsp;  enter the conference room;
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; c. &nbsp;	Bidders will be given a designated waiting area;
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; d. &nbsp; Maximum of ten (10) persons will be allowed to stay inside the conference room;
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; e. &nbsp;	Representatives shall be required to wear face mask and face shield or any protective gear at all <br>  &nbsp;&nbsp;&nbsp;&nbsp; times;
				 </td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small"></td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">
				 &nbsp;&nbsp;&nbsp;&nbsp; f. &nbsp;	Social/Physical distancing maintaining at least one (1) meter apart.  </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">24.2
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">27.1
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">28.3
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">28.3(a)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Partial bid is not allowed.  The goods are grouped in a single lot and the lot shall not be divided into sub-lots for the purpose of bidding, evaluation, and contract award.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">28.3(b)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>Bid modification is not allowed.</i></td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">28.4
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">29.2(a)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Only tax returns filed and taxes paid through the BIR Electronic Filing and Payment System (EFPS) shall be accepted.
				<br><br><br><i>NOTE: <strong>The latest income and business tax returns are those within the last six months preceding the date of bid submission.</strong></i> </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">29.2
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“No additional requirement”</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">32.4(f)
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">“No additional requirement”</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">33</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><strong>The performance security shall be in an amount not less than the required percentage of the total contract price in accordance with the following schedule:</strong>
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; <i> a) &nbsp;	If </i> cash, cashier’s/manager’s check issued by a Universal or Commercial Bank - <i> 5% of Total Contract <br> Price </i>
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; b) &nbsp;	<i>Bank draft/guarantee or irrevocable letter of credit issued by a Universal or Commercial Bank: <br> Provided, however, that it shall be confirmed or authenticated by a Universal or Commercial Bank, if issued by a foreign bank – 5% of Total Contract Price
				<br><br> &nbsp;&nbsp;&nbsp;&nbsp; c) &nbsp;	Surety bond callable upon demand issued by a surety or insurance company duly certified by the Insurance Commission as authorized to issue such security - 30% of Total Contract Price </i>
				</td>
			</tr>
		</table>
	</div>
</div>