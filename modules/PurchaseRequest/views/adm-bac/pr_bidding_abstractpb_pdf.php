<?php

use app\models\User;
use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\user\models\Profile;
use yii\helpers\ArrayHelper;


$assignatory = User::find()->where(['id' => $latest_assignatory->chairperson_id])->one();
$co_assignatory = User::find()->where(['id' => $latest_assignatory->co_chairperson_id])->one();
$listData = ArrayHelper::map($members, 'members_id', function ($model) {
	return $model['members_id'];
});
$member_assignatory = User::find()->where(['id' => $listData])->asArray()->all();


$count = BiddingList::find()->select(['supplier_id'])->where(['supplier_id' => $bidId])->distinct();

$count1 = $count->count();
?>


<div class="pb-abstract">
	<div style="text-align:left">
		QN: <strong> <?= $quot->quotation_no ?> </strong><br>
		PR:<strong> <?= $purchaserequest->pr_no ?> </strong> <br><br>

		<div style="margin-top: -40px; text-align:center ">
			<h2>ABSTRACT OF BIDS AS READ</h2>
		</div>

		<div style=" text-align:left; ">
			<table style="font-family:Arial, Helvetica, sans-serif">
				<tr>
					<td  style="font-size: large;">FUND</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px"><?= $purchaserequest->chargedisplay->project_title ?></td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
				<tr>
					<td  style="font-size: large; ">PROJECT</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px"> <?=  $items->itemexplode ?></td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
				<tr>
					<td  style="font-size: large;">LOCATION</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px">DOST Compound, Bicutan, Taguig</td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
				<tr>
					<td  style="font-size: large; ">DATE</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px"> <?= Yii::$app->formatter->asDatetime(strtotime($quot->option_date), 'php:M d, Y')  ?></td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
				<tr>
					<td  style="font-size: large;">TIME</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px"> <?= Yii::$app->formatter->asDatetime(strtotime($quot->option_date), 'H:m')  ?></td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
				<tr>
					<td  style="font-size: large;">PLACE</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px">Metrology Conference Room, Metrology Building, ITDI</td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
				<tr>
					<td  style="font-size: large;">APPROVED BUDGET COST</td>
					<td style="font-size: large; padding-left: 10%;">:</td>
					<td style="font-size: large; padding-left: 3%; padding-bottom: -20px"><?= number_format($items->total_cost,'2') ?> </td>
				</tr>
				<hr style="color: #000; margin-top: 5px; width: 100%">
			</table>
		</div>


	</div>
	<p>

	<table class="abstract-items" style="width:100%">
		<thead>
			<tr>
				<th>SUPPLIER</th>
				<th>BID BOND</th>
				<th>BID PRICE</th>
				<th>REMARKS</th>
			</tr>
		</thead>
		<tr>
			<?php

			foreach ($suppliers as $supplier) {
				$biddinglist = BiddingList::find()->where(['item_id' => $items->id])->andWhere(['supplier_id' => $supplier->id])->andWhere(['status' => ['13']])->orderBy(['id' => SORT_DESC])->one();

				echo '<tr>
						<td style ="width: 25%; text-align: center">' . $supplier->supplier_name . '</td>
						<td style ="width: 25%; text-align: center">' . $biddinglist->bid_bond . '</td>
						<td style ="width: 20%; text-align: center">' . number_format($biddinglist->supplier_price, 2) . '</td>
						<td style ="width: 25%; text-align: left">' . ''. '</td>';

				'</tr>';
			}
			?>
		</tr>
	</table>

	<div style="text-align: center; padding-top: 3%">
		<h4>ITDI BIDS AND AWARDS COMMITTEE</h4>
	</div>
	<table class="signatories">
		<tr>
			<td> <u> <?= $assignatory->profile->fname . '  ' . $assignatory->profile->lname ?> </u> </td>
			<td style="padding: 10px"> <u> <?= $co_assignatory->profile->fname . '  ' . $co_assignatory->profile->lname ?> </u></td>
		</tr>
		<?php
		foreach ($member_assignatory as $list) {
			$member_list = Profile::find()->where(['user_id' => $list])->one();

			echo '<td style = "padding: 10px"><u>' .  $member_list->fname . '  ' . $member_list->lname . '</u></td>' . '  ';
		}
		?>
		<tr>
			<td style="text-align: center"><i>Chairperson</i></td>
			<td style="text-align: center"><i>Co-Chairperson</i></td>
		</tr>

		<?php
		foreach ($member_assignatory as $list) {
			$member_list = User::find()->where(['id' => $list])->all();
			echo '<td style = "text-align: center"><i> Members </i></td>' . '  ';
		}
		?>
	</table>
</div>