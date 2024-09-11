<?php

use app\modules\PurchaseRequest\models\ItemSpecification;

?>

<div class="rfq" style="font-size: 7pt;">
	<div style="text-align:center">
		<img style=" width: auto; height: auto" src="rfq_header.png" />
	</div>

	<div class="rfqtop" style="font-size: 7pt;">
		<br>
		<table style="width: 100%; font-size: xx-small;">
			<tr>
				<td class="left-rfq"><i>Purchase Request No.: </i> <span style="font-size:small"> <strong> <?= $quotation->purchaseRequest->pr_no ?> </strong></span> </td>
				<td class="right-rfq">Solicitation No.: <span style="font-size:small"> <strong> <?= $quotation->quotation_no ?> </strong></span></td>
			<tr>
				<td class="left-rfq"></td>
				<td class="right-rfq">End-user: <?= $purchaserequest->enduser ?> </td>
			</tr>
		</table>
	</div>

	<div class="leftstyle">
		<div style="text-align: justify">
			<p>
				<span style="font-size: 7pt">
					Sir/Madam: <br>
					Please quote your lowest price on the item/s listed below and submit your detailed quotation <strong> duly signed by the bidder or bidder's authorized representative </strong> not later than <span style="font-size:small"><strong><u> <?= ($quotation->option_date == NULL ? ' ' : Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:F d, Y'))  ?> </u> </strong> </span> at <span style="font-size:small"> <strong><u> <?= ($quotation->option_date == NULL ? ' ' : Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:h:i A')) ?>.</u></strong> </span>Failure to coomply with the deadline and conditions indicated in the posting shall automatically disqualify the bidder from the bidding process. Thank you.</span>
			</p>
		</div>
	</div>
	<br><br>
	<div class="spanstyle" style="font-size: 7pt;">Very truly yours,
		<br><br><br>
		<strong> ROCHEEL LEE C. DELUTA </strong> <br>
		Administrative Officer V
	</div>
	<hr>

	<div style=" text-align:center"><strong>P R O P O S A L</strong></div>

	<div style="text-align:justify; font-size: 7pt">
		Subject to the conditions and specifications herein set forth, I/We offer our best government price for the following item/s. I/We further agree and bind myself/ ourselves to comply with the existing terms and conditions prescribed by the ITDI and do hereby certify that the articles listed below are available in our establishment.
		<p>
	</div>

	<table class="items ">
		<tr>
			<th colspan="5" style="font-size: xx-small; border-bottom: 1px solid">ITDI REQUIREMENT</th>
			<th colspan="5" style="font-size: xx-small; border-bottom: 1px solid">BIDDER'S SPECIFICATIONS OFFER</th>
		</tr>
		<tr>
			<th style="width: 3%; font-size: xx-small">ITEM NO.</th>
			<th style="width: 5%; font-size: xx-small">QTY</th>
			<th style="width: 5%; font-size: xx-small">UOM</th>
			<th style="width: 30%; font-size: xx-small">DESCRIPTION</th>
			<th style="width: 5%; font-size: xx-small">ABC</th>
			<th style="width: 5%; font-size: xx-small">QTY</th>
			<th style="width: 5%; font-size: xx-small">UOM</th>
			<th style="width: 30%; font-size: xx-small">DESCRIPTION</th>
			<th style="width: 5%; font-size: xx-small">UNIT COST</th>
			<th style="width: 7%; font-size: xx-small">TOTAL COST</th>
		</tr>


		<!-- <table class="rfq-items" style="font-family: Arial, Helvetica, sans-serif;"> -->
		<?php
		foreach ($descriptiontest as $items) {
			$itemSpecs = ItemSpecification::find()->where(['item_id' => $items['id']])->all();

			echo
			'<tr>
				<td style = "width: 3%; text-align: center; font-size: xx-small">' . $item_no++ . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . $items->quantity . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . $items->unit . '</td>
				<td style = "width: 30%; text-align: left; font-size: xx-small">' . nl2br($items->item_name) . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' .  $items->total_cost . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 30%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 7%; text-align: center; font-size: xx-small">' . '</td>
			</tr>';

			foreach ($itemSpecs as $itemSpec) {
				// var_dump($itemSpecs);die;
				echo
				'<tr>
				<td style = "width: 3%; text-align: center; font-size: xx-small">' .  '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . ($itemSpec == NULL ? '' : $itemSpec->quantity) . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 30%; text-align: left; font-size: xx-small">' . nl2br($itemSpec->description) . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 30%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 5%; text-align: center; font-size: xx-small">' . '</td>
				<td style = "width: 7%; text-align: center; font-size: xx-small">' . '</td>
			</tr>';
			}
		}
		?>
			<tr>
			<th style="width: 3%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 30%; font-size: xx-small; font-weight: normal; text-align: left;">Delivery Period: <?= $purchaserequest->delivery_period == NULL ? 'N/A' : $purchaserequest->delivery_period ?></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 30%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"> </th>
			<th style="width: 7%; font-size: xx-small"></th>
		</tr>
		<tr>
			<th style="width: 3%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 30%; font-size: xx-small; font-weight: normal; text-align: left;">Warranty: <?= $purchaserequest->warranty == NULL ? 'N/A' : $purchaserequest->warranty ?></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"></th>
			<th style="width: 30%; font-size: xx-small"></th>
			<th style="width: 5%; font-size: xx-small"> </th>
			<th style="width: 7%; font-size: xx-small"></th>
		</tr>
	</table>

	<div class="bottom-text">
		<span style="font-size: 6pt">
			Upon receipt of your order, we are disposed to make the delivery within __________________________ calendar days. <br><br>
			<strong>We are requesting all participating bidders / suppliers to submit together with their quotations the following information: <br>
				1) Account Name 2) Account Number 3) Name of Bank/ Branch (pref. LBP so as not to ineur bank charges which will be paid by the payee) 4) Income Tax Return 5) Business / Mayor's Permit 6) PhilGEPS Registration Certificate
			</strong>
		</span>
	</div>
	<br><br>
	<!-- 	
	<hr style="width:40%; text-align: left">

	<div class="canvasser"> -->
	<table style="width: 100%">
		<tr>
			<td style="width: 50%">
				<hr style="width: 60%; text-align: left;">
			</td>
			<td style="width: 50%">
				<hr style="width: 80%; text-align: center;">
			</td>
		</tr>
		<tr>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt"><strong>CANVASSER</strong></td>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt; text-align: center"><strong>COMPANY NAME</strong></td>
		</tr>
		<tr>
			<td style="width: 50%">
			</td>
			<td style="width: 50%; padding-top: 1%">
				<hr style="width: 80%; text-align: center;">
			</td>
		</tr>
		<tr>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt"></td>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt; text-align: center"><strong>ADDRESS, TELEPHONE AND CELLPHONE NUMBER, EMAIL ADDRESS</strong></td>
		</tr>
		<tr>
			<td style="width: 50%">
			</td>
			<td style="width: 50%; padding-top: 1%">
				<hr style="width: 80%; text-align: center;">
			</td>
		</tr>
		<tr>
			<td style="width: 50%; font-size: 6pt"></td>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt; text-align: center"><strong> NAME AND SIGNATURE OF THE BIDDER'S REPRESENTATIVE</strong></td>
		</tr>
		<tr>
			<td style="width: 50%">
			</td>
			<td style="width: 50%; padding-top: 2%">
				<hr style="width: 80%; text-align: center;">
			</td>
		</tr>
		<tr>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt"></td>
			<td style="width: 50%; padding-top: -1%; font-size: 6pt; text-align: center"><strong>DATE</strong></td>
		</tr>
	</table>

	<!-- <br><br>
	<hr style="width:55%; text-align: right">
	<table>
		<tr>
			<td class="canvasser-bottomright">NAME AND SIGNATURE OF AUTHORIZED REPRESENTATIVE</td>
		</tr>
	</table> -->
	<!-- </div> -->

</div>