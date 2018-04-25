<?php
namespace smc\modules;

use smc\modules\Base as BaseModule;

use smc\models\Gateway as GatewayModel;
use smc\models\package\Command;

class Gateway extends BaseModule {

	const MAX_RETRY_TIMES = 3;
	
	const CURRENT_SERVICE_NAME = 'gateway';
	
	public function __construct() {
		parent::__construct();

		/*
		 * 仅实例化一次
		 */
		$this->models->register('gateway', function () {
			return new GatewayModel;
		});
	}
	
	public function onConnect($server, $fd, $reactorId) {
		//empty
	}
	
	public function onWorkerStart($server, $workerId) {
		if (0 == $workerId) {
			/*
			 * 仅注册一次gateway服务
			 */
			$serviceData = [
				'host' => static::$param['upstream']['host'],
				'port' => static::$param['upstream']['port'],
				'type' => (SWOOLE_SOCK_TCP === static::$param['upstream']['type'] ? 'tcp' : 'udp'),
			];
			$this->models->gateway->registerMicroService(self::CURRENT_SERVICE_NAME, $serviceData);
		}
	}

	public function onReceive($server, $fd, $reactorId, $data) {
		$this->models->gateway->addTask($server, $fd, $data);
	}

	public function onTask($server, $taskId, $workerId, $data) {
		$bin = $this->models->gateway->query($data);
		$server->send($data->fd, $bin);
		return true;
	}

	public function onFinish($server, $taskId, $data) {
		$server->close($data->fd);
		return;
	}

	public function onClose($server, $fd, $reactorId) {
		return;
	}
}