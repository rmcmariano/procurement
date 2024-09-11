<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

use app\models\profile\Profile;
use app\models\lab\Section;

$this->title = 'Document Records - Recipients';
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Document Details', 'url' => ['view' , 'tracking_number' => $response_details->tracking_number, 'type' => $_GET['type']]];
$this->params['breadcrumbs'][] = $this->title;

?>

<?php Pjax::begin(); ?>
<div class="box box-primary">
	<div class="box-body">
		<div style="padding: 10px">

		<table class="table table-responsive">
				<tr>
		            <td style="width: 8%; text-align: right">Tracking No:</td>
		            <td><span style="font-size: 15px; font-weight: bold"><?= $response_details->tracking_number?></span></td>
          		</tr>
          		<tr>
		            <td style="width: 8%; text-align: right">Recipient/s:</td>
          		</tr>
		</table>	

			<?php
			echo GridView::widget([
				'dataProvider' => $dataProvider,
				'columns' => [
					'profile.fullName',

					[

                            'header' => 'Status',
                            'headerOptions' => ['style' => 'color:#337ab7'],
                            'value' => 'statusDescription'
                    ],
				]
			]);

			
			?>
		</div>
	</div>
</div>
<?php Pjax::end(); ?>