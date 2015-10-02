#!/usr/bin/php
<?php
ini_set('memory_limit', '-1');
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$usage = "Usage: parse-post-data.php <\"path to raw POST file\">\n";

// check for all required arguments
// first argument is always name of script!
if ($argc < 2) {
    die($usage);
}

// remove first argument
array_shift($argv);
$filepath = trim($argv[0]);

$input = file_get_contents($filepath);

// what's the boundary? first line in raw post
$split = explode("\n", $input);
$boundary = array_shift($split);

echo 'Boundary = ' . $boundary . "\n";

// last line is the filename
$rawfile = array_pop($split);
$joined = implode("\n", $split);

// make a directory for saving the files
$cwd = getcwd();
$basepath = $cwd . '/output/';

if(!is_dir($basepath)) {
	mkdir($basepath);
}
$savepath = $basepath . str_replace('-','',$rawfile) . '/';
mkdir( $savepath);
`chmod -R 777 $savepath`;

$result = explode($boundary, $joined);

// dump first and last elements
array_shift($result);

echo 'Item count: ' . sizeof($result) . "\n\n";

foreach($result as $chunk) {
	$lines = explode("\n",$chunk);
	echo $lines[2] . "\n";
	
	// split to get the filename
	$split = explode(';', $lines[2]);
	$split2 = explode('=', $split[1]);
	$filename = trim( str_replace('"','',$split2[1]) );
	
	$output = array_splice($lines, 4);
	
	$binary = implode("\n",$output);

	$save = file_put_contents("$savepath/$filename", trim($binary)); //base64_decode($binary)
	echo $filename . ' = ' . $save . " bytes saved\n\n";
}
?>