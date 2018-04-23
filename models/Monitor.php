<?php
namespace smc\models;

use smc\models\Base as BaseModel;
use smc\models\Client as ClientModel;

use smc\models\package\Command;

use smc\models\package\Build as BuildPackage;
use smc\models\package\Parse as ParsePackage;
use smc\models\package\Verify as VerifyPackage;


use Proto\Common\Header as HeaderProto;
use Proto\Monitor\Kernel as KernelProto;

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
	
	private function _parsePackage($bin) {
		$parser = new ParsePackage;
		$kernelProto = new KernelProto;
		
		return $parser->parsePackage($bin, $kernelProto);
	}
	
	private function _verifyPackage($package, $rules = []) {
		$verifier = new VerifyPackage;
		return $verifier->verifyPackage($package, ...$rules);
	}
	
	private function _buildPackage(array $data) {
		$builder = new BuildPackage;
		$headerProto = new HeaderProto;
		$kernelProto = new KernelProto;
		
		/*
		 * 打包header
		 */
		$header = $builder->buildPackage($data['header'], $headerProto);
		/*
		 * 打包content
		 */
		$proto = $builder->buildPackage($data['content'], $kernelProto);
		$proto->getHeader()[] = $header;
		
		return $builder->serialize($proto);
	}
	
	private function _buildPackageType($type = 'heartbeat') {
		/*
		 * 首位保留为0，也就是说32bit中只有31bit有效
		 * 统一把数据包头唯一标识放在前16位
		 */
		$packageFlag = self::PACKAGE_FLAG << 16;
		switch ($type) {
			case 'heartbeat':
				/*
				 * 第17位开始为心跳包
				 */
				$messageType = $packageFlag + (1 << 15);
				break;
			case 'register':
				/*
				 * 第18位开始为服务注册数据包
				 */
				$messageType = $packageFlag + (1 << 14);
				break;
		}
		return $messageType;
	}
	
	public function heartbeat($service, $server) {
		if (false === is_string($service)) {
			return false;
		}
		
		if (false === is_array($server)) {
			return false;
		}
		$this->serviceName = $service;
		$this->serverData = $server;
		/*
		 * header格式
		 * type|cmd|length
		 * body 格式
		 * content
		 */
		$content = '';	//心跳包无包体
		$messageType = $this->_buildPackageType();
		$contentLength = strlen($content);
		$data = [
			'header' => [
				'messageType' => $messageType,
				'messageCmd' => Command::HEARTBEAT_PING,
				'messageLen' => $contentLength,
			],
			'content' => [
				'messageContent' => $content,
			],
		];
		$bin = $this->_buildPackage($data);
		
		$client = new ClientModel;
		$this->rules = [$messageType, Command::HEARTBEAT_PONG, 0, $contentLength];
		return $client->sendPackage($this->serverData, $bin, $this);
	}
	
	public function verifyPackage($bin, $rules = []) {
		$package = $this->_parsePackage($bin);
		return $this->_verifyPackage($package, $rules);
	}
	
	public function remove($service, $server) {
		if ($this->redis->sremove($service, $this->encode($server))) {
			return true;
		}
		return false;
	}
	
	public function registerServer($bin) {
		$package = $this->_parsePackage($bin);
		$length = $package->len;
		$contentLength = strlen($package->content);
		$rules = [$this->_buildPackageType('register'), Command::SERVICE_REGISTER, $length, $contentLength];
		if (true === $this->_verifyPackage($package, $rules)) {
			$server = $this->decode($package->content);
			$service = $server['name'];
			unset($server['name']);
			
			$this->redis->sadd($service, $this->encode($server));
			$this->reloadData = [$service, $server];
			return true;
		}
		return false;
	}
	
	public function buildPackage($type, $cmd, $len, $content) {
		$data = [
			'header' => [
				'messageType' => $this->_buildPackageType($type),
				'messageCmd' => $cmd,
				'messageLen' => $len,
			],
			'content' => [
				'messageContent' => $content,
			],
		];
		return $this->_buildPackage($data);
	}
}