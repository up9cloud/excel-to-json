<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Util;

final class ArrayList extends Base {
	/**
	 * output: simple array list.
	 * 
	 * @override
	 * @param  array $columns  [description]
	 * @param  array $data     [description]
	 * @param  int   $offset_x [description]
	 * @param  int   $offset_y [description]
	 * @example
	 * 
	 * // excel:
	 *     list
	 *     true
	 *     18
	 *     19
	 *     20
	 *     hello
	 *
	 * // json:
	 *     [
	 *         true,
	 *         18,
	 *         19,
	 *         20,
	 *         'hello'
	 *     ]
	 */
	protected function format($columns, $data, $offset_x=0, $offset_y=0){
		$value_index=$offset_x;
		$result=[];
		foreach ($data as $index => $row) {
			$value=$row[$value_index];
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
			$result[] = $value;
		}
		return $result;
	}
}