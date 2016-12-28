<?php
$params = require (__DIR__ . '/params.php');

$config = [ 
		'id' => 'basic',
		'basePath' => dirname ( __DIR__ ),
		'bootstrap' => [ 
				'log' 
		],
		'defaultRoute' => '/welcome/index',
		'components' => [ 
				'request' => [ 
						// !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
						'enableCookieValidation' => false,
						'enableCsrfValidation' => false,
						'enableCsrfCookie' => false 
				],
				'cache' => [ 
						'class' => 'yii\caching\FileCache' 
				],
				'errorHandler' => [ 
						'errorAction' => 'site/error' 
				],
				'mailer' => [ 
						'class' => 'yii\swiftmailer\Mailer',
						// send all mails to a file by default. You have to set
						// 'useFileTransport' to false and configure a transport
						// for the mailer to send real emails.
						'useFileTransport' => true 
				],
				'urlManager' => [ 
						'enablePrettyUrl' => true,
						'showScriptName' => false,
						'enableStrictParsing' => false,
						'rules' => [ ] 
				],
				'log' => [ 
						'traceLevel' => YII_DEBUG ? 3 : 0,
						'targets' => [ 
								[ 
										'class' => 'yii\log\FileTarget',
										'levels' => [ 
												'info',
												'error',
												'warning' 
										],
										'logVars' => [ ],
										'maxFileSize' => 2048576,
										'maxLogFiles' => 20,
										'categories' => [ 
												'yii\*',
												'app\models\*' 
										],
										'logFile' => '@app/runtime/logs/app.log.' . date ( 'Ymd' ) 
								],
								[ 
										'class' => 'yii\log\FileTarget',
										'maxFileSize' => 2048576,
										'maxLogFiles' => 20,
										'categories' => [ 
												'debug',
												'test1',
												'test2',
												'test3',
												'test4',
												'test5',
												'test6',
										],
										'logFile' => '@app/runtime/logs/dataLog.log.' . date ( 'Ymd' ),
										'logVars' => [ ] 
								] 
						] 
				],
				'db' => require (__DIR__ .'/'.ENVIRONMENT. '/db.php'),
    ],
		'params' => $params 
];

if (YII_ENV_DEV) {
	// configuration adjustments for 'dev' environment
	$config ['bootstrap'] [] = 'debug';
	$config ['modules'] ['debug'] = [ 
			'class' => 'yii\debug\Module' 
	];
	
	$config ['bootstrap'] [] = 'gii';
	$config ['modules'] ['gii'] = [ 
			'class' => 'yii\gii\Module' 
	];
}

return $config;
