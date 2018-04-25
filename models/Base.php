<?php
/**
 * base model package
 *
 * @category php
 * @package service.smc.models
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace smc\models;

/*
 * import reflect
 */
use \ReflectionClass;
use \ReflectionMethod;

/*
 * import trait
 */
use smc\traits\Magic as MagicTrait;
use smc\traits\Data as DataTrait;

class Base {
	
	use MagicTrait, DataTrait;
	
	public static $param = [];
	public static $components = [];
	
	public function __construct() {
		$server = static::$components['components']['smc'];
		$this->register('redis', function () use ($server) {
			$redis = new $server['class'];
			$redis->connect($server['host'], $server['port']);
			$redis->auth($server['auth']);
			$redis->select($server['db']);
			return $redis;
		});
	}

	/**
	 * 加载配置文件
	 *
	 * @param array $param config/param-env.php
	 * @param array $components config/main-env.php
	 *
	 * @return self instance
	 */
	public static function init(array $param, array $components) {
		static::$param = $param;
		static::$components = $components;
		
		return new self;
	}
}