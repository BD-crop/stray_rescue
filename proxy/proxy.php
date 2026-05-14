<?php
$lat = $_GET['lat'] ?? '';
$lng = $_GET['lng'] ?? '';

if (!$lat || !$lng) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing lat or lng']);
    exit;
}

$url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}";

$options = [
    "http" => [
        "header" => "User-Agent: MyApp/1.0\r\n"
    ]
];

$response = file_get_contents($url, false, stream_context_create($options));

header('Content-Type: application/json');
echo $response;

?>