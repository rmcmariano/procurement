<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db/db.php';
$user = require __DIR__ . '/db/user.php';
$itdidb_hris = require __DIR__ . '/db/hris.php';
$itdidb_procurement_system = require __DIR__ . '/db/pris.php';
$itdidb_pmis = require __DIR__ . '/db/pmis.php';

$config = [
    'id' => 'basic',
    'name' => 'ITDI Employee App',
    'timeZone' => 'Asia/Manila',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'hoxtpwUHJ7QYmtxCOdr2e16Joe3fOpiY',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => false,
            'authTimeout' => 5200,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtpout.asia.secureserver.net',
                'username' => 'customer@itdi.com.ph',
                'password' => 'mithi@2020',
                'port' => '80',
                'encryption' => '',

            ],
            'useFileTransport' => false,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'db' => $db,
        'userDb' => $user,
        'itdidb_hris' => $itdidb_hris,
        'itdidb_procurement_system' => $itdidb_procurement_system,
        'itdidb_pmis' => $itdidb_pmis,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [],
        ],
        'formatter' => [
            'thousandSeparator' => ',',
            'currencyCode' => 'PHP',
        ],
    ],
    'modules' => [
        'admin' => [
            'class' => 'mdm\admin\Module',
            // 'layout' => 'left-menu',
        ],
        'PurchaseRequest' => [
            'class' => 'app\modules\PurchaseRequest\Module',
            'layout' => 'main',
        ],
        'gridview' =>  [
            'class' => '\kartik\grid\Module',
            'bsVersion' => 3,
        ],
    ],
    'as access' => [
        'class' => 'mdm\admin\components\AccessControl',
        'allowActions' => [
            'site/login',
        ]
    ],
    'params' => $params,
    'defaultRoute' => 'site/login',
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'generators' => [ // HERE
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'adminlte' => '@vendor/dmstr/yii2-adminlte-asset/gii/templates/crud/simple',
                    'customAdminLte' => '@app/templates'
                ]
            ]
        ],
    ];
}

return $config;
