<?php
namespace app\controllers;

use \Yii;
use yii\base\Controller;
use yii\base\InvalidRouteException;
use yii\base\Exception;
class ApiController extends Controller{
	private $getParams;
	private $responseBool = false;
	/**
	 * 重写runAction方法以处理post参数
	 */
	public function runAction($id, $params = [])
	{
		$action = $this->createAction($id);
		if ($action === null) {
			throw new InvalidRouteException('Unable to resolve the request: ' . $this->getUniqueId() . '/' . $id);
		}
	
		Yii::trace('Route to run: ' . $action->getUniqueId(), __METHOD__);
	
		if (Yii::$app->requestedAction === null) {
			Yii::$app->requestedAction = $action;
		}
	
		$oldAction = $this->action;
		$this->action = $action;
	
		$modules = [];
		$runAction = true;
	
		// call beforeAction on modules
		foreach ($this->getModules() as $module) {
			if ($module->beforeAction($action)) {
				array_unshift($modules, $module);
			} else {
				$runAction = false;
				break;
			}
		}
		
		$result = null;
		if ($runAction && $this->beforeAction($action)) {
			
			/**
			 * 做参数处理
			 */
			if(!in_array($action->controller->className(),
					[
						'app\controllers\LoginController',
						'app\controllers\UserController',
						'app\controllers\TunnelController'
					])
			){
				//保存get参数
				$this->getParams = $params;
				//从post数据中读取参数
				$params =  $this->readParamFromPost();
				//是否做返回结果处理
				$this->responseBool = true;
			}
			
			// run the action
			$result = $action->runWithParams($params);
	
			$result = $this->afterAction($action, $result);
	
			// call afterAction on modules
			foreach ($modules as $module) {
				/* @var $module Module */
				$result = $module->afterAction($action, $result);
			}
		}
	
		$this->action = $oldAction;
	
		return $result;
	}
	
	/**
	 * 从post数据中读取参数,解密
	 */
	private function readParamFromPost(){
		$requests = file_get_contents ( 'php://input' );
		if(!empty($requests)){
			$reqData = json_decode ( $requests, true );
			/**
		 	* 检查数据格式
		 	*/
			if (json_last_error ()) {
				throw new Exception ( 'data error', 20001 );
			}
			return $reqData;
		}
		return [];
	}
	
	/**
	 *重写，以绑定参数
	 */
	public function bindActionParams($action, $params){
		if(empty($params)){
			return [];
		}
		return $params;
	}
	
	/**
	 *返回结果加密
	 */
	public function afterAction($action, $result){
		if($this->responseBool){
			$result = parent::afterAction($action, $result);
			return json_encode($result);
		}
		return parent::afterAction($action, $result);
	}
}