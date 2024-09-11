<?php

use mdm\admin\components\Helper;

use app\modules\document_tracking\models\DocumentResponse;
use app\modules\document_tracking\models\ResponseTable;


if (isset($_GET['type'])){

    $highlight = $_GET['type'];
}

else {

    $highlight = NULL;

}

$inter_count_receive = count(ResponseTable::find()
                                ->where(['recipient_id' => Yii::$app->user->identity->id])
                                // ->andWhere(['not', ['status' => '1']])
                                ->andWhere(['status' => '0'])
                                ->andWhere(['like', 'tracking_number', 'INTER'])
                                ->all()
                        );
$inc_count_receive = count(ResponseTable::find()
                                ->where(['recipient_id' => Yii::$app->user->identity->id])
                                // ->andWhere(['not', ['view' => '1']])
                                ->andWhere(['status' => '0'])
                                ->andWhere(['like', 'tracking_number', 'INC'])
                                ->all()
                        );
$out_count_receive = count(ResponseTable::find()
                                ->where(['recipient_id' => Yii::$app->user->identity->id])
                                // ->andWhere(['not', ['view' => '1']])
                                ->andWhere(['status' => '0'])
                                ->andWhere(['like', 'tracking_number', 'OUT'])
                                ->all()
                        );

$total = $inter_count_receive + $inc_count_receive + $out_count_receive;

$inter_count_release = count(DocumentResponse::find()
                                        ->select('document_response.tracking_number, document_response.id, document_response.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 1],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 1],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->distinct()
                                        ->all());

$inc_count_release = count(DocumentResponse::find()
                                        ->select('document_response.tracking_number, document_response.id, document_response.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 2],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 2],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->distinct()
                                        ->all());

$out_count_release = count(DocumentResponse::find()
                                        ->select('document_response.tracking_number, document_response.id, document_response.view')
                                        ->joinWith('documentDetails')
                                        ->where(['and',
                                            ['document_details.document_type' => 3],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'CREATED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->orWhere(['and',
                                            ['document_details.document_type' => 3],
                                            ['action_by' => Yii::$app->user->identity->id],
                                            ['action' => 'RECEIVED'],
                                            ['status' => 0],
                                            ['is_closed' => 0],
                                        ])
                                        ->distinct()
                                        ->all());
$total2 = $inter_count_release + $inc_count_release + $out_count_release;

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
                        'label' => 'DOCUMENT TRACKING',
                        'header' => true,
                        'visible' => Helper::checkRoute('/doctrack/*'),
                        'options' => [
                            'class' => 'header'
                        ]
                    ],
                    ['label' => 'Create Document', 'url' => ['/document_tracking/documents/create']],
                    // [
                    //     'label' => 'Create Document',
                    //     'url'   => ['/document_tracking/documents'],
                    //     'icon' => 'file',
                    //     'visible' => Helper::checkRoute('/document_tracking/*'),
                    //     'items' => [
                    //         [
                    //             'label' => 'Internal Communication',
                    //             'url'   => ['/document_tracking/documents/index?type=IC'],
                    //             'active' => (Yii::$app->controller->id == 'documents' && $highlight == 'IC' ? 'active' : '')
                    //         ],
                    //         [
                    //             'label' => 'Incoming',
                    //             'url'   => ['/document_tracking/documents/index?type=I'],
                    //             'active' => (Yii::$app->controller->id == 'documents' && $highlight == 'I' ? 'active' : '')
                    //         ],
                    //         [
                    //             'label' => 'Outgoing',
                    //             'url'   => ['/document_tracking/documents/index?type=O'],
                    //             'active' => (Yii::$app->controller->id == 'documents' && $highlight == 'O' ? 'active' : '')
                    //         ],
                    //     ],
                    // ],
                    [
                        'label' => 'Receive Document',
                        'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $total . '</span></span></a>',
                        'url'   => ['/document_tracking/receive-document'],
                        'icon' => 'tags',
                        'visible' => Helper::checkRoute('/document_tracking/*'),
                        'items' => [
                            [
                                'label' => 'Internal Communication',
                                'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $inter_count_receive . '</span></span></a>',
                                'url'   => ['/document_tracking/receive-document/index?type=IC'],
                                'active' => (Yii::$app->controller->id == 'receive-document' && $_GET['type'] == 'IC' ? 'active' : ''),
                            ],
                            [
                                'label' => 'Incoming',
                                'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $inc_count_receive . '</span></span></a>',
                                'url'   => ['/document_tracking/receive-document/index?type=I'],
                                'active' => (Yii::$app->controller->id == 'receive-document' && $_GET['type'] == 'I' ? 'active' : '')
                            ],
                            [
                                'label' => 'Outgoing',
                                'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $out_count_receive . '</span></span></a>',
                                'url'   => ['/document_tracking/receive-document/index?type=O'],
                                'active' => (Yii::$app->controller->id == 'receive-document' && $_GET['type'] == 'O' ? 'active' : '')
                            ],
                        ],
                    ],
                    [
                        'label' => 'Release Document',
                        'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $total2 . '</span></span></a>',
                        'url'   => ['/document_tracking/release-document'],
                        'icon' => 'share-square-o',
                        'visible' => Helper::checkRoute('/document_tracking/*'),
                        'items' => [
                            [
                                'label' => 'Internal Communication',
                                'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $inter_count_release . '</span></span></a>',
                                'url'   => ['/document_tracking/release-document/index?type=IC'],
                                'active' => (Yii::$app->controller->id == 'release-document' && $_GET['type'] == 'IC' ? 'active' : '')
                            ],
                            [
                                'label' => 'Incoming',
                                'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $inc_count_release . '</span></span></a>',
                                'url'   => ['/document_tracking/release-document/index?type=I'],
                                'active' => (Yii::$app->controller->id == 'release-document' && $_GET['type'] == 'I' ? 'active' : '')
                            ],
                            [
                                'label' => 'Outgoing',
                                'template' => '<a href="{url}">{icon} {label} <span class="pull-right-container"><span class="label label-danger pull-right">'. $out_count_release . '</span></span></a>',
                                'url'   => ['/document_tracking/release-document/index?type=O'],
                                'active' => (Yii::$app->controller->id == 'release-document' && $_GET['type'] == 'O' ? 'active' : '')
                            ],
                        ],
                    ],
                    [
                        'label' => 'Redirect Document',
                        'url'   => ['/document_tracking/redirect-document'],
                        'icon' => 'arrows',
                        'visible' => Helper::checkRoute('/document_tracking/*'),
                        'items' => [
                            [
                                'label' => 'Internal Communication',
                                'url'   => ['/document_tracking/redirect-document/index?type=IC'],
                                'active' => (Yii::$app->controller->id == 'redirect-document' && $_GET['type'] == 'IC' ? 'active' : '')
                            ],
                            [
                                'label' => 'Incoming',
                                'url'   => ['/document_tracking/redirect-document/index?type=I'],
                                'active' => (Yii::$app->controller->id == 'redirect-document' && $_GET['type'] == 'I' ? 'active' : '')
                            ],
                            [
                                'label' => 'Outgoing',
                                'url'   => ['/document_tracking/redirect-document/index?type=O'],
                                'active' => (Yii::$app->controller->id == 'redirect-document' && $_GET['type'] == 'O' ? 'active' : '')
                            ],
                        ],
                    ],
                    [
                        'label' => 'Reports',
                        'url'   => ['/document_tracking/documents'],
                        'icon' => 'file',
                        'visible' => Helper::checkRoute('/document_tracking/*'),
                        'items' => [
                            [
                                'label' => 'Internal Communication',
                                'url'   => ['/document_tracking/documents/index?type=IC'],
                                'active' => (Yii::$app->controller->id == 'documents' && $highlight == 'IC' ? 'active' : '')
                            ],
                            [
                                'label' => 'Incoming',
                                'url'   => ['/document_tracking/documents/index?type=I'],
                                'active' => (Yii::$app->controller->id == 'documents' && $highlight == 'I' ? 'active' : '')
                            ],
                            [
                                'label' => 'Outgoing',
                                'url'   => ['/document_tracking/documents/index?type=O'],
                                'active' => (Yii::$app->controller->id == 'documents' && $highlight == 'O' ? 'active' : '')
                            ],
                        ],
                    ],
                ],
            ]
        ) ?>
    </section>
</aside>