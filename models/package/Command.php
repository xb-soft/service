<?php
namespace smc\models\package;

class Command {
	/*
	 * 0100000...00
	 */
	const HEARTBEAT_PING = 1 << 62;
	/*
	 * 0110000...00
	 */
	const HEARTBEAT_PONG = self::HEARTBEAT_PING >> 1;
	
	/*
	 * 0010000..00
	 */
	const SERVICE_REGISTER = 1 << 61;
}