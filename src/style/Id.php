<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Util;

final class Id extends Base {
	/**
	 * rull:
	 * 
	 * TYPE column must be 'id'.
	 * 
	 * output:
	 *     {
	 *        id => {
	 *            col_name: value,
	 *            ...
	 *        },
	 *        ...
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
	 *     id     col_1  col_2  col_3
	 *     money  100    200    300
	 *     silver 10     20     30
	 *     gold   1      2      3
	 * 
	 * // json:
	 *     {
	 *         "money": {
	 *             "col_1": "100",
	 *             "col_2": "200",
	 *             "col_3": "300"
	 *         },
	 *         "silver": {
	 *             "col_1": "10",
	 *             "col_2": "20",
	 *             "col_3": "30"
	 *         },
	 *         "gold": {
	 *             "col_1": "1",
	 *             "col_2": "2",
	 *             "col_3": "3"
	 *         }
	 *     }
	 */
	protected function format($columns, $data, $offset_x=0, $offset_y=0){
		$id_index=$offset_x;
		$result=[];
		foreach ($data as $index => $row) {
			$id = $row[$id_index];
			if(!Util::isColumnNameValid($id)) {
				continue;
			}
			$result[$id] = [];
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
				$result[$id][$column_name] = $value;
			}
		}
		return $result;
	}
}