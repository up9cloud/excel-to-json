<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Util;

final class KeyValue extends Base {
	/**
	 * rull:
	 * 
	 * TYPE column must be "key"
	 * and one column (?1) must be "value"!
	 * 
	 * output:
	 *     {
	 *         key: value,
	 *         ...
	 *     }
	 * 
	 * @override
	 * @param  array $columns  [description]
	 * @param  array $data     [description]
	 * @param  int   $offset_x [description]
	 * @param  int   $offset_y [description]
	 * @example:
	 * 
	 * // excel: 
	 *     key     value
	 *     name    thomas
	 *     age     18
	 * 
	 * // json:
	 *     {
	 *         "name": "thomas",
	 *         "age": "18"
	 *     }
	 */
	protected function format($columns, $data, $offset_x=0, $offset_y=0){
		$key_index=$offset_x;
		foreach ($columns as $index => $value) {
			if($value==='value'){
				$value_index=$index;
				break;
			}
		}
		$result=[];
		foreach ($data as $index => $row) {
			$key=$row[$key_index];
			$value=$row[$value_index];
			if(!Util::isColumnNameValid($key)) {
				continue;
			}
			//phpexcel auto change false and true to boolean.
			// $fixed_value=strtolower($value);
			// switch(true){
			// case $fixed_value==='false':
			// 	$value=false;
			// 	break;
			// case $fixed_value==='true':
			// 	$value=true;
			// 	break;
			// }
			$result[$key] = $value;
		}
		return $result;
	}
}