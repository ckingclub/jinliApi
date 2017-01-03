<?php
namespace app\controllers;

class PayController extends ApiController {
	public function actionIndex($orderId,$money) {
		
		return [
			"code"=>1
		];
		//记录用户支付的订单号，用户ID，应付金额与实付金额，并标记是否退款，是否打折
	}
}
