<?php
namespace smc\models;

use \Swoole\Client as SwooleClient;

use smc\models\Base as BaseModel;

class Client extends BaseModel {
	
	const CONNECT_TIMEOUT = 0.5;
	
	private function _parseSockType($type) {
		switch ($type) {
			case 'tcp':
			case 0:
				$sockType = SWOOLE_SOCK_TCP;
				break;
			case 'udp':
			case 1:
				$sockType = SWOOLE_SOCK_UDP;
				break;
		}
		return $sockType;
	}
	
	public function sendPackage($server, $bin, $object) {
		$sockType = $this->_parseSockType($server['type']);
		$client = new SwooleClient($sockType);
		if ($client->connect($server['host'], $server['port'], self::CONNECT_TIMEOUT)) {
			$client->send($bin);
			
			$reply = $client->recv();
			
			if (false === $object->verifyPackage($reply, $object->rules)) {
				return false;
			}
			return true;
		}
		return false;
	}
}