<?php

// comment out the following two lines when deployed to production
//defined('YII_DEBUG') or define('YII_DEBUG', true);
//defined('YII_ENV') or define('YII_ENV', 'dev');

define('ENVIRONMENT', 'online');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../vendor/qcloud/weapp-sdk/AutoLoader.php');//引入sdk

$config = require(__DIR__ . '/../config/web.php');

(new yii\web\Application($config))->run();
