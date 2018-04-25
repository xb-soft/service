<?php
/**
 * client
 *
 * @category php
 * @package service.smc.models
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace smc\models;

/*
 * import swoole client
 */
use \Swoole\Client as SwooleClient;

class Client {
	
	const CONNECT_TIMEOUT = 0.5;
	
	/**
	 * 解析sock类型
	 *
	 * @param string|int $type
	 *
	 * @return swoole const
	 */
	private function _parseSockType($type) {
		switch ($type) {
			case 'tcp':
			case 1:
				$sockType = SWOOLE_SOCK_TCP;
				break;
			case 'udp':
			case 2:
				$sockType = SWOOLE_SOCK_UDP;
				break;
		}
		return $sockType;
	}
	
	/**
	 * 发送数据包
	 *
	 * @param array $server
	 * @param string $bin bin data
	 * @param todo
	 *
	 * @return boolean
	 */
	public function sendPackage($server, $bin, \package\Category $category) {
		$sockType = $this->_parseSockType($server['type']);
		$client = new SwooleClient($sockType);
		if ($client->connect($server['host'], $server['port'], self::CONNECT_TIMEOUT)) {
			$client->send($bin);
			
			$reply = $client->recv();
			
			$package = $category->parserBin($reply);
			$rules = [
				$category->currentType,
				$category->currentCmd,
				$package->len,
				strlen($package->content),
			];
			if (false === $category->verifier->verifyPackage($package, ...$rules)) {
				return false;
			}
			return true;
		}
		return false;
	}
}