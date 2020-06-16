<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

require('../config.php');
require('../model/init.php');

function startsWith($string, $startString) { 
	$len = strlen($startString); 
	return (substr($string, 0, $len) === $startString); 
}

function jsonexit($error, $description) {
	$json = array('error'=>$error, 'msg'=>$description);
	exit(json_encode($json));
}

if (!isset($_POST['skylink']) || empty($_POST['skylink'])) {
	jsonexit('empty_skylink', "The submitted skylink is invalid, please submit 'sia://{46 char}' or '{46 char}' via POST. Please do NOT submit urls with portal domains.");
}

$skylink = $_POST['skylink'];
$skypath = ltrim($skylink, 'sia://');

if (strlen($skypath) !== 46) {
	jsonexit('wrong_skylink', "The submitted skylink is invalid, please submit 'sia://{46 char}' or '{46 char}' via POST. Please do NOT submit urls with portal domains.");
}

$stmt = $db->prepare("SELECT skypath FROM skylinks WHERE skypath = ? LIMIT 1");
if (!$stmt->execute([$skypath])) {
	jsonexit('database_error', 'Database error 1');
}
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (isset($row['skypath'])) {
	jsonexit('already_added', 'This skylink is already in out database');
}
$stmt = null;


$stmt = $db->prepare("INSERT INTO skylinks (skypath, insertion_date, insert_ip) VALUES (?, ?, ?)");
if (!$stmt->execute([$skypath, time(), ip2long($_SERVER['REMOTE_ADDR'])])) {
	jsonexit('database_error', 'Database error 2');
}
$stmt = null;


$json = array('skylink' => htmlspecialchars($_POST['skylink']), 'msg' => 'Skylink added successfully to the updater queue. Thank you!');
echo json_encode($json);