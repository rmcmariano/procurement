<?php

namespace app\modules\PurchaseRequest\controllers;

use app\modules\PurchaseRequest\models\BiddingList;
use app\modules\PurchaseRequest\models\HistoryLog;
use app\modules\PurchaseRequest\models\ItemHistoryLogs;
use app\modules\PurchaseRequest\models\PrItems;
use app\modules\PurchaseRequest\models\ProcurementMode;
use app\modules\PurchaseRequest\models\PrType;
use app\modules\PurchaseRequest\models\PurchaseRequest;
use app\modules\PurchaseRequest\models\Quotation;
use app\modules\PurchaseRequest\models\TrackStatus;
use app\modules\user\models\Profile;
use yii\filters\VerbFilter;
use yii\web\Controller;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Yii;
use yii2tech\spreadsheet\Spreadsheet;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class GenerateReportController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    // end user
    public function actionPrReport()
    {
        $pr_approved = PurchaseRequest::find()
            ->where([
                'AND',
                ['requested_by' => Yii::$app->user->identity->id],
                ['not', ['status' => ['1', '8', '3', '31', '42', '43', '18', '58']]]
            ])
            ->orWhere([
                'AND',
                ['created_by' => Yii::$app->user->identity->id],
                ['not', ['status' => ['1', '8', '3', '31', '42', '43', '18', '58']]]
            ])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $pr_approved,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $pr_approved,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // pending budget
    public function actionBudgetPrReport()
    {

        $items = PrItems::find()
            ->where(['status' => [2, 32,]])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $prBudget = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['2', '51']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $prBudget,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        // $project_charge = ProjectBasicInfo::find()
                        //     ->where(['id' => $model->charge_to])
                        //     ->one();
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                        // return $project_charge->project_title;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $prBudget,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // approved budget
    public function actionBudgetApprovedprReport()
    {
        $items = PrItems::find()
            ->where(['status' => [2, 32,]])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $prBudget = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['7']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $prBudget,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $prBudget,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // Accounting
    public function actionAccountingPrReport()
    {
        $items = PrItems::find()
            ->where(['status' => ['22', '5']])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $prAccounting = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere([
                'AND',
                ['pr_type_id' => 3],
                ['status' => ['7', '5']]
            ])
            ->orWhere([
                'AND',
                ['pr_type_id' => 2],
                ['status' => ['2', '5']]
            ])->orderBy(['time_stamp' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $prAccounting,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        // $project_charge = ProjectBasicInfo::find()
                        //     ->where(['id' => $model->charge_to])
                        //     ->one();
                        // return $project_charge->project_title;
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $prAccounting,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // Bac
    public function actionBacPrReport()
    {
        $bacStatus = PrItems::find()
            ->where(['status' => TRACKSTATUS::BAC_STATUS])
            ->asArray()->all();

        $listData = ArrayHelper::map($bacStatus, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $prBac = PurchaseRequest::find()->where(['id' => $listData])
            ->andWhere(['status' => ['7', '4', '44', '45', '36', '49', '54', '56']])
            ->andWhere(['pr_type_id' => ['3', '4']])
            ->orderBy(['time_stamp' => SORT_DESC]);


        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $prBac,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        // $project_charge = ProjectBasicInfo::find()
                        //     ->where(['id' => $model->charge_to])
                        //     ->one();
                        // return $project_charge->project_title;
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $prBac,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    public function actionBacPrItemsReport()
    {
        $bacPr = PurchaseRequest::find()
            ->where(['not', ['mode_pr_id' => NULL]])
            ->all();

        $listData = ArrayHelper::map($bacPr, 'id', function ($model) {
            return $model['id'];
        });

        $items = PrItems::find()
            ->where(['pr_id' => $listData])
            ->orderBy(['pr_id' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $items,
            ]),
            'columns' => [
                [
                    'attribute' => 'item_name',
                    'label' => 'Code (PAP)',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ],
                        'width' => 20,
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'PR #',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ],
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'font' => [
                            'bold' => true,
                        ],

                    ],
                    'dimensionOptions' => [
                        'width' => 30,
                    ],
                    'value' => function ($model) {
                        $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                        return $pr->pr_no;
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'ITEM NAME',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ],
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ],
                        ],
                        'font' => [
                            'bold' => true,
                        ],

                    ],
                    'dimensionOptions' => [
                        'width' => 30,
                    ],
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'PMO / End-User',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ],
                    'value' => function ($model) {
                        $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                        $nameProfile = Profile::find()->where(['id' => $pr->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Mode of Procurement',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                        return $pr->procurementmode->mode_name;
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Pre-Proc Conference',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        $quotations = Quotation::find()->where(['pr_id' => $model->pr_id])->andWhere(['option_id' => '1'])->all();
                        if (empty($quotations)) {
                            return '';
                        }
                        
                        $formattedQuotations = [];
                        foreach ($quotations as $quotation) {
                            $formattedQuotations[] = Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:d-M-Y');
                        }
                    
                        return implode("\n", $formattedQuotations);
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Ads/Post of IB',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        $quotations = Quotation::find()->where(['pr_id' => $model->pr_id])->andWhere(['option_id' => '2'])->all();
                        if (empty($quotations)) {
                            return '';
                        }
                        
                        $formattedQuotations = [];
                        foreach ($quotations as $quotation) {
                            $formattedQuotations[] = Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:d-M-Y');
                        }
                    
                        return implode("\n", $formattedQuotations);
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Eligibility Check',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();
                        $quotationPb = Quotation::find()->where(['pr_id' => $model->pr_id])->andWhere(['option_id' => '4'])->all();
                        $quotationSvp = Quotation::find()->where(['pr_id' => $model->pr_id])->andWhere(['option_id' => '9'])->all();

                        if ($pr->mode_pr_id != ['1', '2', '3']) {
                            if (empty($quotationSvp)) {
                                return '';
                            }
                            
                            $formattedQuotationsvp = [];
                            foreach ($quotationSvp as $quotationsvp) {
                                $formattedQuotationsvp[] = Yii::$app->formatter->asDatetime(strtotime($quotationsvp->option_date), 'php:d-M-Y');
                            }
                        
                            return implode("\n", $formattedQuotationsvp);
                        }
                        if ($quotationPb == NULL) {
                            if (empty($quotationPb)) {
                                return '';
                            }
                            
                            $formattedQuotations = [];
                            foreach ($quotationPb as $quotationpb) {
                                $formattedQuotations[] = Yii::$app->formatter->asDatetime(strtotime($quotationpb->option_date), 'php:d-M-Y');
                            }
                        
                            return implode("\n", $formattedQuotations);
                        }
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Sub/Open of Bids',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        $quotations = Quotation::find()->where(['pr_id' => $model->pr_id])->andWhere(['option_id' => '4'])->all();

                        if (empty($quotations)) {
                            return '';
                        }
                        
                        $formattedQuotations = [];
                        foreach ($quotations as $quotation) {
                            $formattedQuotations[] = Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:d-M-Y');
                        }
                    
                        return implode("\n", $formattedQuotations);
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Bid Evaluation',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Post Qual',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Date of BAC Resolution Recommending Award',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {

                        $quotations = Quotation::find()->where(['pr_id' => $model->pr_id])->andWhere(['option_id' => '10'])->orderBy(['time_stamp' => SORT_DESC])->all();
                        if (empty($quotations)) {
                            return '';
                        }
                        
                        $formattedQuotations = [];
                        foreach ($quotations as $quotation) {
                            $formattedQuotations[] = Yii::$app->formatter->asDatetime(strtotime($quotation->option_date), 'php:d-M-Y');
                        }
                    
                        return implode("\n", $formattedQuotations);
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Notice of Award',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Contract Signing ',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Notice to Proceed ',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Delivery/Completion ',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Inspection & Acceptance',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        return '';
                    }
                ],
                [
                    'attribute' => 'item_name',
                    'label' => 'Source of Funds ',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        $pr = PurchaseRequest::find()->where(['id' => $model->pr_id])->one();

                        if ($pr->charge_to == 0 || $pr == NULL) {
                            return 'GAA';
                        }
                        return $pr->chargedisplay->project_title;
                    }
                ],
                [
                    'attribute' => 'total_cost',
                    'label' => 'ABC Total Cost ',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                ],
                [
                    'attribute' => 'total_cost',
                    'label' => 'Contract Cost ',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'wrapText' => true,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'width' => 20,
                    ],
                    'value' => function ($model) {
                        // Attempt to find the bidding record
                        $bidding = BiddingList::find()->where(['item_id' => $model['id']])->andWhere(['status' => TrackStatus::PPMS_STATUS])->one();

                        if ($bidding !== null) {
                            if ($bidding->status == 17) {
                                $total = $model['quantity'] * $bidding['supplier_price'];
                                return $total;
                            } elseif ($bidding->status == NULL) {
                                return '';
                            }
                        }

                        return '';
                    }

                ],
            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $items,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['item_name']
        ]);
    }


    // PPMS
    public function actionPpmsPrReport()
    {
        $items = PrItems::find()
            ->where(['status' => TrackStatus::PPMS_STATUS])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $prPpms = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere(['status' => ['7', '44', '45', '46', '51']])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $prPpms,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        // $project_charge = ProjectBasicInfo::find()
                        //     ->where(['id' => $model->charge_to])
                        //     ->one();
                        // return $project_charge->project_title;
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $prPpms,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // PPMS
    public function actionSdoPrReport()
    {
        $items = PrItems::find()
            ->where(['status' => '2'])
            ->all();

        $listData = ArrayHelper::map($items, 'pr_id', function ($model) {
            return $model['pr_id'];
        });

        $prSdo = PurchaseRequest::find()
            ->where(['id' => $listData])
            ->andWhere(['pr_type_id' => '1'])
            ->andWhere(['status' => ['2']])
            ->orderBy(['time_stamp' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $prSdo,
            ]),
            'columns' => [
                [
                    'attribute' => 'pr_no',
                    'label' => 'PR Number',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'responsibility_code',
                    'label' => 'Responsibility Code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'pr_type_id',
                    'label' => 'Type of PR',
                    'value' => function ($model) {
                        $type = PrType::find()->where(['id' => $model->pr_type_id])->one();
                        return $type->type_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'mode_pr_id',
                    'label' => 'Mode of PR',
                    'value' => function ($model) {
                        $mode = ProcurementMode::find()->where(['mode_id' => $model->mode_pr_id])->one();
                        if ($model->mode_pr_id == NULL) {
                            return ' ';
                        }
                        return $mode->mode_name;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'date_of_pr',
                    'label' => 'Date Created',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'purpose',
                    'label' => 'Purpose',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'charge_to',
                    'label' => 'Charge to',
                    'value' => function ($model) {
                        // $project_charge = ProjectBasicInfo::find()
                        //     ->where(['id' => $model->charge_to])
                        //     ->one();
                        // return $project_charge->project_title;
                        if ($model->pr_type_id == 3 && $model->charge_to == 0) {
                            return 'GAA';
                        }
                        if ($model->pr_type_id == 1 && $model->charge_to == 0) {
                            return 'SDO';
                        }
                        if ($model->charge_to == NULL) {
                            return '-';
                        }
                        return $model->chargedisplay ? $model->chargedisplay->project_title : '';
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'requested_by',
                    'label' => 'Requested By',
                    'value' => function ($model) {
                        $nameProfile = Profile::find()->where(['id' => $model->requested_by])->one();
                        $fullNameProfile = ($nameProfile ? $nameProfile->fname : '') . ' ' .  ($nameProfile ? $nameProfile->lname : '');

                        return $fullNameProfile;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'user_id',
                    'label' => 'End User',
                    'format' => 'html',
                    'value' => function ($model) {
                        return implode(", ", $model->endUserNames);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'section',
                    'label' => 'Section',
                    'value' => 'sectiondisplay.section_code',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],

            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'models' => $prPpms,
            'mode' => 'export',
            'asAttachment' => true,
            'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // PR History logs
    public function actionPrlogsReport()
    {
        $historylogs = HistoryLog::find()
            ->where(['pr_id' => $_GET['id']])
            ->orderBy(['action_date' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $historylogs,
            ]),
            'columns' => [
                [
                    'attribute' => 'action_date',
                    'label' => 'PROCESS DATE & TIME',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime(strtotime($model->action_date), 'php:d-M-Y | H:i');
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'action_status',
                    'label' => 'STATUS',
                    'value' => function ($model) {
                        return $model->trackstatus->status;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'action_user_id',
                    'label' => 'PROCESSED BY',
                    'value' => function ($v, $model) {
                        $name = Profile::find()->where(['user_id' => $v->action_user_id])->one();
                        return ($name == NULL ? '' : $name->fname . ' ' .  $name->lname);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'remarks',
                    'label' => 'REMARKS',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'historylogs' => $historylogs,
            'mode' => 'export',
            'asAttachment' => true,
            // 'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }

    // Item History logs
    public function actionItemlogsReport()
    {
        $item = PrItems::find()
            ->where(['id' => $_GET['id']])
            ->one();

        $itemhistorylogs = ItemHistoryLogs::find()
            ->where(['item_id' => $item->id])
            ->orderBy(['action_date' => SORT_DESC]);

        $exporter = new Spreadsheet([
            'dataProvider' => new ActiveDataProvider([
                'query' => $itemhistorylogs,
            ]),
            'columns' => [
                [
                    'attribute' => 'action_date',
                    'label' => 'PROCESS DATE & TIME',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime(strtotime($model->action_date), 'php:d-M-Y | H:i');
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'action_status',
                    'label' => 'STATUS',
                    'value' => function ($model) {
                        return $model->trackstatusDisplay->status;
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'action_by',
                    'label' => 'PROCESSED BY',
                    'value' => function ($v, $model) {
                        $name = Profile::find()->where(['user_id' => $v->action_by])->one();
                        return ($name == NULL ? '' : $name->fname . ' ' .  $name->lname);
                    },
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
                [
                    'attribute' => 'action_remarks',
                    'label' => 'REMARKS',
                    'contentOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                        ]
                    ],
                    'headerOptions' => [
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                            ]
                        ],
                        'font' => [
                            'bold' => true,
                        ],
                    ],
                    'dimensionOptions' => [
                        'autosize' => true,
                    ]
                ],
            ]
        ]);
        return $exporter->send('purchase request.xls');

        return Excel::widget([
            'query' => $itemhistorylogs,
            'mode' => 'export',
            'asAttachment' => true,
            // 'columns' => ['pr_no', 'responsibility_code', 'pr_type_id', 'mode_pr_id', 'date_of_pr', 'purpose', 'charge_to', 'requested_by', 'user_id', 'section']
        ]);
    }
}
