<?php
namespace app\controllers;

use \QCloud_WeApp_SDK\Tunnel\TunnelService;
use app\service\ChatTunnelHandler;


class TunnelController extends ApiController {
    public function actionIndex() {
        $handler = new ChatTunnelHandler();
        TunnelService::handle($handler, array('checkLogin' => TRUE));
    }
}
