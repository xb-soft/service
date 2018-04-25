<?php
namespace smc\controller;
use \Swoole\Server;

use smc\modules\Gateway as GatewayModule;

class GatewayController extends BaseController {
	
	public function init() {
		$this->modules->register('gateway', function () {
			return new GatewayModule;
		});
	}
	
	/**
	 * SMC gateway service
	 */
	public function actionUpstream() {
		$server = new Server($this->app['param']['upstream']['host'], $this->app['param']['upstream']['port'], $this->app['param']['upstream']['mode'], $this->app['param']['upstream']['type']);
		foreach ($this->modules->gateway->getBindMethods() as $name => $callback) {
			$server->on($name, $callback);
		}
		$server->set($this->app['param']['upstream']['setting']);
		$server->start();
	}
}