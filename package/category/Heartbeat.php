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

class Heartbeat extends Category {

	public function getCurrentType() {
		Type::$current = Type::MESSAGE_HEARTBEAT_TYPE;
		return Type::$current;
	}
	
	public function getCurrentCmd() {
		Cmd::$current = Cmd::HEARTBEAT_PONG;
		return Cmd::$current;
	}
	
	public function getCurrentLen() {
		return strlen($this->_data->currentContent);
	}

	public function getHeartbeat() {
		$this->_data->currentContent = '';
		$this->_data->header = [
			'messageType' => Type::MESSAGE_HEARTBEAT_TYPE,
			'messageCmd' => Cmd::HEARTBEAT_PING,
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
	
	public function setHeartbeat() {
		$this->_data->currentContent = '';
		$this->_data->header = [
			'messageType' => Type::MESSAGE_HEARTBEAT_TYPE,
			'messageCmd' => Cmd::HEARTBEAT_PONG,
			'messageLen' => strlen($this->_data->currentContent),
		];
		
		$this->_data->content = [
			'messageContent' => $this->_data->currentContent,
		];
		return $this->_buildPackage();
	}
	
	public function isHeartbeat($data) {
		if (Type::MESSAGE_HEARTBEAT_TYPE === $data->type) {
			return true;
		}
		return false;
	}
}