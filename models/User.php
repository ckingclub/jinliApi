<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
class User extends ActiveRecord {
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['gender','updated_at','created_at'], 'integer'],
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
										'updated_at'
								] 
						] 
				] 
		];
	}
	
	public static function addOrUpdateUser($openId,$data)
	{
		$user = static::find()->where(['openId'=>$openId])->one();
		if( $user == null){
			$user = new User();
			$user->created_at = time();
			$user->loginCount = 1;
		} else {
			$user->loginCount++;
		}
		$user->attributes = $data;
		$user->save();
	}
}