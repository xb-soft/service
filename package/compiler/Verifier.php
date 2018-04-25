<?php
/**
 * compiler package
 *
 * @category php
 * @package service.smc.package
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package\compiler;

use package\Compiler;
use package\command\PackageType;
use package\command\PackageCmd;

class Verifier extends Compiler {

	/**
	 * 校验数据包
	 *
	 * @param StdClass $package
	 * @param int PackageType $type
	 * @param int PackageCmd $cmd
	 * @param int $length
	 * @param int $contentLength
	 *
	 * @return boolean
	 */
	public function verifyPackage(\StdClass $package, $type, $cmd, $length, $contentLength) {
		if ($package->type !== $type) {
			return false;
		}
		
		if ($package->cmd !== $cmd) {
			return false;
		}
		
		if ($package->len !== $length) {
			return false;
		}
		
		if ($package->len !== $contentLength) {
			return false;
		}
		
		return true;
	}
}