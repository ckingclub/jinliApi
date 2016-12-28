<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class User extends ActiveRecord {
	/**
	 * @inheritdoc
	 */
// 	public  $openId;
// 	public  $nickName;
// 	public  $country;
// 	public  $province;
// 	public  $city;
// 	public  $avatarUrl;
// 	public  $gender;
// 	public  $language;
// 	public  $created_at;
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['gender','created_at'], 'integer'],
			[['openId', 'nickName', 'country', 'province', 'city', 'avatarUrl', 'language'], 'string']
		];
	}
	
	public function behaviors()
	{
		return [ 
				[ 
						'class' => TimestampBehavior::className (),
						'attributes' => [ 
								ActiveRecord::EVENT_BEFORE_VALIDATE => [ 
										'created_at' 
								] 
						] 
				] 
		];
	}
}
