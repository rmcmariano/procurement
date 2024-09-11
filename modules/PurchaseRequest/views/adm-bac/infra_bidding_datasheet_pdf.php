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
					<br><br>The name of the Contract is: <strong> “ <?= $items->bid_title ?> " </strong>
					<br><br> The identification number of the Contract is <?= $quot->quotation_no ?>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 5%">The Funding Source is: <br><br>
					The <strong> Industrial Technology Development Institute (ITDI) </strong> through <strong>“<?= $purchaserequest->chargedisplay->project_title ?> ”</strong> in the amount of <strong><?= NumberToWords::transformNumber('en', $items->total_cost) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? 'pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->total_cost, '2') ?> ) </strong> </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">3.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Bidding is restricted to eligible bidders as defined In ITB Clause 5.1 (a) (b) (c)</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Bidding is restricted to eligible bidders as defined In ITB Clause 5.1 (a) (b) (c)</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.4(a)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"> Refer to:
					<br><br>PCAB CIRCULAR NO. 001, SERIES OF 2009
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.4(b)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Not applicable</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">5.5</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Bidder must submit a computation of its Net Financial Contracting Capacity (NFCC), which must be at least equal to the ABC to be bid, calculated as follows:
					<br><br>
					NFCC = [(Current assets minus current liabilities) (15)] minus the value of all outstanding or uncompleted portions of the projects under ongoing contracts, including awarded contracts yet to be started coinciding with the contract for this Project.
					<br><br>
					The values of the domestic bidder’s current assets and current liabilities shall be based on the latest Audited Financial Statements (AFS) submitted to the BIR.
					<br><br>
					For purposes of computing the foreign bidders’ NFCC, the value of the current assets and current liabilities shall be based on their audited financial statements prepared in accordance with international financial reporting standards.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">8.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions. </td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">8.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">9.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Procuring Entity will hold a <strong> Pre-bid Conference </strong> for this Project on <i> <strong><?= Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'php:M d, Y') ?> at <?= Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'H:i') ?> </strong> at the <strong>Metrology Conference Room </strong> 2nd Floor., Metrology Building, DOST Compound Bicutan Taguig City. </i></td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">10.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The Procuring Entity’s address is:
					<br><br>
					ITDI, DOST Cpd., General Santos Avenue, Bicutan, Taguig City
					<br><br>
					Contact Person:<br>
					<strong>ENGR. APOLLO VICTOR O. BAWAGAN</strong><br>
					BAC-Chairperson, Sub.Com. for Infrastructure <br>
					Industrial Technology Development Institute <br>
					Telefax Nos. 837-2071 local 2221 <br>
					Email Add.: <strong>admbac@itdi.dost.gov.ph </strong>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">10.4</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1(a)(iii)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1(b)(iii.2)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The minimum work experience requirements for key personnel are the following: <br><br>
					<u>Key Personnel and Qualifications</u><br><br>
					&nbsp;&nbsp;&nbsp;a. Project Manager – A licensed Civil Engineer or Architect with at least 10 years of experience as <br> &nbsp;&nbsp;&nbsp; project manager of similar projects.<br><br>

					&nbsp;&nbsp;&nbsp;b. Project Architect – A licensed Architect with at least 8 years of experience as project architect of similar <br> &nbsp;&nbsp;&nbsp; projects.<br><br>

					&nbsp;&nbsp;&nbsp;c. Mechanical Engineer - A licensed Mechanical Engineer with 5-10 years as mechanical engineer of <br> &nbsp;&nbsp;&nbsp; similar projects, specifically related to design and installation of steam & water lines.<br><br>

					&nbsp;&nbsp;&nbsp;d. Electrical Engineer – A licensed Electrical Engineer with 5-10 years as electrical engineer of similar <br> &nbsp;&nbsp;&nbsp; projects.<br><br>

					&nbsp;&nbsp;&nbsp;e. Safety and Health Officer – With 3-5 years of experience as safety officer duly accredited by the <br> &nbsp;&nbsp;&nbsp; Department of Labor and Employment (DOLE). <br><br>

					&nbsp;&nbsp;&nbsp;f. Sanitary Engineer – A licensed Sanitary Engineer with 5-10 years of experience as Sanitary Engineer of <br> &nbsp;&nbsp;&nbsp; similar projects.<br><br>

					&nbsp;&nbsp;&nbsp;g. Office Engineer – Graduate of civil engineering or architecture degree and skilled CAD operator, with 3-5 <br> &nbsp;&nbsp;&nbsp; years of experience as office engineer of similar projects. <br><br>

					&nbsp;&nbsp;&nbsp;h. Foreman – With 5-10 years of experience as foreman of similar projects. <br><br>

				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">12.1(b)(iii.3)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The minimum major equipment requirements are the following: <br><br>
					<u>Equipment</u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u> Capacity </u> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u>Number of Units </u>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.1(a)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.1(b)</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">This shall include all of the following documents: <br><br>
					&nbsp;&nbsp;&nbsp; 1) Cash flow schedule.

				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">13.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The ABC is <strong><?= NumberToWords::transformNumber('en', $items->total_cost) ?> <?= ($items->totalCostDecimal != 0 && $items->totalCostDecimal != null) ? 'pesos and ' . NumberToWords::transformNumber('en', $items->totalCostDecimal) . ' centavos' : '' ?> (Php <?= number_format($items->total_cost, '2') ?> ) </strong>.<br><br>
					Any bid with a financial component exceeding this amount shall not be accepted.
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">14.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">15.4</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">16.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The bid prices shall be quoted in Philippine Pesos</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">16.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">17.1
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">18.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The bid security shall be in the form of a <strong>Bid Securing Declaration </strong> of any of the following forms and amounts:
					<br><br>1. &nbsp;&nbsp;&nbsp;&nbsp; The amount of not less than 2% of the ABC, if bid security is in cash, cashier’s/manager’s check, bank <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; draft/guarantee or irrevocable letter of credit; or
					<br><br>2. &nbsp;&nbsp;&nbsp;&nbsp; The amount of not less than 5% of ABC if bid security is in Surety Bond.
				</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">18.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.
				</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">20.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Each Bidder shall submit <i> (1) </i> original and <i> (2) </i> copy of the first and second components of its bid.</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">21</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The address for submission of bids is at <strong>ITDI Metrology Conference Room, 2nd Floor, Metrology Building, DOST Compound, Gen. Santos Avenue, Bicutan, Taguig City.</strong><br><br>
					The deadline for submission of bids is on <strong><?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'php:M d, Y') ?> , <?= Yii::$app->formatter->asDatetime(strtotime(isset($submissionTime) ?  $submissionTime : ''), 'H:i a') ?> - <?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'H:i a') ?> </strong><br>
				</td>
			</tr>
			<tr style="border: 1px solid black; ">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">24.1</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">The place of bid opening is <strong> ITDI Metrology Conference Room, 2nd Floor, Metrology Building, DOST Compound, Gen. Santos Avenue, Bicutan, Taguig City. </strong><br><br>
					The date and time of bid opening is on <strong><?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'php:M d, Y') ?> , <?= Yii::$app->formatter->asDatetime(strtotime(isset($openingTime) ?  $openingTime : ''), 'H:i a') ?> - <?= Yii::$app->formatter->asDatetime(strtotime(isset($openbid->option_date) ?  $openbid->option_date : ''), 'H:i a') ?> </strong>
				</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">24.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">24.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">27.3</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">Partial bid is not allowed. The infrastructure project is packaged in a single lot and the lot shall not be divided into sub-lots for the purpose of bidding, and contract award.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">27.4</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%">No further instructions.</td>
			</tr>
			<tr style="border: 1px solid black; padding: 1%">
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small">28.2</td>
				<td style="width: 90%; border-right: 1px solid;  font-size: small; padding-bottom: 3%"><i>The latest income and business tax returns are those within the last six (6) months preceding the date of submission.</i></td>
			</tr>
		</table>
	</div>
</div>