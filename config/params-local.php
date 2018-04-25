<?php
return [
	'kernel' => [
		'host' => '127.0.0.1',
		'port' => '31010',
		'mode' => SWOOLE_PROCESS,
		'type' => SWOOLE_SOCK_TCP,
		'setting' => [
			'log_file' => '/var/log/swoole/server.log',
			'pid_file' => '/var/run/swoole.pid',
			'daemonize' => 0,
			'worker_num' => 4,
			'task_worker_num' => 2,
		],
	],
	'upstream' => [
		'host' => '127.0.0.1',
		'port' => '31011',
		'mode' => SWOOLE_PROCESS,
		'type' => SWOOLE_SOCK_TCP,
		'setting' => [
			'log_file' => '/var/log/swoole/server.log',
			'pid_file' => '/var/run/swoole.pid',
			'daemonize' => 0,
			'worker_num' => 4,
			'task_worker_num' => 2,
		],
	],
];