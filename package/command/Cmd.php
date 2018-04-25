<?php
/**
 * command package
 *
 * @category php
 * @package service.smc.package.command
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package\command;

class Cmd {
	
	static public $current = null;
	
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
	
	/*
	 * 0001000..00
	 */
	const SERVICE_QUERY = 1 << 60;
}