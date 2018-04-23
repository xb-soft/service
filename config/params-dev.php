<?php
return [
	'kernel' => [
		'host' => '192.168.0.11',
		'port' => '31010',
		'mode' => SWOOLE_PROCESS,
		'type' => SWOOLE_SOCK_TCP,
		'setting' => [
			'log_file' => '/var/log/swoole/server.log',
			'pid_file' => '/var/run/swoole.pid',
			'daemonize' => 1,
			'worker_num' => 4,
			'task_worker_num' => 8,
		],
	],
];