<?php

namespace app\controllers;

use app\controllers\ApiController;
use app\models\User;
use app\models\Shop;
use app\models\Specifications;
use app\models\ShoppingCart;
use app\models\ShopImg;
use app\models\Order;

class OrderController extends ApiController {
	/**
	 * 生成订单，保存至数据库，返回订单号
	 * 这部分非常重要，中间出错，影响比较大
	 * 所以这里做了严格的验证并在必要的地方添加了事务机制与回滚
	 */
	public function actionAdd($openId, $orderDetatil, $fromShoppingCart = false) {
		/**
		 * 参数验证部分
		 */
		// 检查openId是否存在
		$userBool = User::checkUserIn ( $openId );
		if (! $userBool) {
			return [ 
					'code' => 20001,
					'message' => $this->getMessage ( 20001 ) 
			];
		}
		if (empty ( $orderDetatil )) {
			return [ 
					'code' => 20018,
					'message' => $this->getMessage ( 20018 ) 
			];
		}
		// 检查orderDetail
		foreach ( $orderDetatil as $orderItem ) {
			try {
				// 检查goodsId是否存在
				$goodsBool = Shop::checkGoods ( $orderItem ['goodsId'] );
				if (! $goodsBool) {
					return [ 
							'code' => 20002,
							'message' => $this->getMessage ( 20002 ) 
					];
				}
				// 检查规格是否存在
				$specifications = Specifications::findPiceOfSpecifications ( $orderItem ['goodsId'], $orderItem ['specifications'] );
				if (empty ( $specifications )) {
					return [ 
							'code' => 20003,
							'message' => $this->getMessage ( 20003 ) 
					];
				}
				// 检查number是否合法
				if ($orderItem ['num'] <= 0) {
					return [ 
							'code' => 20011,
							'message' => $this->getMessage ( 20011 ) 
					];
				}
				// 检查price是否正确
				if ($specifications ['price'] * $orderItem ['num'] != $orderItem ['price']) {
					return [ 
							'code' => 20010,
							'message' => $this->getMessage ( 20010 ) 
					];
				}
			} catch ( \Exception $e ) {
				return [ 
						'code' => 20012,
						'message' => $this->getMessage ( 20012 ),
						'error' => $e->getMessage () 
				];
			}
		}
		
		/**
		 * 业务逻辑部分
		 */
		// 函数内部使用函数闭包
		$addShoppingCart = function ($openId, $orderItem, $orderSn, $name) {
			$shoppingCart = new ShoppingCart ();
			$shoppingCart->goodsId = $orderItem ['goodsId'];
			$shoppingCart->openId = $openId;
			$shoppingCart->specifications = $orderItem ['specifications'];
			$shoppingCart->price = $orderItem ['price'];
			$shoppingCart->status = ShoppingCart::STATUS_HAS_TO_ORDER;
			$shoppingCart->num = $orderItem ['num'];
			$shoppingCart->name = $name;
			$shoppingCart->orderId = $orderSn;
			$shoppingCart->created_at = time ();
			return $shoppingCart->save ();
		};
		
		$priceAll = 0;
		$saveBool = true;
		// 生成订单号
		$orderSn = Order::createOrderSn ( $openId );
		
		/**
		 * 生成订单相关的购物车信息的事务开始
		 */
		$transaction = \Yii::$app->db->beginTransaction ();
		// 如果不是从购物车过来的订单
		if (! $fromShoppingCart) {
			// 生成购物车信息，状态定位已生成订单
			foreach ( $orderDetatil as $orderItem ) {
				$goods = Shop::getGoodsInfoByGoodsId ( $orderItem ['goodsId'] );
				// 生成购物车信息
				$saveBool = ($saveBool && $addShoppingCart ( $openId, $orderItem, $orderSn, $goods ['name'] ));
				$priceAll += $orderItem ['price'];
			}
		} else { // 如果是从物车过来的订单
		         // 将对应的购物车状态改为已生成订单
			foreach ( $orderDetatil as $orderItem ) {
				$shoppingCart = ShoppingCart::getShoppingCartItem ( $openId, $orderItem ['goodsId'] );
				if (empty ( $shoppingCart )) {
					$goods = Shop::getGoodsInfoByGoodsId ( $orderItem ['goodsId'] );
					$saveBool = $addShoppingCart ( $openId, $orderItem, $orderSn, $goods ['name'] );
				} else {
					$shoppingCart->orderId = $orderSn;
					$shoppingCart->status = ShoppingCart::STATUS_HAS_TO_ORDER;
					$saveBool = $shoppingCart->save ();
					$priceAll += $orderItem ['price'];
				}
			}
		}
		if (! $saveBool) {
			// 事务回滚
			$transaction->rollBack ();
			return [ 
					'code' => 20017,
					'message' => $this->getMessage ( 20017 ) 
			];
		}
		// 生成订单信息，状态定为未支付
		$order = new Order ();
		$order->orderId = $orderSn;
		$order->openId = $openId;
		$order->status = Order::STATUS_NORMAL;
		$order->price = $priceAll;
		$order->created_at = time ();
		$saveBool = $order->save ();
		if ($saveBool) {
			// 事务提交
			$transaction->commit ();
			// 返回订单号等正确信息
			return [ 
					"code" => 1,
					"orderId" => $orderSn 
			];
		} else {
			// 事务回滚
			$transaction->rollBack ();
			return [ 
					"code" => 20013,
					"message" => $this->getMessage ( 20013 ) 
			];
		}
	}
	
	/**
	 * 取消订单
	 */
	public function actionCancleOrder($orderId) {
		$order = Order::find ()->where ( [ 
				'orderId' => $orderId 
		] )->one ();
		if ($order == null) {
			return [ 
					'code' => 20014,
					'message' => $this->getMessage ( 20014 ) 
			];
		}
		if ($order->status !== Order::STATUS_NORMAL) {
			return [ 
					'code' => 20015,
					'message' => $this->getMessage ( 20015 ) 
			];
		}
		$order->status = Order::STATUS_HAS_BEEN_CANCLE;
		if ($order->save ()) {
			return [ 
					"code" => 1 
			];
		} else {
			return [ 
					"code" => 20016,
					'message' => $this->getMessage ( 20016 ) 
			];
		}
	}
	
	/**
	 * 获取用户订单列表
	 */
	public function actionOrderList($openId) {
		// 检查openId是否存在
		$userBool = User::checkUserIn ( $openId );
		if (! $userBool) {
			return [ 
					'code' => 20001,
					'message' => $this->getMessage ( 20001 ) 
			];
		}
		// 从数据库中读取订单列表
		$list = Order::getOrderListByOpenId ( $openId );
		if (empty ( $list )) {
			return [ 
					'code' => 1,
					'orderList' => [ ] 
			];
		}
		// 循环读取每个订单的商品和图片
		foreach ( $list as $key => $item ) {
			$shoppingList = ShoppingCart::getListByOrderId ( $item ['orderId'] );
			foreach ( $shoppingList as $goodsItem ) {
				$list [$key] [$goodsItem ['goodsId']] [] = $goodsItem ['name'];
				$list [$key] [$goodsItem ['goodsId']] [] = ShopImg::getHomeImgUrlsByGoodsId ( $goodsItem ['goodsId'] );
			}
			$list [$key] ['created_at'] = date ( "YmdHis", $item ['created_at'] );
		}
		// 直接读数据库
		return [ 
				"code" => 1,
				"orderList" => $list 
		];
	}
	
	/**
	 * 获取订单详情
	 */
	public function actionOrderDetail($orderId) {
		$order = Order::find ()->where ( [ 
				'orderId' => $orderId 
		] )->one ();
		if ($order == null) {
			return [ 
					'code' => 20014,
					'message' => $this->getMessage ( 20014 ) 
			];
		}
		$orderDetail = [ ];
		$orderDetail ['price'] = $order->price;
		$shoppingList = ShoppingCart::getListByOrderId ( $order->orderId );
		foreach ( $shoppingList as $item ) {
			$temp = [ ];
			$temp ["goodsId"] = $item ["goodsId"];
			$temp ["name"] = $item ["name"];
			$temp ["specifications"] = $item ["specifications"];
			$temp ["num"] = $item ["num"];
			$temp ["price"] = $item ["price"];
			$temp ["imgUrl"] = ShopImg::getHomeImgUrlsByGoodsId ( $item ["goodsId"] );
			$orderDetail [] = $temp;
		}
		// 转义订单状态为string
		$status = '';
		switch ($order->status) {
			case Order::STATUS_NORMAL :
				$status = '未支付';
				break;
			case Order::STATUS_HAS_TO_PAY :
				$status = '已支付';
				break;
			case Order::STATUS_HAS_BEEN_CANCLE :
				$status = '已取消';
				break;
			default :
				$status = 'error';
		}
		// 直接读数据库
		return [ 
				"code" => 1,
				"status" => $status,
				"orderDetail" => $orderDetail 
		];
	}
	
	/**
	 * 将商品添加到购物车，返回是否成功
	 */
	public function actionAddShoppingCart($openId, $goodsId, $specifications, $num) {
		// 检查num是否合法
		if (! is_numeric ( $num ) || $num <= 0) {
			return [ 
					'code' => 20004,
					'message' => $this->getMessage ( 20004 ) 
			];
		}
		// 检查openId是否存在
		$userBool = User::checkUserIn ( $openId );
		if (! $userBool) {
			return [ 
					'code' => 20001,
					'message' => $this->getMessage ( 20001 ) 
			];
		}
		// 检查goodsId是否存在
		$goods = Shop::getGoodsInfoByGoodsId ( $goodsId );
		if (empty ( $goods )) {
			return [ 
					'code' => 20002,
					'message' => $this->getMessage ( 20002 ) 
			];
		}
		// 检查$specifications是否有效
		$specificationsPrice = Specifications::findPiceOfSpecifications ( $goodsId, $specifications );
		if (empty ( $specificationsPrice )) {
			return [ 
					'code' => 20003,
					'message' => $this->getMessage ( 20003 ) 
			];
		}
		// 检查购物车中是否已存在
		$shoppingCart = ShoppingCart::getShoppingCartItem ( $openId, $goodsId );
		if (empty ( $shoppingCart )) {
			// 插入购物车列表
			$shoppingCart = new ShoppingCart ();
			$shoppingCart->goodsId = $goodsId;
			$shoppingCart->openId = $openId;
			$shoppingCart->specifications = $specifications;
			$shoppingCart->price = ($specificationsPrice ['price'] * $num);
			$shoppingCart->status = 0;
			$shoppingCart->num = $num;
			$shoppingCart->name = $goods ['name'];
			$shoppingCart->created_at = time ();
			$saveBool = $shoppingCart->save ();
		} else {
			$shoppingCart->num += $num;
			$saveBool = $shoppingCart->save ();
		}
		if ($saveBool) {
			return [ 
					"code" => 1 
			];
		} else {
			return [ 
					"code" => 20005,
					"message" => $this->getMessage ( 20005 ) 
			];
		}
	}
	
	/**
	 * 获取购物车列表
	 */
	public function actionShoppingCart($openId) {
		// 检查openId是否存在
		$userBool = User::checkUserIn ( $openId );
		if (! $userBool) {
			return [ 
					'code' => 20001,
					'message' => $this->getMessage ( 20001 ) 
			];
		}
		// 直接读数据库
		$list = ShoppingCart::getShoppingCartList ( $openId );
		if (empty ( $list )) {
			return [ 
					'code' => 1,
					'shoppingCartList' => [ ] 
			];
		}
		foreach ( $list as $key => $item ) {
			$list [$key] ['imgUrl'] = ShopImg::getHomeImgUrlsByGoodsId ( $item ['goodsId'] );
		}
		return [ 
				"code" => 1,
				"shoppingCarList" => $list 
		];
	}
	
	/**
	 * 编辑购物车内容
	 */
	public function actionEditShoppingCart($openId, $goodsId, $option) {
		if (! is_numeric ( $option ) || $option == 0) {
			return [ 
					'code' => 20006,
					'message' => $this->getMessage ( 20006 ) 
			];
		}
		// 检查openId是否存在
		$userBool = User::checkUserIn ( $openId );
		if (! $userBool) {
			return [ 
					'code' => 20001,
					'message' => $this->getMessage ( 20001 ) 
			];
		}
		// 检查goodsId是否存在
		$goodsBool = Shop::checkGoods ( $goodsId );
		if (! $goodsBool) {
			return [ 
					'code' => 20002,
					'message' => $this->getMessage ( 20002 ) 
			];
		}
		// 增、删
		$shoppingCart = ShoppingCart::getShoppingCartItem ( $openId, $goodsId );
		if (empty ( $shoppingCart )) {
			return [ 
					"code" => 20007,
					"message" => $this->getMessage ( 20007 ) 
			];
		}
		$shoppingCart->num += $option;
		if ($shoppingCart->num < 0) {
			return [ 
					"code" => 20008,
					"message" => $this->getMessage ( 20008 ) 
			];
		}
		$specificationsPrice = Specifications::findPiceOfSpecifications ( $goodsId, $shoppingCart->specifications );
		if (empty ( $specificationsPrice )) {
			return [ 
					'code' => 20003,
					'message' => $this->getMessage ( 20003 ) 
			];
		}
		$shoppingCart->price = $specificationsPrice ['price'] * $shoppingCart->num;
		$saveBool = $shoppingCart->save ();
		if($saveBool){
			return [
				"code" => 1,
			];
		}else{
			return [
				"code" => 20009,
				"message" => $this->getMessage(20009)
			];
		}
	}
}