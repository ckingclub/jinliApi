<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class Shop extends ActiveRecord {
	const POSITION_LIST = 0;
	const POSITION_BANNER = 1;
	const POSITION_RECOMMEND = 2;
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['type','deleted','position','updated_at','created_at'], 'integer'],
			[['goodsId', 'name', 'remarks'], 'string'],
			[['priceUpLimit','priceLowLimit'], 'double']
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
	 * 获取banner商品
	 */
	public static function getBannerGoods(){
		$banners = static::find()->select([
						'goodsId',
						'name',
				])->where([
					'position'=>static::POSITION_BANNER,
					'deleted'=>0
				])->asArray()->all();
		return $banners;
	} 
	
	/**
	 * 获取推荐商品列表
	 */
	public static function getRecommendGoods(){
		$recommends = static::find()->select([
							'goodsId',
							'name',
							'priceLowLimit',
							'priceUpLimit'
				])->where([
					 'position' => static::POSITION_RECOMMEND,
					 'deleted' => 0
				])->asArray ()->all ();
		return $recommends;
	}
	
	/**
	 * 获取通过goodsId获取商品的名字状态等信息
	 */
	public static function getGoodsInfoByGoodsId($goodsId) {
		$goods = static::find ()->where ( [ 
				'goodsId' => $goodsId 
		] )->asArray ()->one ();
		if (empty ( $goods )) {
			return [ ];
		}
		return $goods;
	}
	
	/**
	 * 获取分类商品信息
	 */
	public static function getGoodsListByType($type){
		$goods = static::find()->where(['type' => $type,'deleted' => 0])->asArray()->all();
		if( empty($goods) ){
			return [];
		}
		return $goods;
	}
}