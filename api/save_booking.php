<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$dataFile = __DIR__ . '/../data/bookings.json';
$bookings = [];

if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    $bookings = json_decode($content, true) ?: [];
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$service = trim($_POST['service'] ?? 'General Service');
$date = trim($_POST['date'] ?? '');
$notes = trim($_POST['notes'] ?? '');

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Name and phone are required']);
    exit;
}

$newBooking = [
    'id' => 'book_' . time() . '_' . rand(100, 999),
    'name' => htmlspecialchars($name),
    'phone' => htmlspecialchars($phone),
    'service' => htmlspecialchars($service),
    'date' => htmlspecialchars($date),
    'notes' => htmlspecialchars($notes),
    'status' => 'New',
    'created_at' => date('Y-m-d H:i:s')
];

array_unshift($bookings, $newBooking);
file_put_contents($dataFile, json_encode($bookings, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'booking' => $newBooking]);
