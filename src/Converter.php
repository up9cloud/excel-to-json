<?php

namespace com\funto\Converter;

use com\funto\Converter\style\ArrayList;
use com\funto\Converter\style\Data;
use com\funto\Converter\style\Group;
use com\funto\Converter\style\Id;
use com\funto\Converter\style\KeyValue;
use com\funto\Converter\style\Map;
use com\funto\Converter\style\MapFlip;
use PHPExcel;
use PHPExcel_IOFactory;

class Converter
{
    use OldConvertTrait;

    protected $excel_ext = 'xls';
    protected $input_root = '';
    protected $output_root = '';
    protected $source_file_list = [];
    protected $write_list = [];
    protected $counter;

    public function __construct($project_dirname = null, $filepaths = array(), $sheet_name = null)
    {
        $this->counter = new Counter;
        if ($project_dirname === null) {
            return;
        }
        $this->setProject($project_dirname);
        if (empty($filepaths)) {
            return;
        }
        $this->setSourceFileList($filepaths);
        if ($sheet_name === null) {
            return;
        }
        $this->setSheet($sheet_name);
    }
    protected function setInputRoot($root)
    {
        $this->input_root = $root;
    }
    protected function setOutputRoot($root)
    {
        $this->output_root = $root;
    }
    public function setProject($name)
    {
        $this->setInputRoot(__DIR__ . '/../input/' . basename($name));
        $this->setOutputRoot(__DIR__ . '/../output/' . basename($name));
    }
    public function setSourceFileList($filepaths)
    {
        // single file.
        if ( ! is_array($filepaths)) {
            if ( ! is_file($filepaths)) {
                //just file name?
                $target_file_path = $this->getFilePathFromName($filepaths);
            }
            $filepaths = [$target_file_path];
        }
        $this->source_file_list = $filepaths;
    }
    /**
     * TODO assign the sheet.
     * @param string $name [description]
     */
    public function setSheet($name)
    {
        echo 'assign sheet not support yet.' . PHP_EOL;
    }
    public function getCounter()
    {
        return $this->counter;
    }
    protected function getAllFile($project_root)
    {
        // support json, so don't limit ext name.
        return glob($project_root . '/*');
        // return glob($project_root . '/*.' . $this->excel_ext);
    }
    protected function getFilePathFromName($file_name)
    {
        $file_path = $this->input_root . DIRECTORY_SEPARATOR . $file_name;
        // try add extention name.
        if ( ! is_file($file_path)) {
            $file_path .= '.' . $this->excel_ext;
        }
        if ( ! is_file($file_path)) {
            throw new \ErrorException($file_path . ' not exist.', 1);
        }
        return $file_path;
    }
    public function run()
    {
        if ($this->input_root === '') {
            // do all the project.
            // get all projects.
            foreach (Util::getProject() as $key => $project) {
                $this->setProject($project);
                $this->setSourceFileList([]); //all files.
                $this->run();
            }
            return;
        }
        if (empty($this->source_file_list)) {
            $this->setSourceFileList($this->getAllFile($this->input_root));
        }
        foreach ($this->source_file_list as $key => $filepath) {
            $pathinfo = pathinfo($filepath);
            if ($pathinfo['extension'] === 'json') {
                $this->counter['jsonToUtf8Json'] += 1;
                $output_path = $this->output_root . DIRECTORY_SEPARATOR . $pathinfo['basename'];
                $this->write($output_path, Encoder::toJson(json_decode(file_get_contents($filepath))));
            } else {
                $this->excelToJson($filepath);
            }
        }
    }
    /**
     * filename to folder 架構
     *
     * @example
     * // input files.
     * config-hello.xls
     * config-world.xxx.xls
     * config-hello.beta.xls
     * // output dir path
     * config/hello
     * config/world
     * config/hello
     * @param  string $filename example: config-battle-pve.xls
     * @return string example: config/battle/pve
     */
    protected function excelFilePathToDir($filepath)
    {
        $pathinfo = pathinfo($filepath);
        // file name may has '.' so we need to parse again.
        $parsed = explode('.', $pathinfo['filename']);
        $filename = $parsed[0];
        return str_replace('-', DIRECTORY_SEPARATOR, $filename);
    }
    /**
     * write result to json file.
     *
     * @param  [type] $result         [description]
     * @param  string $file_ext       [description]
     * @return [type] [description]
     */
    protected function write($output_path, $result)
    {
        if ( ! is_dir(dirname($output_path))) {
            $old_umask = umask(0);
            mkdir(dirname($output_path), 0777, true);
            umask($old_umask);
        }
        if (is_file($output_path)) {
            // 蓋檔案
            $this->counter['override'] += 1;
        }
        file_put_contents($output_path, $result, LOCK_EX);

        chmod($output_path, 0666);
        if (in_array($output_path, $this->write_list)) {
            // 本次已經寫過
            $this->counter['job_override'] += 1;
        } else {
            // 本次沒寫過
            $this->counter['file'] += 1;
            array_push($this->write_list, $output_path);
        }
        $this->counter[__FUNCTION__] += 1;
        $this->counter['size'] += mb_strlen($result, '8bit'); //strlen($result);
    }
    /**
     * excel game settings to json files.
     */
    protected function excelToJson($filepath, $sheet_name = null)
    {
        $this->counter[__FUNCTION__] += 1;
        /**
         * load excel to object.
         */
        $reader_type = PHPExcel_IOFactory::identify($filepath);
        $PHPReader = PHPExcel_IOFactory::createReader($reader_type);
        $PHPReader->setReadDataOnly(true); //reduce memory.
        if ($sheet_name !== null) {
            $PHPReader->setLoadSheetsOnly($sheetname); // reduce memory use.
        }
        $objPHPExcel = $PHPReader->load($filepath);

        $relative_dir = $this->excelFilePathToDir($filepath);

        // 舊設定檔案 特殊處理
        // 把原路徑 path/to/data_product 改成 path/to/config
        $index = strrpos($relative_dir, DIRECTORY_SEPARATOR);
        $filename = substr($relative_dir, $index);
        if ($filename === 'data_product') {
            $relative_dir = substr($relative_dir, 0, $index) . 'config';
            $this->gameExcelToJsonFiles($objPHPExcel, $sheet_name, $relative_dir);
        } else {
            $this->excelObjectToJsonFiles($objPHPExcel, $sheet_name, $relative_dir);
        }
        unset($PHPReader); //reduce memory.
    }
    /**
     * convert new excel files to json and save it.
     * @param  [type] $objPHPExcel    [description]
     * @param  string $sheet_name     指定 sheet 輸出，null 表示全部
     * @param  [type] $relative_dir   [description]
     * @return [type] [description]
     */
    protected function excelObjectToJsonFiles($objPHPExcel, $sheet_name = null, $relative_dir)
    {
        $objPHPExcel->setActiveSheetIndex(0);
        foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
            $filename = $worksheet->getTitle();
            //if it's not target sheet.
            if ($sheet_name !== null && $filename !== $sheet_name) {
                continue;
            }
            $result = $this->excelSheetObjectToJson($worksheet);
            $file_ext = 'json';
            $output_path = $this->output_root . DIRECTORY_SEPARATOR .
                $relative_dir . DIRECTORY_SEPARATOR .
                $filename . '.' . $file_ext;
            $this->write($output_path, $result);
        }
    }
    /**
     * [excelObject2json description]
     * @param  [type]  $worksheet PHPExcel sheet object.
     * @return [array] [filename => json_content, ...]
     */
    protected function excelSheetObjectToJson($worksheet)
    {
        $rows = $worksheet->toArray();
        //first line is column names.
        $column_names = array_shift($rows);
        // we use TYPE column to check what type it is.
        //TYPE column default is A1, but some user may move TYPE column to B1 (let A1 empty)
        $offset_x = 0;
        $type = null;
        foreach ($column_names as $key => $value) {
            if (Util::isColumnNameValid($value)) {
                $type = $column_names[$key];
                $offset_x = $key;
                break;
            }
        }
        switch ($type) {
            case 'key':
                return (string) new KeyValue($column_names, $rows, $offset_x);
            case 'x\\y':
                return (string) new MapFlip($column_names, $rows, $offset_x);
            case 'y\\x':
                return (string) new Map($column_names, $rows, $offset_x);
            case 'id':
                return (string) new Id($column_names, $rows, $offset_x);
            case 'array':
            case 'list':
                return (string) new ArrayList($column_names, $rows, $offset_x);
            case 'group':
            case 'kind':
                return (string) new Group($column_names, $rows, $offset_x);
            default:
                return (string) new Data($column_names, $rows, $offset_x);
        }
    }
}
