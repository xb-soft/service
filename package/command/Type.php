<?php
/**
 * Type package
 *
 * @category php
 * @package service.smc.package.command
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package\command;

class Type {
	
	static public $current = null;
	
	const PACKAGE_FLAG = 0x5842;

	/*
	 * 消息基础类型
	 */
	const MESSAGE_TYPE = self::PACKAGE_FLAG << 16;
	
	/*
	 * 心跳包类型
	 */
	const MESSAGE_HEARTBEAT_TYPE = self::MESSAGE_TYPE + (1 << 15);
	/*
	 * 服务注册数据包类型
	 */
	const MESSAGE_REGISTER_TYPE = self::MESSAGE_TYPE + (1 << 14);
	/*
	 * 服务查询数据包
	 */
	const MESSAGE_QUERY_TYPE = self::MESSAGE_TYPE + (1 << 13);
}