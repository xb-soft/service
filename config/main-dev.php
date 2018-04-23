<?php
return [
	'components' => [
		'smc' => [
			'class' => '\\Redis',
			'host' => '192.168.0.11',
			'port' => 6382,
			'auth' => 'qazpl,123',
			'db' => 15,
		],
	],
];