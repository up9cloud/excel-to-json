<?php

namespace com\funto\Converter;

final class Counter implements \ArrayAccess {
	protected $container=array();
	protected function offsetSetDefault($offset){
		$this->container[$offset]=0;
	}
	/**
	 * ArrayAccess
	 */
	public function offsetSet($offset, $value) {
		if(!isset($this->container[$offset])){
			$this->offsetSetDefault($offset);
		}
		$this->container[$offset] = $value;
	}
	public function offsetGet($offset) {
		if(!isset($this->container[$offset])){
			$this->container[$offset]=0;
		}
		return $this->container[$offset];
	}
	public function offsetExists($offset) {
		return isset($this->container[$offset]);
	}
	public function offsetUnset($offset) {
		unset($this->container[$offset]);
	}
}
