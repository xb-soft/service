<?php
namespace smc\traits;

trait Magic {
	
	public $container = [
		'cb' => [],
	];
	
	public function register($name, $callback) {
		$name = strtolower($name);
		if (is_callable($callback)) {
			$this->container['cb'][$name] = call_user_func($callback);
			return true;
		}
		return false;
	}
	
	public function bind($name, $callback) {
		$this->register($name, $callback);
	}
	
	public function unbind($name) {
		$name = strtolower($name);
		if (true === array_key_exists($name, $this->container['cb'])) {
			unset($this->container['cb'][$name]);
		}
		if (true === array_key_exists($name, $this->container)) {
			unset($this->container[$name]);
		}
	}
	
	public function __get($name) {
		$getter = 'get' . ucfirst($name);
		if (true === method_exists(get_class(), $getter)) {
			return $this->$getter();
		} else {
			$name = strtolower($name);
			if (true === array_key_exists($name, $this->container['cb'])) {
				return $this->container['cb'][$name];
			} else if (true === array_key_exists($name, $this->container)) {
				return $this->container[$name];
			}
		}
		return false;
	}
	
	public function __set($name, $value) {
		if (true === is_callable($value)) {
			$name = strtolower($name);
			$this->register($name, $value);
		} else {
			$setter = 'set' . ucfirst($name);
			if (true === method_exists($setter, get_class())) {
				$this->$setter($value);
			} else {
				$name = strtolower($name);
				$this->container[$name] = $value;
			}
		}
	}
}