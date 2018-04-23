<?php
return [
	'components' => [
		'smc' => [
			'class' => '\\Redis',
			'host' => '127.0.0.1',
			'port' => 6382,
			'auth' => 'qazpl,123',
			'db' => 15,
		],
	],
];