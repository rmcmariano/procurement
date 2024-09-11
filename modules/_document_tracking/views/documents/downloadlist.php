<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Document Records - File List';
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Document Details', 'url' => ['view' , 'tracking_number' => $response_details->tracking_number]];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="box box-primary">
	<div class="box-body">
		<div style="padding: 10px">

		<table class="table table-responsive">
				<tr>
		            <td style="width: 8%; text-align: right">Tracking No:</td>
		            <td><span style="font-size: 15px; font-weight: bold"><?= $response_details->tracking_number?></span></td>
          		</tr>
          		<tr>
		            <td style="width: 25%; text-align: right">File List:</td>
		            <td><span style="font-size: 15px; font-weight: bold"></span></td>
          		</tr>

          		<?php foreach ($listData as $download) {

          		$file_name = explode(':', $download);

          		?>

          		<tr>
            		<td style="width: 25%; text-align: right"></td>
            		<td><span style="font-size: 15px; font-weight: bold"><?= $file_name[0] . ' ' . Html::a('Download File',['../document_tracking/documents/download', 'file_id' => $file_name[1]])?></span></td>
          		</tr>
          		<?php } ?>
		</table>		
		</div>
	</div>
</div>