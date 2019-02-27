<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Util;

final class Group extends Base {
	/**
	 * rull:
	 * 
	 * TYPE column must be 'group'.
	 * 
	 * output:
	 *     {
	 *        group => [
	 *            {
	 *                col_name: value,
	 *                ...
	 *            },
	 *            ...
	 *        ]
	 *        ...
	 *     }
	 * 
	 * @override
	 * @param  array $columns  [description]
	 * @param  array $data     [description]
	 * @param  int   $offset_x [description]
	 * @param  int   $offset_y [description]
	 * @return array
	 * @example:
	 * 
	 * // excel:
	 *     group  index col_1  col_2  col_3
	 *     money  2     301    302    303
	 *     money  1     201    202    203
	 *     money  0     101    102    103
	 *     silver 1     21     22     23
	 *     silver 0     11     12     13
	 *     silver 2     31     32     33
	 * 
	 * // excel:
	 *     group  col_1  col_2  col_3
	 *     money  101    102    103
	 *     money  201    202    203
	 *     money  301    302    303
	 *     silver 11     12     13
	 *     silver 21     22     23
	 *     silver 31     32     33
	 * 
	 * // json:
	 *     {
	 *         "money": [
	 *             {"col_1": "101","col_2": "102","col_3": "103"},
	 *             {"col_1": "201","col_2": "202","col_3": "203"},
	 *             {"col_1": "301","col_2": "302","col_3": "303"},
	 *         ],
	 *         "silver": [
	 *             {"col_1": "11","col_2": "12","col_3": "13"},
	 *             {"col_1": "21","col_2": "22","col_3": "23"},
	 *             {"col_1": "31","col_2": "32","col_3": "33"},
	 *         ]
	 *     }
	 */
	protected function format($columns, $data, $offset_x=0, $offset_y=0){
		$id_index=$offset_x;
		$idx_index=null;
		foreach ($columns as $key => $value) {
			if($value==='index'){
				$idx_index=$key;
				break;
			}
		}
		$result=[];
		$group=[];
		foreach ($data as $index => $row) {
			$id = $row[$id_index];
			if(!Util::isColumnNameValid($id)) {
				continue;
			}
			if($idx_index===null){
				// 照順序排
				if(!isset($group[$id])){
					$result[$id] = [];
					$group[$id] = 0;
				}else{
					// 本群組現在到第幾個惹
					$group[$id]++;
				}
			}else{
				$i=$row[$idx_index];
			}
			foreach ($row as $key => $value) {
				if($key===$id_index){
					continue;
				}
				$column_name=$columns[$key];
				if(!Util::isColumnNameValid($column_name)) {
					continue;
				}
				if(!Util::isColumnValueValid($value)) {
					continue;
				}
				if($column_name==='index'){
					continue;
				}
				$i = $group[$id];
				$result[$id][$i][$column_name] = $value;
			}
		}
		return $result;
	}
}