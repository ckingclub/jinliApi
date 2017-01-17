<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class ShoppingCart extends ActiveRecord {
	const STATUS_NORMAL = 0;
	const STATUS_HAS_TO_ORDER = 1;
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['status','updated_at','created_at'], 'integer'],
			[['goodsId', 'openId','orderId','specifications'], 'string'],
			[['price'], 'double']
		];
	}
	
	public function behaviors()
	{
		return [ 
				[ 
						'class' => TimestampBehavior::className (),
						'attributes' => [ 
								ActiveRecord::EVENT_BEFORE_VALIDATE => [ 
										'updated_at'
								] 
						] 
				] 
		];
	}
	/**
	 * 通过openId获取购物车列表
	 */
	public static function getShoppingCartList($openId){
		$shoppingCartList = static::find()->select(['goodsId','specifications','num','name','price'])->where(['openId'=>$openId,'status'=>self::STATUS_NORMAL])->Where(['and','num>0'])->asArray()->all();
		if(empty($shoppingCartList)){
			return [];
		}
		return $shoppingCartList; 
	}
	
	/**
	 * 通过openId,goddsId找到购物车中
	 */
	public static function getShoppingCartItem($openId,$goodsId){
		$item = static::find()->where(['openId'=>$openId,'goodsId'=>$goodsId,'status'=>self::STATUS_NORMAL])->one();
		if($item === null){
			return [];
		}
		return $item;
    }
    
    /**
     * 通过orderId获取购物车列表，无论状态
     */
    public static function getListByOrderId($orderId){
    	$list = static::find()->where(['orderId'=>$orderId])->asArray()->all();
    	if(empty($list)){
    		return [];
    	}
    	return $list;
    }
}