<?php
namespace smc\modules;

use \ReflectionClass;
use \ReflectionMethod;

use smc\traits\Magic as MagicTrait;
use smc\traits\Data as DataTrait;

use smc\models\Base as BaseModel;

class Base {
	
	use MagicTrait, DataTrait;
	
	public static $param = [];
	public static $components = [];
	
	public function __construct() {
		$this->register('models', function () {
			return call_user_func(['smc\\models\\Base', 'init'], static::$param, static::$components);
		});
	}
	
	public static function init($config) {
		static::$param = $config['param'];
		static::$components = $config['components'];
		
		return new self;
	}
	
	public function getBindMethods() {
		$reflection = new ReflectionClass($this);
		$methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
		foreach ($methods as $item) {
			if (preg_match('/^on([\w]+)/i', $item->name, $match)) {
				yield strtolower($match[1]) => $reflection->getMethod($item->name)->getClosure($this);
			}
		}
	}
}