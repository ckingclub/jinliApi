<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class Specifications extends ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['status','updated_at','created_at'], 'integer'],
			[['goodsId', 'specifications'], 'string'],
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
	 * 通过goodsId读取商品规格
	 */
	public static function getSpecificationsByGoodsId($goodsId){
		$specifications = static::find()->where([
						"goodsId" => $goodsId,
						"status" => 0
					])->asArray()->all();
		if(empty($specifications)){
			return [];
		}
		return $specifications; 
	}
	
	/**
	 * 通过goodsId与specifications检查specifications是否存在
	 */
	public static function findPiceOfSpecifications($goodsId,$specifications){
		$price = static::find()->select(['price'])->where(['goodsId'=>$goodsId,'specifications'=>$specifications,'status'=>0])->asArray()->one();
		if(empty($price)){
			return [];
		}
		return $price;
	}
}