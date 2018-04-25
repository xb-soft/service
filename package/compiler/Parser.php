<?php
/**
 * compiler package
 *
 * @category php
 * @package service.smc.package
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package\compiler;

use Google\Protobuf\Internal\Message as GoogleProtobufMessage;

use package\Compiler;

class Parser extends Compiler {

	/**
	 * 解析数据包头
	 *
	 * @param GoogleProtobufMessage $headers
	 * @param StdClass $container 引用传递
	 *
	 * @return void
	 */
	private function _parseHeader($headers, \StdClass & $container) {
		$getter = [];
		foreach ($headers as $header) {
			$reflection = new \ReflectionClass($header);
			foreach ($reflection->getMethods() as $methods) {
				$reflectMethod = new \ReflectionMethod($methods->class, $methods->name);
				if ($reflectMethod->isUserDefined()) {
					if (preg_match('/^get[\w]+/i', $methods->name)) {
						array_push($getter, $methods);
					}
				}
			}
			foreach ($getter as $method) {
				$property = lcfirst(str_replace('getMessage', '', $method->name));
				$container->$property = $header->{$method->name}();
			}
		}
	}
	
	/**
	 * 解析数据包
	 *
	 * @param string $bin bin data
	 * @param GoogleProtobufMessage $proto
	 *
	 * @return StdClass
	 */
	public function parsePackage($bin, GoogleProtobufMessage $proto) {
		$proto->clear();
		$reflection = new \ReflectionClass($proto);
		$this->_getProtoMethods($reflection);
		
		$package = new \StdClass;
		$proto->mergeFromString($bin);
		foreach ($this->getter as $getter) {
			if (self::GETTER_HEADER == $getter->name) {
				$this->_parseHeader($proto->getHeader(), $package);
			} else {
				$property = lcfirst(str_replace('getMessage', '', $getter->name));
				$package->$property = $proto->{$getter->name}();
			}
		}
		return $package;
	}
}