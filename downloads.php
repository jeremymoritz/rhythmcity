<?php
include('includes/rc_all.php');

if(empty(filter_input(INPUT_GET, 'file'))) {
	header('Location: http://rhythmcity.org');
}

$filename = realpath('downloads/' . filter_input(INPUT_GET, 'file'));
$ctype = 'application/' . substr(filter_input(INPUT_GET, 'file'),strrpos(filter_input(INPUT_GET, 'file'),'.') + 1);

if (!file_exists($filename)) {
	die('NO FILE HERE');
}

header('Pragma: public');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Cache-Control: private', false);
header('Content-Type: ' . $ctype);
header('Content-Disposition: attachment; filename="' . basename($filename) . '";');
header('Content-Transfer-Encoding: binary');
header('Content-Length: ' . @filesize($filename));
set_time_limit(0);
@readfile($filename) or die('File not found.');

?>
