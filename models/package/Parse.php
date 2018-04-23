<?php
namespace smc\models\package;

use \Google\Protobuf\Internal\Message as GoogleProtobufMessage;

use smc\models\Package;

class Parse extends Package {
	
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