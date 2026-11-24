<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$dataFile = __DIR__ . '/../data/inquiries.json';
$inquiries = [];

if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    $inquiries = json_decode($content, true) ?: [];
}

$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$interest = trim($_POST['interest'] ?? 'General Inquiry');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($phone)) {
    echo json_encode(['success' => false, 'error' => 'Name and phone are required']);
    exit;
}

$newInquiry = [
    'id' => 'inq_' . time() . '_' . rand(100, 999),
    'name' => htmlspecialchars($name),
    'phone' => htmlspecialchars($phone),
    'interest' => htmlspecialchars($interest),
    'message' => htmlspecialchars($message),
    'status' => 'New',
    'created_at' => date('Y-m-d H:i:s')
];

array_unshift($inquiries, $newInquiry);
file_put_contents($dataFile, json_encode($inquiries, JSON_PRETTY_PRINT));

echo json_encode(['success' => true, 'inquiry' => $newInquiry]);
