<?php

use app\models\User;
use app\modules\PurchaseRequest\models\Supplier;
use NumberToWords\NumberToWords;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\modules\PurchaseRequest\models\PurchaseRequest */

$assignatory = User::find()->where(['id' => $latest_assignatory->chairperson_id])->one();
$co_assignatory = User::find()->where(['id' => $latest_assignatory->co_chairperson_id])->one();
$listData = ArrayHelper::map($members, 'members_id', function ($model) {
	return $model['members_id'];
});
$member_assignatory = User::find()->where(['id' => $listData])->asArray()->all();


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


<div class="resolution">

	<div style="text-align:center">
		<strong>BAC Resolution Declaring Lowest Calculated/Rated and Responsive Bid </strong>
		<br> RESOLUTION NO. LCRB- <?= $model->resolution_no ?>
	</div>

	<br>
	<div style="text-align:justify">
		WHEREAS, The ITDI posted the Invitation to Bid for the <strong> <?= $model->prItemsdisplay->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $model->prItemsdisplay->unit ?> <?= $description->itemexplode ?> </strong> with an Approved Budget of the Contract (ABC) of <strong> <?= NumberToWords::transformNumber('en', $description->total_cost) ?> <?= ($description->totalCostDecimal != 0 && $description->totalCostDecimal != null) ? ' pesos and ' . NumberToWords::transformNumber('en', $description->totalCostDecimal) . ' centavos' : '' ?> (P <?= number_format($description->total_cost, '2') ?>)</strong> to ITDI website, the Philippine Government Electronic Procurement System (PhilGEPS), conspicuous places at the premises of the ITDI and DOST compound continuously from <?= Yii::$app->formatter->asDatetime(strtotime($posted->option_date), 'php:M d, Y') ?> to <?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php:M d, Y') ?>;
		<br><br> WHEREAS, in response to the said advertisements, <strong> <?= ucwords(convertNumberToWord($countSupplier)) ?> (<?= $countSupplier ?>) </strong> suppliers signified interest, purchased bidding documents and attended the Pre-bid Conference on <?= Yii::$app->formatter->asDatetime(strtotime($prebid->option_date), 'php:M d, Y') ?> ;
		<br><br> WHEREAS, <u><?= $countSupplier ?></u> bids were submitted on <?= Yii::$app->formatter->asDatetime(strtotime($openbid->option_date), 'php:M d, Y') ?> during the opening of bids and passed the preliminary examination of bids using pass/fail criteria (eligibility, technical and financial requirements);
		<br><br> WHEREAS, tabulated below is the result of the evaluation:
		<!-- <br><br> NOW, THEREFORE, We, the Members of the Bids and Awards Committee (BAC) hereby RESOLVE as it is hereby RESOLVED to declare of <strong><?= $model->supplierdisplay->supplier_name  ?></strong> with a bid amount of <strong> <?= ucwords(convertNumberToWord($model->supplier_price)) ?> (P <?= number_format($model->supplier_price, '2') ?>)</strong> (BID Amount of Supplier) only as the <strong>winning bidder</strong> for the <strong> <?= $model->prItemsdisplay->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $model->prItemsdisplay->unit ?> <?= $description->itemexplode ?> </strong> . -->
	</div>
	<br>

	<table class="abstract-items" style="width:100%">
		<thead>
			<tr>
				<th>SUPPLIER</th>
				<th>BID PRICE</th>
				<th>REMARKS</th>
			</tr>
		</thead>
		<tr>
			<?php

			foreach ($complyBidcount as $bid) {
				$supplier = Supplier::find()->where(['id' => $bid->supplier_id])->one();

				// $biddinglist = BiddingList::find()->where(['item_id' => $description->id])->andWhere(['supplier_id' => $supplier->id])->orderBy(['id' => SORT_DESC])->one();

				echo '<tr>
						<td style ="width: 25%; text-align: center">' . $supplier->supplier_name . '</td>
						<td style ="width: 20%; text-align: center">' . number_format($bid->supplier_price, 2) . '</td>
						<td style ="width: 25%; text-align: left">' . '' . '</td>';

				'</tr>';
			}
			?>
		</tr>
	</table>
	<br>
	<div style="text-align:justify">
		WHEREAS, upon careful examination, validation, verification and post qualification of all the eligibility requirements, technical specifications and financial proposal, the bid of <?= $model->supplierdisplay->supplier_name  ?> was evaluated to be responsive and substantially complying.
		<br><br> NOW, THEREFORE, We, the Members of the Bids and Awards Committee (BAC) hereby RESOLVE as it is hereby RESOLVED:
		<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; a) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; To declare <strong><?= $model->supplierdisplay->supplier_name  ?></strong> as the Lowest Calculated and Responsive Bidder for the <br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong> <?= $model->prItemsdisplay->bid_title ?> of <?= ucwords(convertNumberToWord($description->quantity)) ?> (<?= $description->quantity ?>) <?= $model->prItemsdisplay->unit ?> <?= $description->itemexplode ?> </strong>
		<br><br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; b) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; To recommend for approval by the Director of the ITDI the foregoing findings.
	</div>

	<div style="padding-top:1000px;">
		<br>
		RESOLVE, at the <i><u>BAC Rm. Metrology Building, ITDI, </u></i>this <u>DAY</u> day of <u>MONTH</u> 2021.
	</div>

	<div style="text-align: center;">
		<br> <br><br><br>
		DR. JANET F. QUIZON <br>
		<span style=" font-size: meduim">Chairperson, BAC </span>
	</div>

	<table class="signatories">
		<tr>
			<td style="padding-left: 100px; padding-right: 50px"> JOSEFINA CELORICO	 </td>
			<td style="padding-left: 100px"> <?= $co_assignatory->profile->fname . '  ' . $co_assignatory->profile->lname ?> </td>
		</tr>
		<tr>
			<td style="padding-left: 70px; padding-right: 50px; text-align: center;">Co-Chairperson</td>
			<td style="text-align: center; padding-left: 100px">Co-Chairperson</td>
		</tr>
	</table>



	<?php
	echo ('<table><tr>');
	$i = 0;
	foreach ($member_assignatory as $list) {
		$i++;
		$member_list = User::find()->where(['id' => $list])->one();

		echo '<td style = "padding-left:100px;padding-right:70px;padding-top: 70; text-align: center; font-family: Arial" > ' . $member_list->profile->fname . '  ' . $member_list->profile->lname . '<br> Member </span></td>';


		if ($i % 2 == 0) {
			// echo '<td style = "padding: 10px"><u>' .  $member_list->profile->fname . '  ' . $member_list->profile->lname . '</u></td>' . ' ';
			echo '</tr><tr>';
		}
	}
	echo '</tr></table>';
	?>

	<div style="padding-top: 100px">
		Approved by:
		<br> <br><br><br>
		<strong>ANNABELLE V. BRIONES, PhD</strong><br>
		<div span style=" font-size: meduim; padding-left: 70px">Director </div>

	</div>



</div>