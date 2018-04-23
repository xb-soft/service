<?php
namespace smc\models\package;

use smc\models\Package;

class Verify extends Package {
	
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