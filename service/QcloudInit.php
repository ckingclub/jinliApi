<?php
namespace app\service;

use QCloud_WeApp_SDK\Conf;
class QcloudInit {
	public static function Conf(){
		$config = \Yii::$app->params['qcloudUrl'];
		Conf::setup($config);
	}
}

?>