<?php
namespace app\controllers;

use \QCloud_WeApp_SDK\Auth\LoginService;
use yii\base\Controller;
use app\service\QcloudInit;
use app\models\User;

class LoginController extends Controller {
	public function actionIndex() {
		QcloudInit::Conf ();
		$result = LoginService::login ();
		if ($result ['code'] == 0) {
			$user = new User();
			\Yii::info($result['data'],'test2');
			$user->attributes = $result['data']['userInfo'];
			\Yii::info(json_encode($user),'test3');
			$user->save();
		}
    }
}
