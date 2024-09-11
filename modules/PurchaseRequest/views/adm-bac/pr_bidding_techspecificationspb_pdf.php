<?php


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


<div class="technical-specification-pdf">
	<div style="text-align:center; ">
		<h1><strong><i>Section VI. Schedule of Requirements</i></strong></h1>
	</div>
	<div style="text-align:justify">
		The delivery schedule expressed as weeks/months stipulates hereafter a delivery date which is the date of delivery to the project site.
	</div>
	<div style="text-align:justify;padding-bottom: 90%">
		<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:center; font-size: small;  table-layout: fixed;">
			<tr style="border: 1px solid black">
				<th style="width: 10%; border-right: 1px solid;  font-size: small">Item No.</th>
				<th style="width: 50%; border-right: 1px solid;  font-size: small;">Description</th>
				<th style="width: 6%; border-right: 1px solid;  font-size: small;">Qty</th>
				<th style="width: 12%; border-right: 1px solid;  font-size: small;">Total</th>
				<th style="width: 12%; border-right: 1px solid;  font-size: small;">Delivered Weeks/Months</th>
			</tr>

			<tr>
				<td valign="top" style="width: 10%; border-right: 1px solid;  font-size: small; ">1.</td>
				<td valign="top" style="width: 50%; border-right: 1px solid;  font-size: small; text-align: right; padding-bottom: 5"><i><?= $items->bid_title ?> of <?= $items->itemexplode ?> <br><br><br><br><br><br> Delivery Period: <?= $purchaserequest->delivery_period ?> Calendar Days</i></td>
				<td valign="top" style="width: 6%; border-right: 1px solid;  font-size: small; "><?= $items->quantity ?></td>
				<td valign="top" style="width: 12%; border-right: 1px solid;  font-size: small; "><?= $items->quantity ?></td>
				<td valign="top" style="width: 12%; border-right: 1px solid;  font-size: small; text-align: right; "><?= $purchaserequest->delivery_period ?>, calendar days upon issuance of approved Purchase Order </td>
			</tr>
		</table>
	</div>

	<div style="text-align:center; padding-top: 50%; padding-bottom: 80%;">
		<h1><strong><i>Section VII. Technical Specifications</i></strong></h1>
	</div>

	<div style="text-align:center; ">
		<strong>Technical Specifications</strong>
	</div>

	<?php
	$itemNo = 1;
	?>

	<div style="text-align:justify;">
		<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%; text-align:justify; table-layout: fixed;  ">
			<tr style=" padding-bottom: 10%; font-size: small">
				<td valign="top" style="width: 5%; border-right: 1px solid">Item no.1 </td>
				<td valign="top" style="width: 5%; border-right: 1px solid"><strong><?= nl2br($items->item_name) ?></strong></td>
				<td valign="top" style="width: 5%; border-right: 1px solid"><strong>Statement of Compliance </strong> </td>
			</tr>
			<tr style="padding-bottom: 10%">
				<td valign="top" style="width: 5%; border-right: 1px solid; font-size: small"></td>
				<td valign="top" style="width: 65%; border-right: 1px solid;  font-size: small; ">
				TECHNICAL SPECIFICATION:
					<?php foreach ($itemSpecs as $spec) {
						echo '<br>' . nl2br($spec->description) . '<br>';
					} ?> <br><br><br>
				</td>
				<td valign="top" style="width: 30%; font-size: x-small;">
					Bidders must state here either “Comply” or “Not Comply” against each of the individual parameters of each Specification stating the corresponding performance parameter of the equipment offered. Statements of “Comply” or “Not Comply” must be supported by evidence in a Bidders Bid and cross-referenced to that evidence. Evidence shall be in the form of manufacturer’s un-amended sales literature, unconditional statements of specification and compliance issued by the manufacturer, samples, independent test data etc., as appropriate. A statement that is not supported by evidence or is subsequently found to be contradicted by the evidence presented will render the Bid under evaluation liable for rejection. A statement either in the Bidders statement of compliance or the supporting evidence that is found to be false either during Bid evaluation, post-qualification or the execution of the Contract may be regarded as fraudulent and render the Bidder or supplier liable for prosecution subject to the provisions of ITB Clause 3.1(a)(ii) and/or GCC Clause 2.1(a)(ii).
				</td>
			</tr>
			<tr style="padding-bottom: 10%">
				<td style="width: 5%; border-right: 1px solid; font-size: small"></td>
				<td style="width: 65%; border-right: 1px solid;  font-size: small; ">
					Delivery Terms: <?= $purchaserequest->delivery_period ?>
				</td>
				<td style="width: 30%; font-size: x-small;"> </td>
			</tr>
			<tr style="padding-bottom: 10%">
				<td style="width: 5%; border-right: 1px solid; font-size: small"></td>
				<td style="width: 65%; border-right: 1px solid;  font-size: small; ">
					Warranty: <?= $purchaserequest->warranty ?> <br><br>
				</td>
				<td style="width: 30%; font-size: x-small;"> </td>
			</tr>
		</table>
	</div>
</div>