<?php
/**
 * monitor model package
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

use package\category\Heartbeat;
use package\category\Register;

class Monitor extends BaseModel {
	
	public $serviceName = '';
	public $serverData = [];
	
	public $rules = [];
	
	public $reloadData = [];
	
	public function getServiceData() {
		$keys = $this->redis->keys('*');
		foreach ($keys as $key) {
			$data = $this->redis->smembers($key);
			foreach ($data as $service) {
				yield [$key, $this->decode($service)];
			}
		}
	}

	public function heartbeat($serviceName, $serviceData) {
		$client = new Client;
		$heartbeat = new Heartbeat;
		$bin = $heartbeat->getHeartbeat();
		return $client->sendPackage($serviceData, $bin, $heartbeat);
	}
	
	public function remove($serviceName, $serviceData) {
		if ($this->redis->sremove($serviceName, $this->encode($serviceData))) {
			return true;
		}
		return false;
	}
	
	public function registerService($bin) {
		$register = new Register;
		$package = $register->parserBin($bin);
		
		$rules = [
			$register->currentType,
			$register->currentCmd,
			$package->len,
			strlen($package->content),
		];
		if (true === $register->verifier->verifyPackage($package, ...$rules)) {
			$content = $this->decode($package->content);
			$service = $content['name'];
			unset($content['name']);
			$this->redis->sadd($service, $this->encode($content));
			$this->reloadData = [$service, $content];
			return true;
		}
		return false;
	}
}