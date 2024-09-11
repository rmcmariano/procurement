<?php

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */

use app\modules\PurchaseRequest\models\ItemSpecification;

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


<div class="bulletin" style="font-size: 11pt; font-family:Arial, Helvetica, sans-serif">
	<div style="text-align:justify; margin-left: 20px; margin-top: 5x; margin-right: 10px">
		<table style="width: 100%; font-family:Arial, Helvetica, sans-serif">
			<tr>
				<td style="width:15%; font-size: medium; padding-top: 1%"><strong>PROJECT: </strong></td>
				<td style="width:75%; font-size: medium; padding-top: 1%"><?= $item->bid_title ?></td>
			</tr>
			<tr>
				<td style="width:15%; font-size: medium; padding-top: 1%"><strong>LOCATION: </strong></td>
				<td style="width:75%; font-size: medium; padding-top: 1%">DOST Compound, Gen. Santos Ave., Bicutan, Taguig City</td>
			</tr>
			<tr>
				<td style="width:15%; font-size: medium; padding-top: 1%"><strong>OWNER: </strong></td>
				<td style="width:75%; font-size: medium; padding-top: 1%">Industrial Technology Development Institute (ITDI)</td>
			</tr>
			<tr>
				<td style="width:15%; font-size: medium; padding-top: 1%"><strong>SUBJECT: </strong></td>
				<td style="width:75%; font-size: medium; padding-top: 1%"><?= $bidbulletin->bidbulletin_no ?></td>
			</tr>
			<tr>
				<td style="width:15%; font-size: medium; padding-top: 1%"><strong>DATE: </strong></td>
				<td style="width:75%; font-size: medium; padding-top: 1%"> <?= Yii::$app->formatter->asDatetime(strtotime($bidbulletin->date_posted), 'php:F d, Y')  ?></td>
			</tr>
		</table>
		<br>

		<hr style="height:1px; width:100%;  color:black;  margin-top: -10px; ">

		<div style="text-align:justify;  font-size: 11pt;">
			This Bid Bulletin No. 1 is being issued to Bidders for their information and guidance in the preparation of their bids and shall be taken into consideration in their proposal. This bid bulletin will form part of the contract for this project. <br>

			<div style="text-align:justify;">
				<ol>
					<li><strong>Governing Rules:</strong><br>
						Bidding and contract award for the above project shall be governed by the Revised implementing Rules and Regulations of RA 9184 and the Philippine Bidding Documents (PBD), Fifth Edition, October 2016. Any discrepancy between the General Conditions and RA 9184, R.A. 9184 shall prevail.
					</li><br>
					<li>
						<strong>Marking and Sealing of Bids</strong><br><br>
						<ol type="a">
							<li>Contents of sealed envelope marked <strong>"ORIGINAL BID</strong><br>
								Sealed Envelope 1 marked <strong>"ORIGINAL - Eligibility & Technical Proposal</strong><br>
								Sealed Envelope 2 marked <strong>"ORIGINAL - Financial Proposal</strong><br><br>
							</li>
							<li>Contents of sealed envelope marked <strong>"COPY NO. 1</strong><br>
								Sealed Envelope 1 marked <strong>"Copy No. 1 - Eligibility & Technical Proposal"</strong><br>
								Sealed Envelope 2 marked <strong>"Copy No. 1 - Financial Proposal"</strong><br><br>
							</li>
							<li> Contents of sealed envelope marked <strong>"COPY NO. 2</strong><br>
								Sealed Envelope 1 marked <strong>"Copy No. 2 - Eligibility & Technical Proposal"</strong><br>
								Sealed Envelope 2 marked <strong>"Copy No. 2 - Financial Proposal"</strong><br><br>
							</li>
							<li>
								The sealed outer envelopes marked <strong> "ORIGINAL BID", "COPY NO. 1" and "COPY No. 2" </strong> shall be enclosed in <strong>one sigle envelope/package.</strong> <br><br>
							</li>
							<li>
								All envelopes shall contain the following: <br>
							</li>
						</ol>
					</li>
					<br><br><br><br>
					<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 90%; text-align:center; font-size: 11pt ">
						<tr>
							<td style="text-align: center; padding-bottom: 1%"><strong>PROJECT: (NAME OF THE PROJECT)</strong></td>
						</tr>
						<tr>
							<td style="text-align: left; padding-bottom: 1%">(NAME AND ADDRESS OF BIDDER IN CAPITAL LETTER)</td>
						</tr>
						<tr>
							<td style="text-align: left; padding-bottom: 1%"><strong>TO: THE INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE (ITDI)</strong></td>
						</tr>
						<tr>
							<td style="text-align: left;"><strong>DR. JANET F. QUIZON</strong></td>
						</tr>
						<tr>
							<td style="text-align: left;"><strong>ITDI-BAC CHAIRPERSON</strong></td>
						</tr>
						<tr>
							<td style="text-align: left;"><strong>METROLOGY BUILDING, DOST COMPOUND</strong></td>
						</tr>
						<tr>
							<td style="text-align: left;"><strong>BICUTAN, TAGUIG CITY</strong></td>
						</tr>
						<tr>
							<td style="text-align: left;"><strong>"DO NOT OPEN BEFORE: (Stipulated Date and Time of Opening"</strong></td>
						</tr>
					</table>
					<br>

					<li>
						<strong> Items to be considered in the preparation of Bid</strong><br><br>
						3.1 <strong>Bid Proposal</strong> shall be in Philippine currency<br><br>
						3.2 The bid shall be signed on each and every page by the duly authorized signatory of the bidder. Items with erasures on the bid should bear the initials of the authorized signatory. <br><br>
						3.3 Bidders shall submit a Bid Securing Declaration OR at least one (1) other Bid Security, the amount of which shall not be less than the required percentage of the ABC in accordance with the following schedule: <br>

						<br>

						<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 90%; font-size: 11pt; text-align:justify; ">
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:center;padding: 1% ">Forms of Bid Security</td>
								<td style="width: 10%; border:1px solid; font-size: small; text-align:center; ">Amount of Bid Security <br> (Not less than to Percentage of the ABC)</td>
							</tr>
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">a) Cash of Cashier's/manager's check issued by a Universal or Commercial Bank.</td>
							</tr>
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">b) Bank draft /guarantee or irrevocable letter of credit issued by a Universal or commercial Bank: Provided, however, that it shall be confirmed or authenticated by a Universal or Commercial Bank, if issued by a foreign bank.</td>
								<td rowspan="1" style="width:15%;  font-size: small; text-align:center;">Two percent (2%)</td>
							</tr>
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">c) Surety bond callable upon demand issued by a surety or insurance company duly certified by the Insurance Commission as authorized to issue such security. </td>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">Five percent (5%) </td>
							</tr>
						</table> <br>

						3.4 Bid bonds should be posted in favor of Industrial Technology Development Institute. If other than GSIS, the bid bond should be accomplished with certificate of accreditation from the Insurance Commission. Bid bonds should be submitted with original receipts of payments. Bid security shall be Callable on Demand with a validity period of 120 calendar days.<br><br>
						3.5 To guarantee the faithful performance by the winning bidder of its obligations under the contract, it shall post perfomance security taken from the categories below the amount of which shall not be less than the percentage of the total contract price in accordance price with the following schedule: <br><br>

						<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 90%; font-size: 11pt; text-align:justify;">
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:center; padding: 1% ">Forms of Performance Security</td>
								<td style="width: 10%; border:1px solid; font-size: small; text-align:center; padding: 1% ">Amount of Performance Security <br> (Not less than to Percentage of the Total Contract Price)</td>
							</tr>
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">a) Cash of Cashier's/manager's check issued by a Universal or Commercial Bank.</td>
							</tr>
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">b) Bank draft /guarantee or irrevocable letter of credit issued by a Universal or commercial Bank: Provided, however, that it shall be confirmed or authenticated by a Universal or Commercial Bank, if issued by a foreign bank.</td>
								<td rowspan="1" style="width:15%;  font-size: small; text-align:center;">Goods and Consulting Services - Five percent (5%)</td>
							</tr>
							<tr>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">c) Surety bond callable upon demand issued by a surety or insurance company duly certified by the Insurance Commission as authorized to issue such security. </td>
								<td style="width:15%; border: 1px solid; font-size: small; text-align:justify; padding: 1%">Thirty percent (30%) </td>
							</tr>
						</table> <br>
					</li>
					<li>
						<strong>Submission of Bids - <i><?= Yii::$app->formatter->asDatetime(strtotime(isset($testQuote->option_date) ?  $testQuote->option_date : ''), 'php:M d, Y') ?> , <?= Yii::$app->formatter->asDatetime(strtotime(isset($submissionTime) ?  $submissionTime : ''), 'php: h:i a') ?> - <?= Yii::$app->formatter->asDatetime(strtotime(isset($testQuote->option_date) ?  $testQuote->option_date : ''), 'php: h:i a') ?></i> </strong><br>
						<ul style="list-style-type: disc; font-size: 10pt">
							<?php
							$amount = 0;
							foreach ($prItems as $prItem) {
								echo
								'<li><strong>' . $prItem->item_name . '</strong></li>';
							}
							?>
						</ul><br>
						<strong><i>Late bids shall not be accepted</i></strong>
					</li><br>
					<li>
						<strong>Opening of Bids - <i><?= Yii::$app->formatter->asDatetime(strtotime(isset($testQuote->option_date) ?  $testQuote->option_date : ''), 'php:M d, Y') ?> , will start at time <?= Yii::$app->formatter->asDatetime(strtotime(isset($openingTime) ?  $openingTime : ''), 'php: h:i a') ?> </i> </strong><br>
						<ul style="list-style-type: disc; font-size: 10pt">
							<?php
							$amount = 0;
							foreach ($prItems as $prItem) {
								echo
								'<li><strong>' . $prItem->item_name . '</strong></li>';
							}
							?>
						</ul><br>
					</li>
					<li>
						<strong>Eligibility & Technical Specification: <br><br></strong>
						<ul style="list-style-type: disc; font-size: 10pt">
							<?php
							$amount = 0;
							foreach ($prItems as $prItem) {
								echo
								'<strong>' . $prItem->bid_title . ' of ' . ucwords(convertNumberToWord($prItem->quantity)) . '(' . $prItem->quantity . ') ' . $prItem->item_name . '- ' . 'Php ' . number_format($prItem->total_cost, '2') . '</strong><br>';

								foreach ($itemSpecs as $itemSpec) {
									echo
									'<ul style = "list-style-type: disc; font-size: 10pt">
									<li> ' . nl2br($itemSpec->bidbulletin_changes) . '</li>
									</ul>';
								}
							}
							?>
						</ul>
					</li>
				</ol>
			</div>

			&nbsp;&nbsp;*Only those technical specifications indicated herein will be changed, those that are not mentioned will be retained as indicated in Section VII of the Bidding Documents.<br><br>
		</div><br><br>
		Issued by:<br><br><br>
		<strong>DR. JANET F. QUIZON</strong><br>
		Chairperson, ITDI-BAC
	</div>
</div>