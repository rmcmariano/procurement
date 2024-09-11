<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use yii\bootstrap\Modal;

use app\modules\document_tracking\models\ResponseTable;
use app\models\profile\Profile;
// use mdm\admin\models\User;

$this->title = 'Document Details';
if ($_GET['type'] == 'IC'){
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index?type=IC']];
}
if ($_GET['type'] == 'I'){
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index?type=I']];
}
if ($_GET['type'] == 'O'){
$this->params['breadcrumbs'][] = ['label' => 'Documents', 'url' => ['index?type=O']];
}
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="box box-primary">

  <div class="box-body">
    <hr style="margin-top: -5px !important; border-bottom: 2px solid #fbc02d;"/>
    <div style="padding: 10px">
      <div class="calibration-view row">
        <table class="table table-responsive">
          <tr>
            <td style="width: 25%; text-align: right">Tracking No:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->tracking_number?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Title:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->title?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Created By:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $full?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Details:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->details?></span></td>
          </tr>
          <tr>
            <td style="width: 25%; text-align: right">Date Created:</td>
            <td><span style="font-size: 15px; font-weight: bold"><?= $document_details->date_created ?></span></td>
          </tr>
        </table>
      </div>
    </div>
    <hr style="margin-top: -5px !important; border-bottom: 2px solid #fbc02d;"/>
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

        <?= $form->field($close, 'tracking_number')->hiddenInput(['value' => $document_details->tracking_number])->label(false) ?>

        <?php if ($document_details->is_closed != 1 && $document_details->created_by == Yii::$app->user->identity->id) : ?>
          <center style="margin-top: -10px;margin-bottom: -10px; float: left; padding-left: 1px;"><?= Html::submitButton('Close Document', 
                    [
                        'class' => 'btn btn-primary',
                        'data' => [
                            'confirm' => 'Click Yes to confirm your action.',
                            'method' => 'post',
                        ]
                    ])?>
        </center>
        <?php endif; ?>
    <?php $form = ActiveForm::end(); ?>
  </div>


  <div class="box-header with-border">
    <?= GridView::widget([
      'dataProvider' => $dataProvider,
      'columns' => [
          'date',
          [
            'attribute' => 'Action By',
            'value' => function ($url, $index){
                $name = Profile::find()->where(['user_id' => $url->action_by])->one();
                return $name->fname . ' ' . $name->mi . ' ' . $name->lname;
            }
          ],
          'action',
          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Recipient/s',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'template' => '{view}',
            'buttons' =>  [
            'view'   => function ($url, $tracking) {
                  $url = Url::to(['recipients', 'id' => $tracking->id, 'type' => $_GET['type']]);
                  $action = $tracking->action;

                if ($action == 'CREATED'){

                  return '-';
                    
                }

                if ($action == 'RELEASED'){
                  return Html::a('<span class="fa fa-eye"></span>', $url, ['title' => 'recipients']);
                  // return Html::a('<span class="fa fa-eye"></span>', $url, ['title' => 'recipients']);
                    
                }

                if ($action == 'RECEIVED'){

                  return '-';
                    
                }

                if ($action == 'REDIRECT'){

                  $recipient = ResponseTable::find()
                                ->where(['release_id' => $tracking->id])
                                ->one();

                  $name = Profile::find()
                            ->where(['user_id' => $recipient['recipient_id']])
                            ->one();
                  return $name->fname . ' ' . $name->mi . ' ' . $name->lname; 
                    
                }

                if ($action == 'CLOSED'){

                  return '-';
                    
                }
              }
            ]  
          ],
          [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Download/s',
            'headerOptions' => ['style' => 'color:#337ab7'],
            'template' => '{view}',
            'buttons' =>  [
            'view'   => function ($url, $tracking) {
                  $url = Url::to(['download-list', 'id' => $tracking->id]);
                  $action = $tracking->action;

                if ($action == 'CREATED'){

                  return '-';
                    
                }

                if ($action == 'RELEASED'){
                  return Html::button('<span class="fa fa-eye"></span>', ['class' => 'btn btn-link view-downloadlist-modal', 'data-key' => $tracking->id]);
                  // return Html::a('<span class="fa fa-eye"></span>', $url, ['title' => 'recipients']);
                    
                }

                if ($action == 'RECEIVED'){

                  return '-';
                    
                }

                if ($action == 'REDIRECT'){

                  return '-';
                    
                }

                if ($action == 'CLOSED'){

                  return '-';
                    
                }
              }
            ]  
          ],
        'remarks',
      ],
    ])?>
  </div>
</div>

<?php

Modal::begin([
    'header' => '<h2>Download List</h2>',
    'options' => [
      'id' => 'downloadlist-modal',
    ],
    'size' => Modal::SIZE_LARGE,
]);

Modal::end();

$this->registerJs(<<<JS
  $('.view-downloadlist-modal').on('click', function() {
    var elementId = $(this).data('key');
    $.get({
      url: 'downloadlist',
      data: {
        'id': elementId
      },
      success: function (data) {
        $('.modal-body').html(data);
        $('#downloadlist-modal').modal('show');
      }
    });
  });
JS,
View::POS_READY);


Modal::begin([
    'header' => '<h2>Recipients</h2>',
    'options' => [
      'id' => 'recipients-modal',
    ],
    'size' => Modal::SIZE_LARGE,
]);

// echo 'Say hello...';

Modal::end();

$this->registerJs(<<<JS
  $('.view-recipients-modal').on('click', function() {
    var elementId = $(this).data('key');
    $.get({
      url: 'recipients',
      data: {
        'id': elementId
      },
      success: function (data) {
        $('.modal-body').html(data);
        $('#recipients-modal').modal('show');
      }
    });
  });
JS,
View::POS_READY);