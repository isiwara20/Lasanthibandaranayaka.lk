<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$dataFile = __DIR__ . '/../data/testimonials.json';

if (!file_exists($dataFile)) {
    echo json_encode([]);
    exit;
}

$content = file_get_contents($dataFile);
$items = json_decode($content, true) ?: [];

// Filter to only approved items
$approved = array_filter($items, function($item) {
    return isset($item['status']) && $item['status'] === 'approved';
});

// Re-index array
$approved = array_values($approved);

echo json_encode($approved);
