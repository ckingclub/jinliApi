<?php
namespace app\controllers;

use app\controllers\ApiController;

class OrderController extends ApiController {
	/**
	 * 生成订单，保存至数据库，返回订单号
	 */
	public function actionAdd($openId,$orderDetatil){
		return [
			"code" => 1,
			"orderId" => 'JLZT201701031234'
		];
	}
	
	/**
	 *取消订单 
	 */
	public function actionCancleOrder($orderId) {
		return [
			"code" => 1
		];
	}
	
	/**
	 * 获取用户订单列表
	 */
	public function actionOrderList($openId) {
		//直接读数据库
		return [
			"code" => 1,
			"orderList" => []
		];
	}
	
	/**
	 * 获取订单详情
	 */
	public function actionOrderDetail($orderId) {
		//直接读数据库
		return [
			"code" => 1,
			"status" => 'payed',
			"orderDetail" => []
		];
	}
	
	/**
	 * 将商品添加到购物车，返回是否成功
	 */
	public function actionAddShoppingCart ($openId,$goodsId,$specifications,$num) {
		//插入数据库
		return [
			"code" => 1
		];
	}
	
	/**
	 * 获取购物车列表
	 */
	public function actionShoppingCart($openId) {
		//直接读数据库
		return [
			"code" => 1,
			"shoppingCarList" => []
		];
	}
	
	/**
	 * 编辑购物车内容
	 */
	public function actionEditShoppingCart ($openId,$goodsId,$option) {
		//增、删
		return [
			"code" => 1,
		];
	}
}