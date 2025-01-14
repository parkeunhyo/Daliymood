<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'pyp2024';
$username = 'pyp2024';
$password = 'pyp3992!';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

// JSON 입력 데이터 읽기
$rawInput = file_get_contents('php://input');
if (!$rawInput) {
    http_response_code(400);
    echo json_encode([
        "error" => "No raw input received",
        "raw_input" => $rawInput
    ]);
    exit;
}

$input = json_decode($rawInput, true);
if (!$input) {
    http_response_code(400);
    echo json_encode([
        "error" => "Invalid JSON format",
        "raw_input" => $rawInput
    ]);
    exit;
}

// 사용자 데이터 파싱
$id = $input['id'] ?? null;
$display_name = $input['display_name'] ?? null;
$email = $input['email'] ?? null;
$profile_image = $input['profile_image'] ?? null;
$spotify_url = $input['spotify_url'] ?? null;
$account_type = $input['account_type'] ?? null;
$access_token = $input['access_token'] ?? null;

// 필수 데이터 검증
if (!$id || !$display_name || !$email) {
    http_response_code(400);
    echo json_encode(["error" => "Missing required fields"]);
    exit;
}

// 데이터 저장 또는 업데이트
try {
    $stmt = $pdo->prepare("
        INSERT INTO member (id, display_name, email, profile_image, spotify_url, account_type, access_token, token_expires_at)
        VALUES (:id, :display_name, :email, :profile_image, :spotify_url, :account_type, :access_token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
        ON DUPLICATE KEY UPDATE
            display_name = :display_name,
            email = :email,
            profile_image = :profile_image,
            spotify_url = :spotify_url,
            account_type = :account_type,
            access_token = :access_token,
            token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR)
    ");

    $stmt->execute([
        ':id' => $id,
        ':display_name' => $display_name,
        ':email' => $email,
        ':profile_image' => $profile_image,
        ':spotify_url' => $spotify_url,
        ':account_type' => $account_type,
        ':access_token' => $access_token,
    ]);

    // 최종 JSON 응답
    echo json_encode([
        "message" => "User saved successfully",
        "rows_affected" => $stmt->rowCount()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
exit; // 추가 출력 방지
