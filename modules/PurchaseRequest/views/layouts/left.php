<?php

use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\PurchaseOrder;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use mdm\admin\components\Helper;
use yii\helpers\ArrayHelper;

$authManager = Yii::$app->getAuthManager();

$modelPrPending = PurchaseRequest::find()->where(['created_by' => Yii::$app->user->identity->id])->andWhere(['status' => ['1', '41']])->all();
$count = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPrPending])->distinct();
$countPending = $count->count();
$pendingCount =  $countPending;

$modelPrApproved = PurchaseRequest::find()->where(['created_by' => Yii::$app->user->identity->id])->andWhere(['status' => ['39', '48', '2', '7']])->all();
$count = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPrApproved])->distinct();
$countApproved = $count->count();
$approvedCount =  $countApproved;

// $modelPrChief = PurchaseRequest::find()->where(['approved_by' => Yii::$app->user->identity->id])->andWhere(['status' => ['1', '41']])->all();
// $countChief = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPrChief])->distinct();
// $countChiefnotif = $countChief->count();
// $chiefnotifCount =  $countChiefnotif;


$isVisible = Yii::$app->user->can('CHIEF/PL Employee') || Yii::$app->user->can('System Admin') || Yii::$app->user->can('SEC Employee');
$modelPrChief = PurchaseRequest::find()
    ->where(['approved_by' => Yii::$app->user->identity->id])
    ->andWhere(['status' => ['1', '41']]);

if (!$isVisible) {
    $modelPrChief->andWhere(['visible' => true]); 
}

$modelPrChief = $modelPrChief->all();
$countChiefnotif = count($modelPrChief);
$chiefnotifCount = $countChiefnotif;

// ppms
$ppmsitems = PrItems::find()->where(['status' => ['17']])->all();
$ppmsdata = ArrayHelper::map($ppmsitems, 'pr_id', function ($model) {
    return $model['pr_id'];
});
$prPpms = PurchaseRequest::find()->where(['id' => $ppmsdata])->andWhere(['status' => ['7', '44', '45', '46', '51']])->all();
$countPpms = PurchaseRequest::find()->select(['id'])->where(['id' => $prPpms])->distinct();
$countPpmsnotif = $countPpms->count();
$ppmsnotifCount =  $countPpmsnotif;

// budget-pending list
$budgetitems = PrItems::find()->where(['status' => ['2']])->all();
$budgetdata = ArrayHelper::map($budgetitems, 'pr_id', function ($model) {
    return $model['pr_id'];
});
$modelPrBudget = PurchaseRequest::find()->where(['id' => $budgetdata])->andWhere(['status' => ['2', '51']])->andWhere(['pr_type_id' => ['3']])->all();
$countBudget = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPrBudget])->distinct();
$countBudgetnotif = $countBudget->count();
$budgetnotifCount =  $countBudgetnotif;

// budget-approved list
$budgetapproveditems = PrItems::find()->where(['status' => ['32']])->all();
$budgetapproveddata = ArrayHelper::map($budgetapproveditems, 'pr_id', function ($model) {
    return $model['pr_id'];
});
$modelPrapprovedBudget = PurchaseRequest::find()->where(['id' => $budgetapproveddata])->andWhere(['status' => ['7', '51']])->andWhere(['pr_type_id' => ['3']])->all();
$countapprovedBudget = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPrapprovedBudget])->distinct();
$countapprovedBudgetnotif = $countapprovedBudget->count();
$approvedbudgetnotifCount =  $countapprovedBudgetnotif;

// accounting-request list
$accountingitems = PrItems::find()->where(['status' => ['22', '2']])->all();
$accountingdata = ArrayHelper::map($accountingitems, 'pr_id', function ($model) {
    return $model['pr_id'];
});
$modelPraccounting = PurchaseRequest::find()->where(['id' => $accountingdata])->andWhere(['status' => ['2', '51', '7']])->andWhere(['pr_type_id' => ['3']])->all();
$countAccounting = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPraccounting])->distinct();
$countAccountingnotif = $countAccounting->count();
$accountingnotifCount =  $countAccountingnotif;

// bac-request list
$modelPrbac = PurchaseRequest::find()->where(['status' => ['4', '49']])->all();
$countBac = PurchaseRequest::find()->select(['id'])->where(['id' => $modelPrbac])->distinct();
$countBacnotif = $countBac->count();
$bacnotifCount =  $countBacnotif;

// conforme list
$modelConforme = PurchaseOrder::find()->where(['conforme_status' => ['1']])->all();
$countConforme = PurchaseOrder::find()->select(['id'])->where(['id' => $modelConforme])->distinct();
$countConformenotif = $countConforme->count();
$conformenotifCount =  $countConformenotif;

// po/wo list
$modelPo = PurchaseOrder::find()->where(['po_status' => ['5']])->all();
$countPo = PurchaseOrder::find()->select(['id'])->where(['id' => $modelPo])->distinct();
$countPonotif = $countPo->count();
$ponotifCount =  $countPonotif;


?>
<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="/images/itdi-logo.png" class="img-circle" alt="User Image" />
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->profile->firstName ?></p>
                <p><?= Yii::$app->user->identity->profile->lastName ?></p>
            </div>
        </div>

        <!-- search form -->
        <form action="#" method="get" class="sidebar-form">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="Search..." />
                <span class="input-group-btn">
                    <button type='submit' name='search' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        </form>
        <!-- /.search form -->

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget' => 'tree'],
                'items' => [
                    [
                        'label' => 'End users',
                        'header' => true,
                        'options' => [
                            'class' => 'header'
                        ]
                    ],

                    [
                        'label' => 'Purchase Request',
                        'url'   => ['/PurchaseRequest/purchase-request/pending-request-index'],
                        'icon' => 'shopping-cart',
                        'items' => [
                            [
                                'label' => 'Create New Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="circle"></span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/purchaserequest-create'],
                                'visible' => Yii::$app->user->can('Employee')  !== null,
                            ],
                            [
                                'label' => 'Pending Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $countPending . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/pending-request-index'],
                            ],
                            [
                                'label' => 'On-Process Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $approvedCount . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/on-process-request-index'],
                            ],

                            [
                                'label' => 'Cancelled Requests',
                                'icon' => 'circle',
                                'url'   => ['/PurchaseRequest/purchase-request/archive'],

                            ],
                        ],
                    ],

                    [
                        'label' => 'Project Leader/Chief',
                        'url'   => ['/PurchaseRequest/purchase-request/chief-request-index'],
                        'icon' => 'folder',
                        'visible' => Yii::$app->user->can('CHIEF/PL Employee') || Yii::$app->user->can('System Admin') || Yii::$app->user->can('SEC Employee'),
                        'items' => [
                            [
                                'label' => 'Pending Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $chiefnotifCount . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/chief-request-index'],
                            ],
                            [
                                'label' => 'Monitoring List',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/chief-monitoring-list'],
                            ],
                        ]
                    ],

                    [
                        'label' => 'FMD-Budget',
                        'url'   => ['https://procurement.itdi.ph/PurchaseRequest/purchase-request/budget-pending-request-index'],
                        'icon' => 'th-list',
                        'visible' => Yii::$app->user->can('BUDGET Employee') || Yii::$app->user->can('System Admin'),
                        'items' => [
                            [
                                'label' => 'Pending Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $budgetnotifCount . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/budget-pending-request-index'],

                            ],
                            // [
                            //     'label' => 'Approved Request',
                            //     'icon' => 'circle',
                            //     'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $approvedbudgetnotifCount . '</span></a>',
                            //     'url'   => ['/PurchaseRequest/purchase-request/budget-approved-request-index'],

                            // ],
                            [
                                'label' => 'Monitoring List',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/budget-monitoring-list'],
                            ],
                        ]
                    ],

                    [
                        'label' => 'FMD-Accounting',
                        'url'   => ['/PurchaseRequest/purchase-request/accounting-request-index'],
                        'icon' => 'th-list',
                        'visible' => Yii::$app->user->can('ACCOUNTING Employee') || Yii::$app->user->can('System Admin'),
                        'items' => [
                            [
                                'label' => 'List of Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $accountingnotifCount . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/accounting-request-index'],

                            ],
                            // [
                            //     'label' => 'P.O. / W.O.',
                            //     'url'   => ['/PurchaseRequest/purchase-order/po-index-budget'],
                            //     'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) || $authManager->getAssignment('FMD-BUDGET', Yii::$app->user->identity->id) !== null,
                            // ],
                        ]
                    ],

                    [
                        'label' => 'BAC',
                        'url'   => ['/PurchaseRequest/purchase-request/bac-request-index'],
                        'icon' => 'th-list',
                        'visible' => Yii::$app->user->can('BAC Employee') || Yii::$app->user->can('System Admin'),
                        'items' => [
                            [
                                'label' => 'On-Process Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $bacnotifCount . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/bac-request-index'],

                            ],
                            [
                                'label' => 'Monitoring List',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/bac-monitoring-list'],
                            ],
                            [
                                'label' => 'List for Bid Offers',
                                'url'   => ['/PurchaseRequest/bidding/bac-bid-offer-list'],

                            ],
                            [
                                'label' => 'Accepted Bid bulletin',
                                'url'   => ['/PurchaseRequest/purchase-request/bidbulletin-acceptedlist'],

                            ],
                        ]
                    ],

                    [
                        'label' => 'Procurement',
                        'url'   => ['/PurchaseRequest/purchase-request/procurement-request-index'],
                        'icon' => 'th-list',
                        'visible' => Yii::$app->user->can('PPMS Employee') || Yii::$app->user->can('System Admin'),
                        'items' => [
                            [
                                'label' => 'On-Process Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $ppmsnotifCount . '</span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/procurement-request-index'],

                            ],
                            // [
                            //     'label' => 'P.O./W.O.',
                            //     'icon' => 'circle',
                            //     'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $ponotifCount . '</span></a>',
                            //     'url'   => ['/PurchaseRequest/purchase-order/purchaseorder-list'],
                            //     'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) || $authManager->getAssignment('PROCUREMENT', Yii::$app->user->identity->id) !== null,
                            // ],
                            // [
                            //     'label' => 'Conforme',
                            //     'icon' => 'circle',
                            //     'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $conformenotifCount . '</span></a>',
                            //     'url'   => ['/PurchaseRequest/purchase-order/ppms-conforme-lists'],
                            //     'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) || $authManager->getAssignment('PROCUREMENT', Yii::$app->user->identity->id) !== null,
                            // ],
                            // [
                            //     'label' => 'Delivery',
                            //     'icon' => 'circle',
                            //     'template' => '<a href="{url}">{icon}{label} <span class="badge">' . $bacnotifCount . '</span></a>',
                            //     'url'   => ['/PurchaseRequest/purchase-order/ppms-delivery-lists'],
                            //     'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) || $authManager->getAssignment('PROCUREMENT', Yii::$app->user->identity->id) !== null,
                            // ],
                        ]
                    ],

                    [
                        'label' => 'SDO',
                        'url'   => ['/PurchaseRequest/purchase-request/sdo-index'],
                        'icon' => 'th-list',
                        'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) !== null,
                        'items' => [
                            [
                                'label' => 'List of Request',
                                'url'   => ['/PurchaseRequest/purchase-request/sdo-index'],
                                'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) || $authManager->getAssignment('PROCUREMENT', Yii::$app->user->identity->id) !== null,
                            ],
                        ]
                    ],

                    // [
                    //     'label' => 'Payment',
                    //     'url'   => ['/PurchaseRequest/payment/index'],
                    //     'icon' => 'th-list',
                    //     // 'active' => Yii::$app->controller->id == 'payment',
                    //     'visible' => $authManager->getAssignment('Developer', Yii::$app->user->identity->id) !== null,
                    // ],
                    [
                        'label' => 'Suppliers Profile',
                        'url'   => ['/PurchaseRequest/supplier/index'],
                        'icon' => 'file',
                        'visible' => Yii::$app->user->can('BAC Employee') || Yii::$app->user->can('System Admin'),
                        'items' => [
                            [
                                'label' => 'Create New Suppliers',
                                'url'   => ['/PurchaseRequest/supplier/create'],
                                'icon' => 'circle',
                            ],
                            [
                                'label' => 'List of Suppliers',
                                'url'   => ['/PurchaseRequest/supplier/index'],
                                'icon' => 'circle',
                            ],
                        ]
                    ],
                    [
                        'label' => 'System Admin',
                        'url'   => ['/PurchaseRequest/purchase-request/pending-request-index'],
                        'icon' => 'file',
                        'visible' =>  Yii::$app->user->can('System Admin'),
                        'items' => [
                          
                            [
                                'label' => 'All Request',
                                'icon' => 'circle',
                                'template' => '<a href="{url}">{icon}{label} <span class="badge"> </span></a>',
                                'url'   => ['/PurchaseRequest/purchase-request/allrequest-index'],
                            ],
                        ]
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
                ],
            ],

        ) ?>

    </section>
</aside>

<style>
    .badge {
        position: absolute;
        top: -5px;
        right: -5px;
        /* padding: 10px; */
        border-radius: 50%;
        background: green;
        color: white;
    }
</style>