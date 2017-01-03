<?php
namespace app\controllers;

class ShopController extends ApiController {
	
	/**
	 * 获取首页商品信息
	 */
	public function actionHome() {
		//获取banner信息
		
		//获取推荐商品信息
		
		return [
			"code" => 1,
			"goodsList" => []
		];
	}
	
	/**
	 * 获取商品列表的分页商品信息
	 */
	public function actionGoodsList($type,$page) {
		//获取商品类别
		
		//获取当前页数
		
		//读取数据库
		
		return [
			"code" => 1,
			"pageNum" => 1,
			"goodsList" => []
		];
	}
	
	/**
	 * 获取商品详情
	 */
	public function actionGoodsDetail($goodsId) {
		//直接读数据库
		return [
			"code" => 1,
			"name" => "枕头",
			"imgUrls" => [],
			"specifications" => [],
			"remarks" => []
		];
	}
	
	/**
	 * 商品搜索
	 */
	public function actionSearch($keyWord) {
		return [
			"code" => 1,
			"goodsList" => []
		];
	}
}