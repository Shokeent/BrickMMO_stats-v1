<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include __DIR__ . "/includes/connect.php";

$input = file_get_contents("php://input");
$data = json_decode($input, true);

$response = [
    "timestamp" => date('Y-m-d H:i:s'),
    "received_data" => $data,
    "server_info" => [
        "ip" => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        "user_agent" => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        "referrer" => $_SERVER['HTTP_REFERER'] ?? null,
        "request_method" => $_SERVER['REQUEST_METHOD'],
        "content_type" => $_SERVER['CONTENT_TYPE'] ?? 'unknown'
    ]
];

if (!$data) {
    $response["status"] = "error";
    $response["message"] = "No input data received";
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

if (!isset($data['asset_id']) || !is_numeric($data['asset_id'])) {
    $response["status"] = "error";
    $response["message"] = "Invalid or missing asset_id";
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$asset_id = (int)$data['asset_id'];
$url = mysqli_real_escape_string($connect, $data['url'] ?? '');
$referrer = mysqli_real_escape_string($connect, $data['referrer'] ?? '');
$browser = mysqli_real_escape_string($connect, $data['browser'] ?? 'Unknown');
$os = mysqli_real_escape_string($connect, $data['os'] ?? 'Unknown');
$user_agent = mysqli_real_escape_string($connect, $_SERVER['HTTP_USER_AGENT'] ?? '');
$ip_address = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

$screen_width = isset($data['screen_width']) ? (int)$data['screen_width'] : null;
$screen_height = isset($data['screen_height']) ? (int)$data['screen_height'] : null;
$viewport_width = isset($data['viewport_width']) ? (int)$data['viewport_width'] : null;
$viewport_height = isset($data['viewport_height']) ? (int)$data['viewport_height'] : null;
$color_depth = isset($data['color_depth']) ? (int)$data['color_depth'] : null;
$timezone = mysqli_real_escape_string($connect, $data['timezone'] ?? '');
$timezone_offset = isset($data['timezone_offset']) ? (int)$data['timezone_offset'] : null;
$language = mysqli_real_escape_string($connect, $data['language'] ?? '');
$languages = mysqli_real_escape_string($connect, $data['languages'] ?? '');
$page_title = mysqli_real_escape_string($connect, $data['page_title'] ?? '');
$cookies_enabled = isset($data['cookies_enabled']) ? ($data['cookies_enabled'] ? 1 : 0) : null;
$java_enabled = isset($data['java_enabled']) ? ($data['java_enabled'] ? 1 : 0) : null;
$online = isset($data['online']) ? ($data['online'] ? 1 : 0) : null;

$asset_check_query = "SELECT id FROM assets WHERE id = $asset_id";
$asset_check_result = mysqli_query($connect, $asset_check_query);

if (mysqli_num_rows($asset_check_result) === 0) {
    $response["status"] = "error";
    $response["message"] = "Asset ID $asset_id not found";
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$table_info_query = "SHOW COLUMNS FROM stats";
$table_info_result = mysqli_query($connect, $table_info_query);
$existing_columns = [];
while ($row = mysqli_fetch_assoc($table_info_result)) {
    $existing_columns[] = $row['Field'];
}

$query = "
    INSERT INTO stats (
        asset_id, 
        url, 
        ip_address, 
        browser, 
        os, 
        user_agent, 
        referrer, 
        viewed_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
";

$stmt = $connect->prepare($query);

if ($stmt === false) {
    $response["status"] = "error";
    $response["message"] = "Database prepare failed: " . $connect->error;
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}

$stmt->bind_param("issssss", 
    $asset_id, 
    $url, 
    $ip_address, 
    $browser, 
    $os, 
    $user_agent, 
    $referrer
);

// Execute query
if ($stmt->execute()) {
    $insert_id = $stmt->insert_id;
    $affected_rows = $stmt->affected_rows;
    
    $response["status"] = "success";
    $response["message"] = "Tracking data saved successfully";
    $response["insert_id"] = $insert_id;
    $response["affected_rows"] = $affected_rows;
    $response["asset_id"] = $asset_id;
    
    if (isset($data['debug']) && $data['debug']) {
        $response["additional_data"] = [
            "screen" => ["width" => $screen_width, "height" => $screen_height],
            "viewport" => ["width" => $viewport_width, "height" => $viewport_height],
            "color_depth" => $color_depth,
            "timezone" => $timezone,
            "timezone_offset" => $timezone_offset,
            "language" => $language,
            "languages" => $languages,
            "page_title" => $page_title,
            "cookies_enabled" => $cookies_enabled,
            "java_enabled" => $java_enabled,
            "online" => $online
        ];
    }
    
} else {
    $response["status"] = "error";
    $response["message"] = "Database execution failed: " . $stmt->error;
}

$stmt->close();

if ($response["status"] === "success") {
    http_response_code(200);
} else {
    http_response_code(400);
}

echo json_encode($response, JSON_PRETTY_PRINT);
?>
