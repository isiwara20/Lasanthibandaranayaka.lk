<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$dataFile = __DIR__ . '/../data/banner_videos.json';
$videos = json_decode(@file_get_contents($dataFile), true) ?: [];

// Filter only active videos
$activeVideos = array_values(array_filter($videos, fn($v) => ($v['active'] ?? true) === true));

echo json_encode($activeVideos);
