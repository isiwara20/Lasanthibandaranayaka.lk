<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dataFile = __DIR__ . '/../data/gallery.json';
$gallery = json_decode(@file_get_contents($dataFile), true) ?: [];

echo json_encode($gallery);
