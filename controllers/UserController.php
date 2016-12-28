<?php
namespace app\controllers;

use \QCloud_WeApp_SDK\Auth\LoginService;
use yii\base\Controller;

class UserController extends Controller {
    public function actionIndex() {
        $result = LoginService::check();

        // check failed
        if ($result['code'] !== 0) {
            return;
        }

        $response = array(
            'code' => 0,
            'message' => 'ok',
            'data' => array(
                'userInfo' => $result['data']['userInfo'],
            ),
        );

        echo json_encode($response, JSON_FORCE_OBJECT);
    }
}
