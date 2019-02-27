<?php

namespace com\funto\Converter\style;

use com\funto\Converter\Util;

class Map extends Base {
	protected $flip=false;
	/**
	 * rull:
	 * TYPE column must be 'x\y'
	 *
	 * output:
	 *     {
	 *         "x1,y1": value,
	 *         "x2,y2": value,
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
	 *     y\x 1    2    3
	 *     1   stop tree oil
	 *     2   oil  stop tree
	 *     3   tree oil  stop
	 * 
	 * // excel: (flip===true)
	 *     x\y 1    2    3
	 *     1   stop oil  tree
	 *     2   tree stop oil
	 *     3   oil  tree stop
	 * 
	 * // json:
	 *     {
	 *         "1,1": "stop",
	 *         "1,2": "oil",
	 *         "1,3": "tree",
	 *         "2,1": "tree",
	 *         "2,2": "stop",
	 *         "2,3": "oil",
	 *         "3,1": "oil",
	 *         "3,2": "tree",
	 *         "3,3": "stop",
	 *         "x,min": 1,
	 *         "x,max": 3,
	 *         "y,min": 1,
	 *         "y,max": 3,
	 *     }
	 */
	protected function format($columns, $data, $offset_x=0, $offset_y=0){
		$separator=',';
		$start_index=$offset_x;
		$result=[];
		$left_min=null;
		$left_max=null;
		$up_min=null;
		$up_max=null;
		foreach ($data as $index => $row) {
			$left_coor=$row[$start_index];
			if(!Util::isCoordinateNameValid($left_coor)){
				continue;
			}
			if($left_min===null){
				$left_min=$left_max=$left_coor;
			}else{
				$left_min=min($left_coor, $left_min);
				$left_max=max($left_coor, $left_max);
			}
			if(!Util::isCoordinateNameValid($left_coor)){
				continue;
			}
			foreach ($row as $key => $value) {
				if($key===$start_index){
					continue;
				}
				$up_coor=$columns[$key];
				if(!Util::isCoordinateNameValid($up_coor)){
					continue;
				}
				if($up_min===null){
					$up_min=$up_max=$up_coor;
				}else{
					$up_min=min($up_coor, $up_min);
					$up_max=max($up_coor, $up_max);
				}
				if(!Util::isColumnValueValid($value)) {
					continue;
				}
				if($this->flip===true){
					$key_name=$left_coor.$separator.$up_coor;
				}else{
					$key_name=$up_coor.$separator.$left_coor;
				}
				$result[$key_name]=$value;
			}
		}
		if($this->flip===true){
			$result['x,min']=$left_min;
			$result['x,max']=$left_max;
			$result['y,min']=$up_min;
			$result['y,max']=$up_max;
		}else{
			$result['x,min']=$up_min;
			$result['x,max']=$up_max;
			$result['y,min']=$left_min;
			$result['y,max']=$left_max;
		}
		return $result;
	}
}