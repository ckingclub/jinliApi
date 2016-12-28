<?php
namespace app\controllers;

use \QCloud_WeApp_SDK\Tunnel\TunnelService;
use yii\base\Controller;
use app\service\ChatTunnelHandler;


class TunnelController extends Controller {
    public function actionIndex() {
        $handler = new ChatTunnelHandler();
        TunnelService::handle($handler, array('checkLogin' => TRUE));
    }
}
