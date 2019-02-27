<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Util;

final class Data extends Base {
	/**
	 * output: same as the database.
	 *     [
	 *         {key: value, key2: value, ...},
	 *         {key: value, key2: value, ...},
	 *         ...
	 *     ]
	 * 
	 * @override
	 * @param  array $columns  [description]
	 * @param  array $data     [description]
	 * @param  int   $offset_x [description]
	 * @param  int   $offset_y [description]
	 * @example:
	 * 
	 * // excel:
	 *     name   age gender
	 *     thomas 18  0
	 *     ben    19  1
	 *     simon  20  2
	 * 
	 * // json:
	 *     [
	 *         {"name": "thomas", "age": "18", "gender": "0"},
	 *         {"name": "ben", "age": "19", "gender": "1"},
	 *         {"name": "simon", "age": "20", "gender": "2"}
	 *     ]
	 */
	protected function format($columns, $data, $offset_x=0, $offset_y=0){
		$id_index=$offset_x;
		$result=[];
		foreach ($data as $key => $row) {
			$fixed_row=[];
			foreach ($row as $key => $value) {
				$column_name=$columns[$key];
				if(!Util::isColumnNameValid($column_name)) {
					continue;
				}
				if(!Util::isColumnValueValid($value)) {
					continue;
				}
				$fixed_row[$column_name] = $value;
			}
			$result[]=$fixed_row;
		}
		return $result;
	}
}