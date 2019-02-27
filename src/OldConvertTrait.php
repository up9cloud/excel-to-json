<?php

namespace com\funto\Converter;

trait OldConvertTrait
{
    protected function getExcelPageList($flip = false)
    {
        static $list = array(
            //item data
            'product' => 0,
            'lvup' => 1,
            'monster' => 2,
            //frondend language file
            'mission_ch' => 3,
            //frondend language file
            'mission_text_ch' => 4,
            'potentital' => 5,
            //frondend language file
            'mission_text' => 6,
            'achieve' => 7,
            //equipment data
            'product_value' => 8,
            'treasure' => 9,
            //frondend language file
            'InvetWord' => 10,
            'TowerMonster' => 11,
            //default map file (import to database mapwar, but client use database's datas)
            'MapInfo' => 12,
        );
        if ($flip) {
            return array_flip($list);
        } else {
            return $list;
        }
    }
    protected function gameExcelToJsonFiles($objPHPExcel, $sheet_name = null, $relative_dir)
    {
        // build jobs.
        $map = $this->getExcelPageList();
        $jobs = [];
        if ($sheet_name === null) {
            foreach ($map as $name => $sheet) {
                array_push($jobs, array(
                    'sheet' => $sheet,
                    'name' => $name,
                ));
            }
        } elseif (isset($map[$sheet_name])) {
            array_push($jobs, array(
                'sheet' => $map[$sheet_name],
                'name' => $sheet_name,
            ));
        } else {
            throw new \ErrorException('EXCEL_SHEET_TITLE_INVALID');
        }

        /**
         * do all jobs.
         */
        foreach ($jobs as $job) {
            $sheet = $job['sheet'];
            $filename = $job['name'];
            $worksheet = $objPHPExcel->getSheet($sheet);
            $result = $this->gameExcelSheetObjectToJson($worksheet, $filename);

            //write all result in json file.
            $file_ext = 'json';
            $save_path = $this->output_root . DIRECTORY_SEPARATOR . $relative_dir . DIRECTORY_SEPARATOR . $filename . '.' . $file_ext;
            $this->write($save_path, $result);
        }
    }
    protected function gameExcelSheetObjectToJson($worksheet, $filename)
    {
        $rows = $worksheet->toArray();
        $column_names = $rows[0];
        $row = array();
        $result = array();
        $row_key_fixer = function ($val, $key) use (&$row, &$column_names) {
            if ($val == null) {
                $val = 0;
            }
            $row[$column_names[$key]] = $val;
        };
        switch ($filename) {
            //使用於d有重複的情況
            case 'achieve':
                for ($i = 1; $i < count($rows); $i++) {
                    $result[$rows[$i][0]][$i] = $rows[$i];
                }
                break;
            // mission language file need to be fixed.
            case 'mission_ch':
                for ($i = 1; $i < count($rows); $i++) {
                    array_walk($rows[$i], $row_key_fixer);
                    if ( ! isset($result[$row['d']])) {
                        $result[$row['d']] = [];
                    }
                    $result[$row['d']][$row['num']] = $row;
                }
                break;
            //使用於d沒重複的情況
            default:
                for ($i = 1; $i < count($rows); $i++) {
                    array_walk($rows[$i], $row_key_fixer);
                    $result[$rows[$i][0]] = $row;
                }
                break;
        }
        return Encoder::toJson($result);
    }
}
