<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class Order extends ActiveRecord {
	const STATUS_NORMAL = 0;//未支付
	const STATUS_HAS_TO_PAY = 1;//已支付
	const STATUS_HAS_BEEN_CANCLE = 2;//被取消
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['status','updated_at','created_at'], 'integer'],
			[['orderId', 'openId'], 'string'],
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
	
	public static function createOrderSn($openId){
		$orderSn = 'JLOR'.date('Ymd').substr($openId, -4);
		$random = rand(1000, 10000);
		while(true){
			$order = static::find()->where(['orderId'=>$orderSn.$random])->one();
			if($order === null){
				$orderSn .= $random;
				break;
			}
			$random += 1;
		}
		return $orderSn;
	}
	
	public static function getOrderListByOpenId($openId){
		$list = static::find()->select(['orderId','price','created_at'])->where(['openId'=>$openId])->asArray()->all();
		if(empty($list)){
			return [];
		}
		return $list;
	}
}