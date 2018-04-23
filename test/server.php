<?php
use \Swoole\Server;
use \Swoole\Client;

require_once __DIR__ . '/../vendor/autoload.php';

use Proto\Common\Header as HeaderProto;
use Proto\Monitor\Kernel as KernelProto;

$server = new Server('127.0.0.1', 31002, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
$server->set(['task_worker_num' => 2]);
$server->on('connect', function ($ser, $fd) {
	echo "client connect.\n";
});

$server->on('workerStart', function ($ser, $workerId) {

	/*
	 * 注册服务
	 */
	if (0 == $workerId) {
		$client = new Client(SWOOLE_SOCK_TCP);
		
		if (!$client->connect('127.0.0.1', 31010, 0.5)) {
			/*
			 * 重启worker进程
			 */
			exit;
		}
		$header = new HeaderProto;
		$kernel = new KernelProto;
		
		$content = [
			'name' => 'account',
			'host' => '127.0.0.1',
			'port' => 31002,
			'type' => 'tcp',
		];
		$message = json_encode($content);
		
		$header->setMessageType((0x5842 << 16) + (1 << 14));
		$header->setMessageCmd(smc\models\package\Command::SERVICE_REGISTER);
		$header->setMessageLen(strlen($message));
		
		$kernel->getHeader()[] = $header;
		$kernel->setMessageContent($message);
		$data = $kernel->serializeToString();

		$client->send($data);
		
		$reply = $client->recv();
		$client->close();
	}
});

$server->on('receive', function ($ser, $fd, $fromId, $data) {
	/*
	 * 校验heartbeat数据包
	 */
	

	$kernel = new KernelProto;
	$header = new HeaderProto;
	$content = '';
	$header->setMessageType((0x5842 << 16) + (1 << 15));
	$header->setMessageCmd(smc\models\package\Command::HEARTBEAT_PONG);
	$header->setMessageLen(strlen($content));
	$kernel->getHeader()[] = $header;
	$kernel->setMessageContent($content);
	$bin = $kernel->serializeToString();
	$ser->send($fd, $bin);
});

$server->on('task', function ($ser, $taskId, $workerId, $fd) {
	
});

$server->on('finish', function ($ser, $taskId, $data) {
	echo "finish.\n";
});

$server->on('close', function ($ser, $fd) {
	echo $fd . " client close.\n";
});

$server->start();
