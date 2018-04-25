<?php
/**
 * compiler package
 *
 * @category php
 * @package service.smc.package
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package;

use smc\traits\Data as DataTrait;

class Compiler {
	
	use DataTrait;
	
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