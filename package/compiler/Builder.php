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

class Builder extends Compiler {
	
	/**
	 * 生成package键值对
	 *
	 * @param array $data
	 */
	private function _parseData(array $data) {
		foreach ($data as $key => $value) {
			yield $key => $value;
		}
	}
	
	/**
	 * 构造数据包
	 *
	 * @param array $data
	 * @param GoogleProtobufMessage $proto
	 *
	 * @return GoogleProtobufMessage
	 */
	public function buildPackage(array $data, GoogleProtobufMessage $proto) {
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
	
	/**
	 * 序列化数据包
	 *
	 * @param GoogleProtobufMessage $proto
	 *
	 * @return bin string
	 */
	public function serialize(GoogleProtobufMessage $proto) {
		return $proto->serializeToString();
	}
}