<?php
namespace smc\models;

use smc\models\Base as BaseModel;

use smc\traits\Data as DataTrait;

class Package extends BaseModel {
	
	use DataTrait;
	
	public $getter = [];
	public $setter = [];
	
	const SETTER_HEADER = 'setHeader';
	const GETTER_HEADER = 'getHeader';
	
	protected function _getProtoMethods(\ReflectionClass $reflection) {
		foreach ($reflection->getMethods() as $methods) {
			$reflectMethod = new \ReflectionMethod($methods->class, $methods->name);
			if ($reflectMethod->isUserDefined()) {
				if (preg_match('/^get[\w]+/i', $methods->name)) {
					array_push($this->getter, $methods);
				} else if (preg_match('/^set[\w]+/i', $methods->name)) {
					array_push($this->setter, $methods);
				}
			}
		}
	}
}