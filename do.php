<?php

require_once __DIR__ . '/vendor/autoload.php';

// let generator output all error.
error_reporting(E_ALL);
ini_set('display_errors', true);

use com\funto\Converter\Converter;
use com\funto\Converter\Util;

function usage()
{
    echo 'Usage:' . PHP_EOL;
    echo '  php -h # show this help' . PHP_EOL;
    echo PHP_EOL;
    echo '  php ' . basename(__FILE__) . ' project_name [file_name] [sheet_name] # run!' . PHP_EOL;
    echo PHP_EOL;
    echo 'example:' . PHP_EOL;
    echo '  php ' . basename(__FILE__) . '                          # do all project' . PHP_EOL;
    echo '  php ' . basename(__FILE__) . ' mg2' . '                     # do only the project' . PHP_EOL;
    echo '  php ' . basename(__FILE__) . ' mg2 data_product' . '        # do only the file of the project' . PHP_EOL;
    echo '  php ' . basename(__FILE__) . ' mg2 data_product config' . ' # do only the sheet of the file of the project' . PHP_EOL;
}

if (isset($argv)) {
    $arguments = &$argv;
} else if (isset($_POST['argv'])) {
    $arguments = &$_POST['argv'];
}

try {
    $service = new Converter;
    if (isset($arguments[1])) {
        switch ($arguments[1]) {
            case '-h':
            case '--help':
                usage();
                die;
                break;
            case '--project':
                echo json_encode(Util::getProject($arguments[2]));
                die;
                break;
            case '--projects':
                echo json_encode(Util::getProject());
                die;
                break;
            case '--all':
                break;
            default:
                $service->setProject($arguments[1]);
                break;
        }
    }

    if (isset($arguments[2])) {
        $service->setSourceFileList($arguments[2]);
    }
    if (isset($arguments[3])) {
        $service->setSheet($arguments[3]);
    }
    $time = -microtime(true);
    $service->run();
    $time += microtime(true);
    $counter = $service->getCounter();
    echo 'total excel: ' . $counter['excelToJson'] . PHP_EOL;
    echo 'total 本次重複寫檔: ' . $counter['job_override'] . PHP_EOL;
    echo 'total 未重複寫檔: ' . $counter['file'] . PHP_EOL;
    echo 'total 蓋檔: ' . $counter['override'] . PHP_EOL;
    echo 'total 寫檔: ' . $counter['write'] . PHP_EOL;
    echo 'total write size: ' . $counter['size'] . PHP_EOL;
} catch (\Exception $e) {
    echo 'error: ' . $e->getMessage() . PHP_EOL;
} finally {
    echo 'total time: ' . $time . PHP_EOL;
}
