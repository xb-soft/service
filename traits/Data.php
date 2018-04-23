<?php
/**
 * service monitor heartbeat
 *
 * @category php
 * @package service
 * @author enze.wei <[enzewei@gmail.com]>
 * @copyright 2018
 * @version 1.0.0
 */
namespace smc\traits;

trait Data {
	
	public function encode($data) {
		if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
			return json_encode($data, JSON_UNESCAPED_UNICODE);
		} else {
			$data = preg_replace_callback('/(\\\\u[\w]{4})+/i', function ($matchs) {
				$str = json_decode('["' . $matchs[0] . '"]', true);
				return $str[0];
			}, json_encode($data));
			return $data;
		}
	}
	
	public function decode($data, $method = true) {
		return json_decode($data, $method);
	}
	
	protected function _format($code, $message, $data) {
		/*
		 * 失败/错误时data为null, code为负整数, message必须有内容
		 */
		$response = [
			'code' => $code,
			'msg' => $message,
			'result' => $data,
		];
		
		/*
		 * json方式
		 */
		return $this->encode($response);
	}
	
	public function success($data, $echo = false) {
		$reply = $this->_format(0, '', $data);
		if (true === $echo) {
			echo $reply;
		} else {
			return $reply;
		}
	}
	
	public function error($code, $message = '', $echo = false) {
		$reply = $this->_format($code, $message, null);
		if (true === $echo) {
			echo $reply;
		} else {
			return $reply;
		}
	}
}