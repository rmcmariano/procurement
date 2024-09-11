<?php

use mdm\admin\components\Helper;

$this->title = 'ITDI Portal';
?>

<div class="row featurette">
  <div class="col-md-7">
    <h2 class="featurette-heading">Welcome <?= Yii::$app->user->identity->profile->firstName ?>!</h2>
    <p class="lead">-</p>
  </div>
  <div class="col-md-5">
<!--    <img class="featurette-image img-responsive center-block" data-src="holder.js/500x500/auto" alt="Generic placeholder image">-->
  </div>
</div>
  
  <div class="featurette-divider"><b>ITDI Applications</b></div>

<div class="container marketing">

      
  <div class="row">
    <?php if (Helper::checkRoute('/admin/*')) { ?>
    <div class="col-xs-6 col-md-3 text-center">
      <h2>Developer</h2>
      <p>Management Information System</p>
      <p><a class="btn btn-default" href="/admin" target="_blank" role="button">View &raquo;</a></p>
    </div>

    <?php } ?>

    <?php if (Helper::checkRoute('/document_tracking/*')) { ?>
    <div class="col-xs-6 col-md-3 text-center">
      <img src="images/Document_Tracking.png" style="max-height: 50%; max-width: 50%;"/>
      <h2>DocTrack</h2>
      <p>Document Tracking System</p>
      <p><a class="btn btn-default" href="/document_tracking/documents/index?type=IC" target="_blank" role="button">View &raquo;</a></p>
    </div>
    <?php } ?>
  </div>
  
</div>

