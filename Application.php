<?php
/**
 * service application
 *
 * @category php
 * @package service
 * @author enze.wei <[enzewei@gmail.com]>
 * @copyright 2018
 * @version 1.0.0
 */
namespace smc;

use ReflectionClass;
use ReflectionMethod;

class Application {
	
	const SERVICE_SUFFIX = 'controller';
	const SERVICE_COMMAND_PREFIX = 'action';
	
	/*
	 * service dir
	 */
	const SERVICE_PATH = __DIR__ . DIRECTORY_SEPARATOR . self::SERVICE_SUFFIX . DIRECTORY_SEPARATOR;
	
	const SERVICE_NAMESPACE = __NAMESPACE__ . '\\' . self::SERVICE_SUFFIX;
	
	/*
	 * command help
	 */
	const COMMAND_HELP = [
		'?', 'h', 'help', 'man', '-?', '-h', '-help', '--h', '--help', '--?'
	];
	
	private $_service = [];
	
	private $_boot = [];
	
	public static $app = [];
	
	public function __construct($config = [], $components = []) {
		static::$app = [
			'param' => $config,
			'components' => $components,
		];
	}
	
	public function run($command = '', ...$args) {
		if (true === empty($command) || true === in_array($command, self::COMMAND_HELP)) {
			return $this->_showService();
		} else {
			$className = ucfirst($command) . ucfirst(self::SERVICE_SUFFIX);
			$className = self::SERVICE_NAMESPACE . '\\' . $className;
			$method = array_shift($args);
			$method = self::SERVICE_COMMAND_PREFIX . ucfirst($method);
			$reflection = new ReflectionMethod($className, $method);
			
			return $reflection->invokeArgs(new $className(static::$app), $args);
		}
	}
	
	private function _showService() {
		if (false === $this->_scanService()) {
			return -1;
		}
		$this->_scanAction();
		$this->_outputService();
	}
	
	private function _scanService() {
		/*
		 * 扫描不等于.,..的目录与文件
		 */
		$filePath = array_diff(scandir(self::SERVICE_PATH), ['.', '..']);
		/*
		 * 剔除文件
		 */
		$this->_service = array_map(function ($file) {
			$fileInfo = pathinfo($file);
			return $fileInfo['filename'];
		}, $filePath);
		
		if (true === empty($this->_service)) {
			return false;
		}
		/*
		 * 重新排序
		 */
		sort($this->_service);
	}
	
	private function _scanAction() {
		foreach ($this->_service as $service) {
			$reflection = new ReflectionClass(self::SERVICE_NAMESPACE . '\\' . $service);
			$method = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
			$action = [];
			array_map(function ($item) use (& $action) {
				if (false !== strpos($item->name, self::SERVICE_COMMAND_PREFIX )) {
					$actionName = lcfirst(str_replace(self::SERVICE_COMMAND_PREFIX, '', $item->name));
					$action[$actionName] = $this->_formatDoc($item->getDocComment());
				}
			}, $method);
			ksort($action, SORT_NATURAL);
			$serviceName = lcfirst(str_replace(ucfirst(self::SERVICE_SUFFIX), '', $service));
			$this->_boot[$serviceName] = $action;
		}
	}
	
	/**
	 * 获取简单一行注释信息
	 *
	 * @return string
	 */
	private function _formatDoc($comment) {
		$docs = explode(PHP_EOL, $comment);
        return true === isset($docs[1]) ? trim(str_replace('*', '', $docs[1])) : '';
	}
	
	/**
	 * 输出服务列表
	 *
	 * @return void
	 */
	private function _outputService() {
		echo str_pad('-', 170, '-', STR_PAD_RIGHT) . "\n";
		echo '|';
		echo str_pad('service', 28, ' ', STR_PAD_BOTH);
		echo '|';
		echo str_pad('command', 48, ' ', STR_PAD_BOTH);
		echo '|';
		echo str_pad('comment', 90, ' ', STR_PAD_BOTH);
		echo "|\n";
		echo str_pad('-', 170, '-', STR_PAD_RIGHT) . "\n";
		
		
		array_walk($this->_boot, function ($action, $service) {
			if (false === empty($action)) {
				echo '| ';
				echo str_pad($service, 27, ' ', STR_PAD_RIGHT);
				echo '|';
				echo str_pad(' ', 48, ' ', STR_PAD_BOTH);
				echo '|';
				echo str_pad(' ', 90, ' ', STR_PAD_BOTH);
				echo "|\n";
				array_walk($action, function ($comment, $command) use ($service) {
					echo '| ';
					echo str_pad(' ', 3, ' ', STR_PAD_LEFT);
					echo '|- ';
					echo str_pad($command, 21, ' ', STR_PAD_RIGHT);
					echo '| ';
					echo str_pad('[path]/service ' . $service . ' ' . $command, 47, ' ', STR_PAD_RIGHT);
					echo '| ';
					echo str_pad($comment, 89, ' ', STR_PAD_RIGHT);
					echo "|\n";
				});
				echo str_pad('-', 170, '-', STR_PAD_RIGHT) . "\n";
			}
		});
	}
}