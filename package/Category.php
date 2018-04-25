<?php
/**
 * compiler package
 *
 * @category php
 * @package service.smc.package
 * @author enze.wei <[enzewei@gmail.com]>
 * @version 1.0.1
 */
namespace package;

use smc\traits\Data as DataTrait;
use smc\traits\Magic as MagicTrait;

use package\compiler\Builder;
use package\compiler\Parser;
use package\compiler\Verifier;

use Proto\Common\Header as HeaderProto;
use Proto\Monitor\Kernel as KernelProto;

abstract class Category {
	
	use DataTrait, MagicTrait;
	
	protected $_data = null;
	
	public function __construct() {
		$this->_data = new \StdClass;
		$this->register('verifier', function () {
			return new Verifier;
		});
		
		$this->register('builder', function () {
			return new Builder;
		});
		
		$this->register('parser', function () {
			return new Parser;
		});
	}

	final protected function _buildPackage() {
		$builder = new Builder;		
		/*
		 * 打包header
		 */
		$header = $this->builder->buildPackage($this->_data->header, new HeaderProto);
		/*
		 * 打包content
		 */
		$proto = $this->builder->buildPackage($this->_data->content, new KernelProto);
		$proto->getHeader()[] = $header;
				
		return $this->builder->serialize($proto);
	}
	
	final protected function _parsePackage($bin) {
		return $this->parser->parsePackage($bin, new KernelProto);
	}
	
	abstract public function parserBin($bin);
	
	abstract public function getCurrentType();
	
	abstract public function getCurrentCmd();

	public function getCurrentLen() {
		return strlen($this->_data->currentContent);
	}
	
	public function getCurrentContent() {
		return $this->_data->currentContent;
	}
}