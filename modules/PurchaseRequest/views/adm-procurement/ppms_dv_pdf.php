<?php

use app\modules\PurchaseRequest\models\AdditionalServices;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\LessDeductions;
use app\modules\PurchaseRequest\models\PrItems;

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


<div class="purchase-order">

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 15%; font-size: small;" img style=" width: auto; height: auto" src="' . Yii::$app->request->baseUrl . '/logo.png"></td>
			<td style="width: 65%; border-right: 1px solid; font-size: small; text-align: center"><strong>INDUSTRIAL TECHNOLOGY DEVELOPMENT INSTITUTE
				</strong></td>
			<td style="width: 20%; font-size: small;"> Fund Cluster:</td>
		</tr>
		<tr>
			<td style="width: 15%; font-size: small;"></td>
			<td style="width: 65%; border-right: 1px solid;  font-size: small; text-align: center">Entity Name</td>
			<td style="width: 20%;  font-size: small; border-bottom: 1px solid; "> </td>
		</tr>
		<tr>
			<td style="width: 15%; font-size: small;"></td>
			<td style="width: 65%; border-right: 1px solid; font-size: small; text-align: center"></td>
			<td style="width: 20%; font-size: small;"> Date: <?= Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->date_ors_burs), 'php:M d, Y') ?></td>
		</tr>
		<tr>
			<td style="width: 15%; font-size: small;"></td>
			<td style="width: 65%; border-right: 1px solid;  font-size: large;  text-align: center"><strong>DISBURSEMENT VOUCHER</strong></td>
			<td style="width: 20%;  font-size: small"> DV No. :
			</td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 15%; border-right: 1px solid; font-size: small; padding-top: 1% "> Mode of Payment</td>
			<td style="width: 45%; font-size: small; padding-top: 1%  "></td>
			<td style="width: 20%; font-size: small; padding-top: 1%  "></td>
			<td style="width: 20%; font-size: small; padding-top: 1%  "></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 15%; border-right: 1px solid; font-size: small; padding-top: 1% "> Payee</td>
			<td style="width: 45%; border-right: 1px solid; font-size: small; padding-top: 1%  "><strong> <?= $bidding->supplierdisplay->supplier_name ?> </strong></td>
			<td style="width: 20%; border-right: 1px solid; font-size: small; padding-top: 1%  ">TIN/Employee No.: <br> <?= $bidding->supplierdisplay->tin_no ?> </td>
			<td style="width: 20%; border-right: 1px solid; font-size: small; padding-top: 1%  ">ORS/BURS No.: <br> <?= $purchaseOrder->ors_burs_num ?></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif; width: 100%">
		<tr>
			<td style="width: 15%; border-right: 1px solid; font-size: small; padding-top: 1% "> Address</td>
			<td style="width: 45%; font-size: small; padding-top: 1%  "> <?= $bidding->supplierdisplay->supplier_address ?></td>
			<td style="width: 20%; font-size: small; padding-top: 1%  "></td>
			<td style="width: 20%; font-size: small; padding-top: 1%  "></td>
		</tr>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 100%; font-size: small ">
		<tr>
			<td style="width: 60%; border: 1px solid; font-size: small;padding-top: 1%; text-align:center; "> Particulars</td>
			<td style="width: 10%; border: 1px solid;  font-size: small;padding-top: 1%; text-align:center;">Responsibility Center</td>
			<td style="width: 10%; border: 1px solid; font-size: small;padding-top: 1%; text-align:center;">MFO/PAP </td>
			<td style="width: 20%; border: 1px solid; font-size: small;padding-top: 1%; text-align:center;">Amount </td>
		</tr>

		<tr>
			<td style="width:60%; border-right: 1px solid;  font-size: small;padding-top: 1%"> For the payment of: </td>
			<td style="width: 10%; border-right: 1px solid;  font-size: small;padding-top: 1%; text-align:left; padding-left: 1%; "></td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
			<td style="width: 20%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
		</tr>

		<?php
		$amount = 0;
		foreach ($biddingLists as $biddingList) {

			$testDescription = PrItems::find()->where(['id' => $biddingList->item_id])->one();
			$testDeductions = LessDeductions::find()->where(['po_id' => $biddingList->po_id])->all();

			$itemPrice = $testDescription->quantity * $biddingList->supplier_price;
			$amount += $itemPrice;

			echo
			'<tr>
				<td style="width:60%; border-right: 1px solid;  font-size: small;padding-top: 1%"> &nbsp;&nbsp;&nbsp;&nbsp; Approved Amount: </td>
				<td style="width: 10%; border-right: 1px solid;  font-size: small;padding-top: 1%; padding-left: 1%; text-align:left">' . '</td>
				<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
				<td style="width: 20%; border-right: 1px solid; font-size: small;padding-top: 1%;  text-align:right">' .  number_format($testDescription['quantity'] * $biddingList['supplier_price'], 2) . '</td>
			</tr>';
		}
		?>

		<?php
		$amount = 0;
		foreach ($biddingLists as $ordItem) {
			// $testBidding = BiddingList::find()->where(['id' => $ordItem->bid_id])->one();
			$testDescription = PrItems::find()->where(['id' => $ordItem->item_id])->one();

			echo
			'<tr>
					<td style="width: 60%; border-right: 1px solid;  font-size: small; padding-top: 1%"> &nbsp;&nbsp;&nbsp;&nbsp; PO/WO No.: <strong> ' . $purchaseOrder->po_no . '</strong> &nbsp;&nbsp;&nbsp;&nbsp; Dated: <strong> ' . Yii::$app->formatter->asDatetime(strtotime($purchaseOrder->date_ors_burs), 'php:d-M-Y') . '</strong></td>
					<td style="width: 10%; border-right: 1px solid;  font-size: small; text-align:left"></td>
					<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
					<td style="width: 20%; border-right: 1px solid; font-size: small;"></td>
			</tr>';
		}
		?>

		<tr>
			<td style="width: 60%; border-right: 1px solid;  font-size: small;"> &nbsp;&nbsp;&nbsp;&nbsp; as per Inv. No.: <?= $iarModel->sales_invoice_number ?> &nbsp;&nbsp;&nbsp;&nbsp; Dated: &nbsp;&nbsp;&nbsp;&nbsp; <?= $iarModel->sales_invoice_date ?>  </td>
			<td style="width: 10%; border-right: 1px solid;  font-size: small;text-align:left"> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
			<td style="width: 20%; border-right: 1px solid; font-size: small;"></td>
		</tr>


		<?php
		$amount = 0;
		foreach ($biddingLists as $ordItem) {
			// $testBidding = BiddingList::find()->where(['id' => $ordItem->bid_id])->one();
			$testDescription = PrItems::find()->where(['id' => $ordItem->item_id])->one();
			$lessVat = LessDeductions::find()->where(['po_id' => $ordItem->po_id])->andWhere(['deduction_id' => '1'])->one();
			$lessEwt = LessDeductions::find()->where(['po_id' => $ordItem->po_id])->andWhere(['deduction_id' => '2'])->one();
			$addCharges = AdditionalServices::find()->where(['po_id' => $ordItem->po_id])->all();

			$itemPrice = $testDescription->quantity * $ordItem->supplier_price;

			$amount += $itemPrice;


			echo
			'<tr>
				<td style="width: 60%; border-right: 1px solid;  font-size: small;padding-top: 1%">&nbsp;&nbsp;&nbsp;&nbsp; <i>Less:</i> &nbsp;&nbsp;&nbsp;&nbsp; VAT(5%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <i>Php</i> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .  (($lessVat) ? number_format($lessVat->deduction_amount, 2) : '') . '</td>
				<td style="width: 10%; border-right: 1px solid;  font-size: small;padding-top: 1%; text-align:left"></td>
				<td style="width: 10%; border-right: 1px solid; font-size: small;padding-top: 1%"></td>
				<td style="width: 20%; border-right: 1px solid; "></td>
			</tr>';
			
		}
		?>

		 <?php
		$amount = 0;

		$totalDeduct = 0;
		$totalAmount = 0;

		$lessEwt = LessDeductions::find()->where(['po_id' => $ordItem->po_id])->andWhere(['deduction_id' => '2'])->one();
		foreach ($lessDeduction as $lessDeductions) {
			
			$total = $lessDeductions->deduction_amount;
			$totalDeduct += $total;
			$totalAmount = $amount - $total;


			echo
			'<tr>
				<td style="width: 60%; border-right: 1px solid;  font-size: small; ">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; EWT(1%) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.  (($lessEwt) ? number_format($lessEwt->deduction_amount, 2) : ''). ' </td>
				<td style="width: 10%; border-right: 1px solid;  font-size: small;"></td>
				<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
				<td style="width: 20%; border-right: 1px solid; font-size: small; text-align:right">' .  number_format($totalDeduct, 2)	. '</td>
			</tr>';
		}
		?>

		<?php
		$amount = 0;

		$totalDeduct = 0;
		$totalAmount = 0;

		foreach ($lessDeduction as $lessDeductions) {

			$total = $lessDeductions->deduction_amount;
			$totalDeduct += $total;
			$amount += $itemPrice;
			$totalAmount = $amount - $total;


			echo
			'<tr>
				<td style="width: 60%; border-right: 1px solid;  font-size: small; "> </td>
				<td style="width: 10%; border-right: 1px solid;  font-size: small;"></td>
				<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
				<td style="width: 20%; border-right: 1px solid; border-top: 1px solid; font-size: small; text-align:right;">' .  number_format($totalAmount, 2)	. '</td>
			</tr>';
		}
		?>

		<tr>
			<td style="width: 60%; border-right: 1px solid;  font-size: small; padding-top: 3%"> &nbsp;&nbsp;&nbsp;&nbsp; <i> <?= $descriptiontest->bid_title ?> </i></td>
			<td style="width: 10%; border-right: 1px solid;  font-size: small;text-align:left"> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
			<td style="width: 20%; border-right: 1px solid; font-size: small;"></td>
		</tr>

		<tr>
			<td style="width: 60%; border-right: 1px solid;  font-size: small; padding-top: 3%"> &nbsp;&nbsp;&nbsp;&nbsp; <strong> Charge to: </strong> <i> <?= $purchaserequest->chargedisplay->project_title ?> </i></td>
			<td style="width: 10%; border-right: 1px solid;  font-size: small;text-align:left"> </td>
			<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
			<td style="width: 20%; border-right: 1px solid; font-size: small;"></td>
		</tr>

		<?php
		$amount = 0;

		$totalDeduct = 0;
		$totalAmount = 0;

		foreach ($lessDeduction as $lessDeductions) {

			$total = $lessDeductions->deduction_amount;
			$totalDeduct += $total;
			$amount += $itemPrice;
			$totalAmount = $amount - $total;


			echo
			'<tr>
				<td style="width: 60%; border-right: 1px solid;  font-size: small; text-align: center; "><strong>Amount Due</strong> </td>
				<td style="width: 10%; border-right: 1px solid;  font-size: small;"></td>
				<td style="width: 10%; border-right: 1px solid; font-size: small;"></td>
				<td style="width: 20%; border-right: 1px solid; border-top: 1px solid; font-size: small; text-align:right;">Php ' .  number_format($totalAmount, 2)	. '</td>
			</tr>';
		}
		?>
	</table>

	<table style="border-collapse: collapse; border: 1px solid;  font-family: Arial, Helvetica, sans-serif;width: 100%; text-align:center; font-size: small ">
		<tr>
			<td valign="top" style="width:5%; border: 1px solid;  font-size: small;">A.</td>
			<td colspan="7" style="border-right: 1px solid; border-top: 1px solid; font-size: small; text-align:left;"> Certified: Expenses/Cash Advance necessary, lawful and incurred under my direct supervision.
			</td>
		</tr>
		<tr>
			<td style="font-size: small; padding-top: 2%"></td>
			<td colspan="7" style="font-size: small; text-align:center; padding-top: 2%">
			</td>
		</tr>
		<tr>
			<td valign="top" style="width:5%; font-size: small;"></td>
			<td colspan="7" style="font-size: small; text-align:center;"><u><strong>RHEA MAE C. MARIANO</strong></u>
			</td>
		</tr>
		<tr>
			<td valign="top" style="width:5%; font-size: small;"></td>
			<td colspan="7" style="font-size: small; text-align:center;">Supervising SRS/ Project Leader
			</td>
		</tr>
		<tr>
			<td valign="top" style="width:5%; font-size: small;"></td>
			<td colspan="7" style="font-size: small; text-align:center;"> Printed Name, Designation and Signature of Supervisor

			</td>
		</tr>
		<tr>
			<td style="width:5%; border: 1px solid;  font-size: small;padding-top: 1%">B.</td>
			<td colspan="7" style="border-right: 1px solid; border-top: 1px solid; font-size: small; text-align:left;">Accounting Entry:</td>
		</tr>
		<tr>
			<td colspan="4" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> Account Title</strong> </td>
			<td style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> UACS Code </strong> </td>
			<td style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> Debit </strong> </td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> Credit </strong> </td>
		</tr>
		<tr>
			<td colspan="4" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center; padding: 5%"><strong></strong> </td>
			<td style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
		</tr>
		<tr>
			<td colspan="4" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"><strong> C. Certified:</strong> </td>
			<td colspan="4" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"><strong> D. Approved Payment </strong> </td>
		</tr>
		<tr>
			<td colspan="4" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left; padding: 5%"><strong> </strong> </td>
			<td colspan="4" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"><strong> </strong> </td>
		</tr>
		<tr>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Signature:</td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Signature: </td>
			<td colspan="3" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"> </td>
		</tr>
		<tr>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Printed Name:</td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Printed Name: </td>
			<td colspan="3" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"> </td>
		</tr>
		<tr>
			<td rowspan="2" colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Position:</td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;">Admin Officer IV </td>
			<td rowspan="2" colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Position: </td>
			<td colspan="3" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"> Director </td>
		</tr>

		<tr>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: x-small; text-align:center;"> Head, Accounting Unit/Authorized Representative
			</td>
			<td colspan="3" style="border-right: 1px solid; border: 1px solid; font-size: x-small; text-align:center;"> Agency Head/Authorized Representative
			</td>
		</tr>
		<tr>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Date :</td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Date :</td>
			<td colspan="3" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"> </td>
		</tr>
		<tr>
			<td colspan="6" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"><strong> E. Receipt of Payment </strong> </td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">JEV No.</td>
		</tr>
		<tr>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Check/ADA No.:</td>
			<td colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Date:</td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Bank Name & Account Number :</td>
			<td colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"> </td>
		</tr>
		<tr>
			<td valign = "top" colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Signature:</td>
			<td valign = "top" colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:center;"><strong> </strong> </td>
			<td valign = "top" colspan="1" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Date:</td>
			<td valign = "top" colspan="2" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;">Printed Name:</td>
			<td valign = "top" colspan="2" style="border-right: 1px solid;font-size: small; text-align:left;">Date</td>
		</tr>
		<tr>
			<td colspan="6" style="border-right: 1px solid; border: 1px solid; font-size: small; text-align:left;"> Official Receipt No. & Date/Other Documents </td>
			<td colspan="2" style="border-right: 1px solid; font-size: small; text-align:left;"></td>
		</tr>



	</table>






</div>