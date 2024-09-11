<?php

use yii\helpers\Url;
use mdm\admin\components\Helper;

?>

<li>
    <a href="<?= Url::toRoute(['/land/index'], $schema = true) ?>">
        <i class="menu-icon  fa fa-home bg-blue"></i>
        <div class="menu-info">
            <h4 class="control-sidebar-subheading">Employee Portal</h4>
        </div>
    </a>
</li>

<?php if (Helper::checkRoute('/timetrack/*')) : ?>
<li>
    <a href="<?= Url::toRoute(['/timetrack/time'], $schema = true) ?>">
        <i class="menu-icon fa fa-clock-o bg-green"></i>
        <div class="menu-info">
            <h4 class="control-sidebar-subheading">DTR</h4>
        </div>
    </a>
</li>
<?php endif; ?>

<?php if (Helper::checkRoute('/admin/assignment/index')) : ?>
    <li>
        <a href="<?= Url::toRoute(['/admin/assignment/index'], $schema = true) ?>">
            <i class="menu-icon fa fa-user-secret bg-black"></i>
            <div class="menu-info">
                <h4 class="control-sidebar-subheading">Admin</h4>
            </div>
        </a>
    </li>
<?php endif; ?>

<?php if (Helper::checkRoute('/accounting/index/')) : ?>
    <li>
        <a href="<?= Url::toRoute(['/accounting/order-of-payment/technical-services'], $schema = true) ?>">
            <i class="menu-icon fa fa-calculator bg-red"></i>
            <div class="menu-info">
                <h4 class="control-sidebar-subheading">Accounting</h4>
            </div>
        </a>
    </li>
<?php endif; ?>

<?php if (Helper::checkRoute('/report/default/index/')) : ?>
    <li>
        <a href="<?= Url::toRoute(['/report/default/index'], $schema = true) ?>">
            <i class="menu-icon fa fa-file-text-o bg-teal"></i>
            <div class="menu-info">
                <h4 class="control-sidebar-subheading">Reports</h4>
            </div>
        </a>
    </li>
<?php endif; ?>

<?php if (Helper::checkRoute('/cashier/index/')) : ?>
    <li>
        <a href="<?= Url::toRoute(['/cashier/official-receipt/technical-services'], $schema = true) ?>">
            <i class="menu-icon fa fa-money bg-green"></i>
            <div class="menu-info">
                <h4 class="control-sidebar-subheading">Cashier</h4>
            </div>
        </a>
    </li>
<?php endif; ?>

<?php if (Helper::checkRoute('/doctrack/*')) : ?>
<li>
    <a href="<?= Url::toRoute(['/doctrack/intercomm'], $schema = true) ?>">
        <i class="menu-icon fa fa-file bg-yellow"></i>
        <div class="menu-info">
            <h4 class="control-sidebar-subheading">Document Tracking</h4>
        </div>
    </a>
</li>
<?php endif; ?>