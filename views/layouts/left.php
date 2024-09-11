<?php

use mdm\admin\components\Helper;

// var_dump();die;
?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->profile->firstName ?></p>
                <p><?= Yii::$app->user->identity->profile->lastName ?></p>
            </div>
        </div>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => [
                    [
                        'label' => 'ADMINISTRATOR',
                        'header' => true,
                        'visible' => Helper::checkRoute('/admin/index'),
                        'options' => [
                            'class' => 'header'
                        ]
                    ],
                    [
                        'label' => 'Development',
                        'icon' => 'gear',
                        'visible' => Helper::checkRoute('/gii/index'),
                        'items' => [
                            [
                                'label' => 'Gii',
                                'url' => ['/gii'],
                                'icon' => 'leaf'
                            ],
                            [
                                'label' => 'Debug',
                                'url' => ['/debug'],
                                'icon' => 'bug',
                            ],
                        ],
                    ],
                    [
                        'label' => 'RBAC',
                        'url' => ['/admin/index'],
                        'icon' => 'user',
                        'visible' => Helper::checkRoute('/admin/index'),
                        'items' => [
                            [
                                'label' => 'User',
                                'url'   => ['/admin/user/index'],
                                'active' => Yii::$app->controller->id == 'user',
                            ],
                            [
                                'label' => 'Assignment',
                                'url'   => ['/admin/assignment/index'],
                                'active' => Yii::$app->controller->id == 'assignment',
                            ],
                            [
                                'label' => 'Role',
                                'url'   => ['/admin/role/index'],
                                'active' => Yii::$app->controller->id == 'role',
                            ],
                            [
                                'label' => 'Permission',
                                'url'   => ['/admin/permission/index'],
                                'active' => Yii::$app->controller->id == 'permission',
                            ],
                            [
                                'label' => 'Rule',
                                'url'   => ['/admin/rule/index'],
                                'active' => Yii::$app->controller->id == 'rule',
                            ],
                            [
                                'label' => 'Route',
                                'url'   => ['/admin/route/index'],
                                'active' => Yii::$app->controller->id == 'route',
                            ],
                        ],
                    ],
                    [
                        'label' => 'PR',
                        'url'   => ['/PurchaseRequest/purchase-request/pending-request-index'],
                        'visible' => Yii::$app->user->can('Employee'),
                        'active' => Yii::$app->controller->route == 'pending-request-index',
                    ],
                ],
            ]
        ) ?>

    </section>

</aside>