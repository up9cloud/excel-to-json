<?php

namespace com\funto\Converter;

class Util {
	/**
	 * 欄位名空的不輸出（有可能是企劃註解）
	 * @param  [type]  $column_name [description]
	 * @return boolean              [description]
	 */
	public static function isColumnNameValid($column_name) {
		if ($column_name === null || $column_name === '') {
			return false;
		}
		return true;
	}
	/**
	 * 值空的不輸出
	 * @param  [type]  $value [description]
	 * @return boolean        [description]
	 */
	public static function isColumnValueValid($value) {
		if ($value === null || $value === '') {
			return false;
		}
		return true;
	}
	/**
	 * 座標key應該是數字
	 * @param  [type]  $coor [description]
	 * @return boolean       [description]
	 */
	public static function isCoordinateNameValid($coor) {
		if (is_numeric($coor)) {
			return true;
		}
		return false;
	}
	public static function getProject($project_name = null) {
		if ($project_name === null) {
			return array_map('basename', glob(__DIR__ . '/../input/*', GLOB_ONLYDIR));
		}
		return array_map('basename', glob(__DIR__ . '/../input/' . $project_name . '/*'));
	}
}
