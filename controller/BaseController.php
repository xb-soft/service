<?php
namespace smc\controller;

use smc\traits\Magic as MagicTrait;

use smc\modules\Base as BaseModule;

class BaseController {
	
	public $app = [];
	
	use MagicTrait;
	
	public function __construct($config) {
		$this->app = $config;
		
		$this->register('modules', function () {
			return call_user_func(['smc\\modules\\Base', 'init'], $this->app);
		});
		
		$this->init();
	}
	
	public function init() {
		//empty
	}
}