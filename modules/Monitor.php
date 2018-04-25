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
			echo "start monitor service now...\n";
			foreach ($this->models->monitor->getServiceData() as $service) {
				$server->task($service);
			}
		}
	}

	public function onReceive($server, $fd, $reactorId, $data) {
		echo "register service now...\n";
		if (true === $this->models->monitor->registerService($data)) {
			echo "register service success\n";
		} else {
			echo "register service fail\n";
		}

		/*
		 * reload new register service
		 */
		echo "reload monitor now...\n";
		$server->task($this->models->monitor->reloadData);
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
						echo "retry send heartbeat now...\n";
						$i++;
						goto retry;
					} else {
						return true;
					}
				} else {
					echo "remove service now...\n";
					$server->clearTimer($tickId);
					if (true === $this->models->monitor->remove(...$data)) {
						echo "remove service success\n";
					}
				}
			}
		});
	}

	public function onFinish($server, $taskId, $data) {
		echo "monitor success\n";
	}

	public function onClose($server, $fd, $reactorId) {
		echo "close connect\n";
	}
}