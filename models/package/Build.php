<?php
namespace smc\models\package;

use \Google\Protobuf\Internal\Message as GoogleProtobufMessage;

use smc\models\Package;

class Build extends Package {
	
	private function _parseData($data) {
		foreach ($data as $key => $value) {
			yield $key => $value;
		}
	}
	
	public function buildPackage($data, GoogleProtobufMessage $proto) {
		$proto->clear();
		$reflection = new \ReflectionClass($proto);
		$this->_getProtoMethods($reflection);
		
		foreach ($this->setter as $setter) {
			if (self::SETTER_HEADER == $setter->name) {
				continue 1;
			}
			foreach ($data as $key => $item) {
				if ('set' . ucfirst($key) == $setter->name) {
					call_user_func([$proto, $setter->name], $item);
					break 1;
				}
			}
		}
		return $proto;
	}
	
	public function serialize(GoogleProtobufMessage $proto) {
		return $proto->serializeToString();
	}
}