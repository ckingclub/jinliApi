<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class Shop extends ActiveRecord {
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
}