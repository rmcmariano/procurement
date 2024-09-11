<?php

use app\models\User;
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
		<strong>BIDS AND AWARDS COMMITTEE </strong>
		<br> RESOLUTION NO.: <?= $model->resolution_no ?>
		<br><?= Yii::$app->formatter->asDatetime(strtotime($model->resolution_date), 'php: F d, Y')  ?>
	</div>

	<br>
	<div style="text-align:justify">
		The subject of this resolution deals with the request for <strong> <?= $model->prItemsdisplay->item ?> </strong> under Solicitation No.: <strong> <?= $model->quotationdisplay->quotation_no ?> </strong> and Purchase Request No.:<strong> <?= $model->quotationdisplay->pr_id ?>;</strong> <br> <br>
		WHEREAS, Section 53 of the 2016 Revised Implementing Rules and Regulations (IRR) of Republic Act No. 9184, otherwise known as the Government Procurement Reform Act, allows an agency to resort to negotiated procurement; <br> <br>
		WHEREAS, further, Section 53.9 of the Revised IRR of RA 9184, allows an agency to resort to Small Value Procurement for the procurement of goods, infrastructure projects and consulting services, where the amount involved does not exceed the threshold prescribed in Annex “H” of this IRR in the amount of One Million Pesos (P1,000,000.00) for NGAs, GOCCs, GFIs, SUCs and Autonomous Regional Government; <br> <br>
		WHEREAS, the Bids and Awards Committee (BAC) invited possible suppliers for the above-mentioned request through posting of Request for Quotation to PhilGEPS and/or canvassing; <br> <br>
		WHEREAS, on the bidding conducted on <strong> <?= Yii::$app->formatter->asDatetime(strtotime($model->quotationdisplay->option_date), 'php:M d, Y')  ?> </strong>, <strong> <?= ucwords(convertNumberToWord($countbid)) ?> (<?= $countbid ?>) </strong> supplier/s participated in the bidding process; <br> <br>
		WHEREAS, pursuant to Section 53.9, receipt of at least one (1) quotation from the supplier is sufficient to proceed with the evaluation thereof; <br><br>
		WHEREAS, the said request is needed <strong> <?= $model->purchaserequest->chargedisplay->project_title ?>; </strong><br><br>
		WHEREAS, after the evaluation conducted by the end-user and Bids and Awards Committee, the bid of <strong> <?= $model->supplierdisplay->supplier_name ?> </strong> conforms to the required technical specifications of the end-user; <br><br>
		WHEREFORE, foregoing premises considered, we, the Bids and Awards Committee, hereby recommend that the above-mentioned request be awarded to <strong> <?= $model->supplierdisplay->supplier_name ?> </strong> through Small Value Procurement under Negotiated Procurement pursuant to Sec. 53.9 of the 2016 Revised IRR of RA 9184 in the total amount of <strong><?= ucwords(convertNumberToWord($model->supplier_price)) ?> pesos (Php <?= number_format($model->supplier_price) ?>) </strong> only.
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
			<td style="padding-left: 100px; padding-right: 50px"> <?= $assignatory->profile->fname . '  ' . $assignatory->profile->lname ?> </td>
			<td style="padding-left: 100px"> <?= $co_assignatory->profile->fname . '  ' . $co_assignatory->profile->lname ?> </td>
		</tr>
		<tr>
			<td style="padding-left: 50px; padding-right: 50px; text-align: center;">Chairperson, BAC Sub-Committee</td>
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

	<div style ="padding-top: 100px">
		Approved by:
		<br> <br><br><br>
		<strong>ANNABELLE V. BRIONES, PhD</strong><br>
		<div span style=" font-size: meduim; padding-left: 70px">Director </div>

	</div>



</div>