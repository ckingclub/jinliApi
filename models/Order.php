<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class Order extends ActiveRecord {
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
}