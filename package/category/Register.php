<?php
/**
 * category package
 *
 * @category php
 * @package service.smc.package
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package\category;

use package\Category;

use package\command\Type;
use package\command\Cmd;

class Register extends Category {
	
	public function getCurrentType() {
		Type::$current = Type::MESSAGE_REGISTER_TYPE;
		return Type::$current;
	}
	
	public function getCurrentCmd() {
		Cmd::$current = Cmd::SERVICE_REGISTER;
		return Cmd::$current;
	}
	
	public function getCurrentLen() {
		return strlen($this->_data->currentContent);
	}

	public function getRegister($service) {
		$this->_data->currentContent = $this->encode($service);
		$this->_data->header = [
			'messageType' => Type::MESSAGE_REGISTER_TYPE,
			'messageCmd' => Cmd::SERVICE_REGISTER,
			'messageLen' => strlen($this->_data->currentContent),
		];
		
		$this->_data->content = [
			'messageContent' => $this->_data->currentContent,
		];
		
		return $this->_buildPackage();
	}
	
	public function parserBin($bin) {
		return $this->_parsePackage($bin);
	}
}