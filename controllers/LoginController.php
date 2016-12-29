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
			User::addOrUpdateUser($result['data']['userInfo']['openId'],$result['data']['userInfo']);
		}
    }
}
