<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">P</span><span class="logo-lg">' . Yii::$app->name . '</span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">

        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <div class="navbar-custom-menu">

            <ul class="nav navbar-nav">

                <li>
                    <?= Html::a('Logout ('.Yii::$app->user->identity->username.')', ['/site/logout'], $options = ['data-method' => 'post']) ?>
                </li>

                <li>
                    <a href="#" data-toggle="control-sidebar"><i class="fa fa-sitemap"></i></a>
                </li>
            </ul>
        </div>
    </nav>
</header>