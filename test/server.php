<?php
use \Swoole\Client;

require_once __DIR__ . '/../vendor/autoload.php';

use Proto\Common\Header as HeaderProto;
use Proto\Monitor\Kernel as KernelProto;

$client = new Client(SWOOLE_SOCK_TCP);

if (!$client->connect('127.0.0.1', 31011, 0.5)) {
	/*
	 * 重启worker进程
	 */
	exit;
}
$header = new HeaderProto;
$kernel = new KernelProto;

$message = 'gateway';

$header->setMessageType(package\command\Type::MESSAGE_QUERY_TYPE);
$header->setMessageCmd(package\command\Cmd::SERVICE_QUERY);
$header->setMessageLen(strlen($message));

$kernel->getHeader()[] = $header;
$kernel->setMessageContent($message);
$data = $kernel->serializeToString();

$client->send($data);

$reply = $client->recv();

var_dump($reply);
$client->close();