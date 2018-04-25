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

class Upstream extends Category {

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

	public function getUpstream($service) {
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
	
	public function setUpstream($content) {
		$this->_data->currentContent = $content;
		$this->_data->header = [
			'messageType' => Type::MESSAGE_QUERY_TYPE,
			'messageCmd' => Cmd::SERVICE_QUERY,
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
	
	public function query(\StdClass $service, \Redis $redis) {
		$server = $redis->smembers($service->content);
		$hit = array_rand($server);
		return $this->setUpstream($server[$hit]);
	}
}