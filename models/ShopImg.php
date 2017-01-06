<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
class ShopImg extends ActiveRecord {
	/**
	 * @inheritdoc
	 */
	const TYPE_BANNER = 1;
	const TYPE_BIG_SHOW = 0;
	public function rules()
	{
		return [
			[['type','master','deleted','updated_at','created_at'], 'integer'],
			[['goodsId', 'url'], 'string'],
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
	 * 通过goodsId获取首页商品展示用的图片
	 */
	public static function getHomeImgUrlsByGoodsId($goodsId,$banner=false){
		if($banner){
			$img = static::find()->select(['url'])->where([
				'goodsId' => $goodsId,
				'type' => static::TYPE_BANNER,
				'deleted' => 0
			])->asArray()->one();
		} else {
			$img = static::find()->select(['thumbnail'])->where([
					'goodsId' => $goodsId,
					'type' => static::TYPE_BIG_SHOW,
					'master' => 1,
					'deleted' => 0
			])->asArray()->one();
		}
		if(!empty($img)){
			return current($img);
		} else {
			return "";
		}
	}
	
	/**
	 * 通过goodsId找到商品详情中用于展示的图片
	 */
	public static function getBigImgUrlsByGoodsId($goodsId){
		$imgsWithKey = static::find()->select(['url'])->where([
					'goodsId' => $goodsId,
					'type' => static::TYPE_BIG_SHOW,
					'deleted' => 0
				])->asArray()->all();
		if(empty($imgsWithKey)){
			return [];
		}
		$imgs = ArrayHelper::getColumn($imgsWithKey, 'url');
		return $imgs; 
	}
}