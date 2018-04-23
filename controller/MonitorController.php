<?php
namespace smc\controller;
use \Swoole\Server;

use smc\modules\Monitor as MonitorModule;

class MonitorController extends BaseController {
	
	public function init() {
		$this->modules->register('monitor', function () {
			return new MonitorModule;
		});
	}
	
	/**
	 * SMC kernel service
	 */
	public function actionKernel() {
		$server = new Server($this->app['param']['kernel']['host'], $this->app['param']['kernel']['port'], $this->app['param']['kernel']['mode'], $this->app['param']['kernel']['type']);
		foreach ($this->modules->monitor->getBindMethods() as $name => $callback) {
			$server->on($name, $callback);
		}
		$server->set($this->app['param']['kernel']['setting']);
		$server->start();
	}
}