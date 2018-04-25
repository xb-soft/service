<?php
/**
 * gateway model package
 *
 * @category php
 * @package service.smc.models
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace smc\models;

/*
 * import base model
 */
use smc\models\Base as BaseModel;
/*
 * import swoole client
 */
use smc\models\Client;

use package\category\Register;
use package\category\Upstream;
use package\category\Heartbeat;

class Gateway extends BaseModel {
	
	public $serviceName = '';
	public $serverData = [];
	
	public $rules = [];
	
	public function registerMicroService($serviceName, $serviceData) {
		$client = new Client;
		$server = [
			'host' => static::$param['kernel']['host'],
			'port' => static::$param['kernel']['port'],
			'type' => static::$param['kernel']['type'],
		];
		$service = array_merge(['name' => $serviceName], $serviceData);
		$register = new Register;
		$bin = $register->getRegister($service);
		return $client->sendPackage($server, $bin, $register);
	}
	
	public function parserBin($bin) {
		$upstream = new Upstream;
		return $upstream->parserBin($bin);
	}
	
	public function addTask($server, $fd, $bin) {
		$data = $this->parserBin($bin);
		$heartbeat = new Heartbeat;
		if (true === $heartbeat->isHeartbeat($data)) {
			$bin = $heartbeat->setHeartbeat();
			$server->send($fd, $bin);
		} else {
			$data->fd = $fd;
			$server->task($data);
		}
	}
	
	public function query($package) {
		$upstream = new Upstream;
		return $upstream->query($package, $this->redis);
	}
}