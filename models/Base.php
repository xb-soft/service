<?php
namespace smc\models;

use \ReflectionClass;
use \ReflectionMethod;

use smc\traits\Magic as MagicTrait;
use smc\traits\Data as DataTrait;

class Base {
	
	use MagicTrait, DataTrait;
	
	const PACKAGE_FLAG = 0x5842;
	/*
	 * 数据包类型长度 32bit
	 */
	const PACKAGE_TYPE_LEN = 2 << 4;
	/*
	 * 数据包命令长度 64bit
	 */
	const PACKAGE_CMD_LEN = 2 << 5;
	/*
	 * 数据包包体内容长度 32bit
	 */
	const PACKAGE_CONTENT_LEN = 2 << 4;
	
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

	public static function init($param, $components) {
		static::$param = $param;
		static::$components = $components;
		
		return new self;
	}
}