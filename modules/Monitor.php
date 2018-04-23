<?php
namespace smc\modules;

use smc\modules\Base as BaseModule;

use smc\models\Monitor as MonitorModel;
use smc\models\package\Command;

class Monitor extends BaseModule {
	
	const HEARTBEAT_TICK_TIME = 30000;
	const MAX_RETRY_TIMES = 3;
	
	public function __construct() {
		parent::__construct();

		/*
		 * 仅实例化一次
		 */
		$this->models->register('monitor', function () {
			return new MonitorModel;
		});
	}
	
	public function onConnect($server, $fd, $reactorId) {
		//empty
	}
	
	public function onWorkerStart($server, $workerId) {
		if (0 == $workerId) {
			foreach ($this->models->monitor->getServiceData() as $service) {
				$server->task($service);
			}
		}
	}

	public function onReceive($server, $fd, $reactorId, $data) {
		$data = $this->models->monitor->registerServer($data);
		if (false === $data) {
			$reply = $this->error('-100007');
		} else {
			$reply = $this->success(true);
		}
		$bin = $this->models->monitor->buildPackage('register', Command::SERVICE_REGISTER, strlen($reply), $reply);
		/*
		 * reload new register service
		 */
		$server->send($fd, $bin);
		if (false !== $data) {
			$server->task($this->models->monitor->reloadData);
		}
	}

	public function onTask($server, $taskId, $workerId, $data) {
		$server->tick(self::HEARTBEAT_TICK_TIME, function ($tickId) use ($server, $data) {
			$i = 0;
			retry : {
				$sleep = 5 << $i;
				if (0 == $i) {
					goto connect;
				}
				sleep($sleep);
			}
			connect: {
				if ($i < self::MAX_RETRY_TIMES) {
					if (false === $this->models->monitor->heartbeat(...$data)) {
						$i++;
						goto retry;
					}
				} else {
					if (true === $this->models->monitor->remove(...$data)) {
						$server->clearTimer($tickId);
					} else {
					}
				}
			}
		});
	}

	public function onFinish($server, $taskId, $data) {
		//empty
	}

	public function onClose($server, $fd, $reactorId) {
		//empty
	}
}