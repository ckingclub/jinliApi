<?php
namespace app\controllers;

use app\models\Shop;
use app\models\ShopImg;
use app\models\Specifications;
class ShopController extends ApiController {
	
	/**
	 * 获取首页商品信息
	 */
	public function actionHome() {
		$goodsList = [ ];
		// 获取banner信息
		$banners = Shop::getBannerGoods ();
		foreach ( $banners as $key => $banner ) {
			$banners [$key] ['imgUrl'] = ShopImg::getHomeImgUrlsByGoodsId ( $banner ['goodsId'], true );
		}
		$goodsList ['banners'] = $banners;
		// 获取推荐商品信息
		$recommends = Shop::getRecommendGoods ();
		foreach ( $recommends as $key => $recommend ) {
			$recommends [$key] ['imgUrl'] = ShopImg::getHomeImgUrlsByGoodsId ( $recommend ['goodsId'] );
		}
		$goodsList ['recommendGoods'] = $recommends;
		return [ 
				"code" => 1,
				"goodsList" => $goodsList 
		];
	}
	
	/**
	 * 获取商品列表的商品信息
	 */
	public function actionGoodsList($type) {
		$goodsList = [ ];
		// 验证分类是否存在
		$types = array_keys ( \Yii::$app->params ['goodsType'] );
		if (! in_array ( $type, $types )) {
			return [ 
					'code' => 10005,
					'message' => $this->getMessage ( 10005 ) 
			];
		}
		// 读出分类商品列的信息
		$goodsItems = Shop::getGoodsListByType ( $type );
		foreach ( $goodsItems as $goodsItem ) {
			$temp = [ ];
			$temp ['goodsId'] = $goodsItem ['goodsId'];
			$temp ['goddsName'] = $goodsItem ['name'];
			$temp ['price'] = $goodsItem ['priceLowLimit'] . "~" . $goodsItem ['priceUpLimit'];
			$temp ['url'] = ShopImg::getHomeImgUrlsByGoodsId ( $goodsItem ['goodsId'] );
			$goodsList [] = $temp;
		}
		return [ 
				"code" => 1,
				"goodsList" => $goodsList 
		];
	}
	
	/**
	 * 获取商品详情
	 */
	public function actionGoodsDetail($goodsId) {
		$name = '';
		$imgUrls = [ ];
		$specifications = [ ];
		$remarks = '';
		
		// 读取商品的信息
		$goods = Shop::getGoodsInfoByGoodsId ( $goodsId );
		// 如果未找到商品或者商品状态不是正常，返回错误信息
		if (empty ( $goods )) {
			return [ 
					'code' => 10001,
					'message' => $this->getMessage ( 10001 ) 
			];
		}
		if ($goods ['status'] !== 0) {
			return [ 
					'code' => 10002,
					'message' => $this->getMessage ( 10002 ) 
			];
		}
		$name = $goods ['name'];
		$remarks = $goods ['remarks'];
		// 读取商品的图片
		$imgUrls = ShopImg::getBigImgUrlsByGoodsId ( $goodsId );
		// 如果未找到商品图片,返回错误信息
		if (empty ( $imgUrls )) {
			return [ 
					'code' => 10003,
					'message' => $this->getMessage ( 10003 ) 
			];
		}
		
		// 读取商品的规格
		$specificationsArr = Specifications::getSpecificationsByGoodsId ( $goodsId );
		// 如果未找到规格
		if (empty ( $specificationsArr )) {
			return [ 
					'code' => 10004,
					'message' => $this->getMessage ( 10004 ) 
			];
		}
		foreach ( $specificationsArr as $specification ) {
			$specifications [$specification ['specifications']] = $specification ['price'];
		}
		return [ 
				"code" => 1,
				"name" => $name,
				"imgUrls" => $imgUrls,
				"specifications" => $specifications,
				"remarks" => $remarks
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