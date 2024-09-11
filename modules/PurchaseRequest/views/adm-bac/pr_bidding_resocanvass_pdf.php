<?php

use app\models\User;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\PrItems;
use yii\helpers\ArrayHelper;


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

	$parts = explode('.', $num);
	$wholePart = (int)$parts[0];
	$fractionalPart = isset($parts[1]) ? (int)str_pad($parts[1], 2, '0', STR_PAD_RIGHT) : 0;

	$words = [];

	$list1 = array(
		'', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
		'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
	);
	$list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety');
	$list3 = array(
		'', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
		'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
		'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
	);

	$num_length = strlen($wholePart);
	$levels = (int) (($num_length + 2) / 3);
	$max_length = $levels * 3;
	$wholePart = substr('00' . $wholePart, -$max_length);
	$num_levels = str_split($wholePart, 3);
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
	}

	if ($fractionalPart > 0) {
		$fractionalWords = [];
		if ($fractionalPart < 20) {
			$fractionalWords[] = $list1[$fractionalPart];
		} else {
			$tens = (int)($fractionalPart / 10);
			$units = $fractionalPart % 10;
			$fractionalWords[] = $list2[$tens];
			if ($units > 0) {
				$fractionalWords[] = $list1[$units];
			}
		}
		$fractionalWords[] = 'centavos';
	}

	$commas = count($words);
	if ($commas > 1) {
		$commas = $commas - 1;
	}

	$wholeWords = implode(' ', $words);
	if ($fractionalPart > 0) {
		$wholeWords .= ' and ' . implode(' ', $fractionalWords);
	}

	return trim($wholeWords);
}
?>


<div class="resolution">

	<div style="text-align:center">
		<strong>BIDS AND AWARDS COMMITTEE </strong>
		<br> RESOLUTION NO.: <?= $resolution->resolution_no == NULL ? '' : $resolution->resolution_no ?>
		<br><?= $resolution->resolution_date == NULL ? '-' : Yii::$app->formatter->asDatetime(strtotime($resolution->resolution_date), 'php: F d, Y')  ?>
	</div>

	<br>
	<div style="text-align:justify; font-size:medium;">
		The subject of this resolution deals with the request for
		<?php
		foreach ($allBiddinglist as $bid) {
			$prItems = PrItems::find()->where(['id' => $bid->item_id])->all();

			foreach ($prItems as $prItem) {
				echo
				'<strong>' . ucwords(convertNumberToWord($prItem->quantity)) . '(' . $prItem->quantity . ') ' . $prItem->unit . ' ' . $prItem->item_name . ', </strong>';
			}
		}
		?>
		under Solicitation No.: <strong> <?= $model->quotationdisplay->quotation_no ?> </strong> and Purchase Request No.:<strong> <?= $model->purchaserequest->pr_no  ?>;</strong> <br> <br>

		<div>
			<?php if ($prDetails->mode_pr_id == 5) : ?>

			<?php endif; ?>

			<?php if ($prDetails->mode_pr_id != 5) : ?>
				WHEREAS, Section 53 of the 2016 Revised Implementing Rules and Regulations (IRR) of Republic Act No. 9184, otherwise known as the Government Procurement Reform Act, allows an agency to resort to negotiated procurement; <br> <br>

				WHEREAS, further, Section 53.9 of the Revised IRR of RA 9184, allows an agency to resort to Small Value Procurement for the procurement of goods, infrastructure projects and consulting services, where the amount involved does not exceed the threshold prescribed in Annex “H” of this IRR in the amount of One Million Pesos (P1,000,000.00) for NGAs, GOCCs, GFIs, SUCs and Autonomous Regional Government; <br> <br>

			<?php endif; ?>
		</div>

		WHEREAS, the end-user invited at least three (3) possible suppliers for the above-mentioned request through canvassing; <br> <br>

		WHEREAS, on the bid evaluation conducted on <?= Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:F d, Y')  ?> ,
		<?php
		$totalSupplierCount = 0;
		foreach ($allBiddinglist as $bid) {
			$prItems = PrItems::find()->where(['id' => $bid->item_id])->all();

			foreach ($prItems as $prItem) {
				$bidding = BiddingList::find()->where(['item_id' => $prItem->id])->all();
				$supplierIds = ArrayHelper::getColumn($bidding, 'supplier_id');
				$countSupplier = count(array_unique($supplierIds));
				$totalSupplierCount += $countSupplier;
			}
		}
		?>
		<?= strtolower(convertNumberToWord($totalSupplierCount)) ?> (<?= $totalSupplierCount ?>) supplier/s who submitted their quotations were considered and evaluated; <br> <br>

		WHEREAS, the said request is needed <span style="text-transform: lowercase;"> <?= $model->purchaserequest->purpose ?> </span> chargeable against <?= $model->purchaserequest->charge_to == NULL ? 'GAA' : $model->purchaserequest->chargedisplay->project_title ?>; <br><br>

		<div>
			<?php if ($prDetails->mode_pr_id == 5) : ?>
				WHEREAS, after the evaluation of the Bids and Awards Committee and/or the Technical Working Group (TWG), the bid of <strong> <?= $model->supplierdisplay->supplier_name ?> </strong> conforms to the required specifications of the end-user; <br><br>
			<?php endif; ?>

			<?php if ($prDetails->mode_pr_id != 5) : ?>
				WHEREAS, after the evaluation conducted by the end-user and Bids and Awards Committee, the bid of <strong> <?= $model->supplierdisplay->supplier_name ?> </strong> conforms to the required technical specifications of the end-user; <br><br>

			<?php endif; ?>
		</div>

		<div>
			<?php if ($prDetails->mode_pr_id == 5) : ?>

				WHEREFORE, foregoing premises considered, we, the Bids and Awards Committee, hereby recommend that the above-mentioned request be awarded to <strong> <?= $model->supplierdisplay->supplier_name ?> </strong> through <?= $prDetails->procurementmode->mode_name ?> under Alternative Methods of Procurement pursuant to Sec. 52.1(b) of the 2016 Revised IRR of RA 9184 in the total amount of
				<?php
				$total = 0;
				foreach ($allBiddinglist as $bid) {

					$prItems = PrItems::find()->where(['id' => $bid->item_id])->all();

					foreach ($prItems as $prItem) {
						$total_amount = $prItem->quantity * $bid->supplier_price;
						$total += $total_amount;
					}
				}
				?>
				<strong><?= ucwords(convertNumberToWord($total)) ?> Pesos (Php <?= number_format($total, 2) ?>) </strong> only.
			<?php endif; ?>

			<?php if ($prDetails->mode_pr_id != 5) : ?>

				WHEREFORE, foregoing premises considered, we, the Bids and Awards Committee, hereby recommend that the above-mentioned request be awarded to <strong> <?= $model->supplierdisplay->supplier_name ?> </strong> through <?= $prDetails->procurementmode->mode_name ?> under Negotiated Procurement pursuant to Sec. 53.9 of the 2016 Revised IRR of RA 9184 in the total amount of
				<?php
				$total = 0;
				foreach ($allBiddinglist as $bid) {

					$prItems = PrItems::find()->where(['id' => $bid->item_id])->all();

					foreach ($prItems as $prItem) {
						$total_amount = $prItem->quantity * $bid->supplier_price;
						$total += $total_amount;
					}
				}
				?>
				<strong><?= ucwords(convertNumberToWord($total)) ?> (Php <?= number_format($total, 2) ?>) </strong> only.
			<?php endif; ?>
		</div>
	</div>

	<div>
		<?php if ($prDetails->mode_pr_id == 5) : ?>
			<div style="padding-top: 60%;">
				<br>
				RESOLVED, at the <i><u>BAC Office, Metrology Building, ITDI, </u></i>this ________ day of ________</> 2024.
			</div>

			<div style="text-align: center;">
				<br> <br><br><br>
				DR. JANET F. QUIZON <br>
				<span style=" font-size: meduim">Chairperson, BAC </span>
			</div>

			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center; padding-top: 5%"> ERIC M. CHARLON </td>
					<td style="width:50%; text-align: center;  padding-top: 5%"> MARGARITA V. ATIENZA </td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Chairperson, BAC Sub-Committee</td>
					<td style="width:50%; text-align: center;"> Co-Chairperson</td>
				</tr>
			</table>
			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center;  padding-top: 5%"> RODA MAE O. URMENETA </td>
					<td style="width:50%; text-align: center; padding-top: 5%"> ABIGAIL GRACE H. BION</td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Members</td>
					<td style="width:50%; text-align: center;"> Members</td>
				</tr>
			</table>
			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center;  padding-top: 5%"> MONICA R. MANALO</td>
					<td style="width:50%; text-align: center;  padding-top: 5%"> SANIELOU V. JARDIN </td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Members</td>
					<td style="width:50%; text-align: center;"> Members</td>
				</tr>
			</table>
			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center; padding-top: 5%"> JENNY LYN H. LAGA</td>
					<td style="width:50%; text-align: center; padding-top: 5%"> </td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Members</td>
					<td style="width:50%; text-align: center;"></td>
				</tr>
			</table>

			<div style="padding-top: 70px">
				Approved by:
				<br> <br><br><br>
				<strong>ANNABELLE V. BRIONES, PhD</strong><br>
				<div span style=" font-size: meduim; padding-left: 70px">Director </div>

			</div>
		<?php endif; ?>

		<?php if ($prDetails->mode_pr_id != 5) : ?>
			<div style="padding-top: 20%;">
				<br>
				RESOLVED, at the <i><u>BAC Office, Metrology Building, ITDI, </u></i>this ________ day of ________</> 2024.
			</div>

			<div style="text-align: center;">
				<br> <br><br><br>
				DR. JANET F. QUIZON <br>
				<span style=" font-size: meduim">Chairperson, BAC </span>
			</div>

			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center; padding-top: 5%"> VALERIE FAY R. ABLANG</td>
					<td style="width:50%; text-align: center;  padding-top: 5%"> MARGARITA V. ATIENZA </td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Chairperson, BAC Sub-Committee</td>
					<td style="width:50%; text-align: center;"> Co-Chairperson</td>
				</tr>
			</table>
			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center;  padding-top: 5%"> RODA MAE O. URMENETA </td>
					<td style="width:50%; text-align: center; padding-top: 5%"> ABIGAIL GRACE H. BION</td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Members</td>
					<td style="width:50%; text-align: center;"> Members</td>
				</tr>
			</table>
			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center;  padding-top: 5%"> MONICA R. MANALO</td>
					<td style="width:50%; text-align: center;  padding-top: 5%"> SANIELOU V. JARDIN </td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Members</td>
					<td style="width:50%; text-align: center;"> Members</td>
				</tr>
			</table>
			<table class="signatories" style="width: 100%;">
				<tr>
					<td style="width:50%; text-align: center; padding-top: 5%"> JENNY LYN H. LAGA</td>
					<td style="width:50%; text-align: center; padding-top: 5%"> </td>
				</tr>
				<tr>
					<td style="width:50%; text-align: center;">Members</td>
					<td style="width:50%; text-align: center;"></td>
				</tr>
			</table>

			<div style="padding-top: 70px">
				Approved by:
				<br> <br><br><br>
				<strong>ANNABELLE V. BRIONES, PhD</strong><br>
				<div span style=" font-size: meduim; padding-left: 70px">Director </div>

			</div>
		<?php endif; ?>
	</div>




</div>