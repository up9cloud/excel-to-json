<?php

// let generator output all error.
error_reporting(E_ALL);
ini_set('display_errors', true);

// set folder.
$base_dir = __DIR__ . '/input';
if (!isset($_POST['project'])) {
	echo 'project is invalid.';
	die;
}
$target_dir = $base_dir . DIRECTORY_SEPARATOR . $_POST['project'];

$upload_key = 'file';
if (!isset($_FILES[$upload_key])) {
	echo 'file not defined.';
	die;
}
$output_path = $target_dir . DIRECTORY_SEPARATOR . basename($_FILES[$upload_key]["name"]);

$counter = [
	'override' => 0,
	'move' => 0,
];

// Allow certain file formats
$file_ext = pathinfo($output_path, PATHINFO_EXTENSION);
$allow_ext_list = [
	'xls',
];
if (!in_array($file_ext, $allow_ext_list)) {
	echo 'only ' . implode(' ', $allow_ext_list) . ' file ext are allowed.';
	die;
}
// Check file size
// if ($_FILES["fileToUpload"]["size"] > 500000) {
//     echo "Sorry, your file is too large.";
//     die;
// }

if (!is_dir(dirname($output_path))) {
	$old_umask = umask(0);
	mkdir(dirname($output_path), 0777, true);
	umask($old_umask);
}
if (file_exists($output_path)) {
	$counter['override'] += 1;
}
touch($output_path);
chmod($output_path, 0666);
if (move_uploaded_file($_FILES[$upload_key]["tmp_name"], $output_path)) {
	$counter['move'] += 1;
	echo basename($_FILES[$upload_key]["name"]) . ' has been uploaded.' . PHP_EOL;
	echo 'total override: ' . $counter['override'] . PHP_EOL;
	echo 'total write: ' . $counter['move'] . PHP_EOL;
} else {
	echo '檔案無法寫入，請檢查資料夾權限。' . PHP_EOL;
}