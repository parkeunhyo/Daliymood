<?php
// 데이터베이스 연결 정보
$host = 'localhost';
$dbname = 'pyp2024';
$username = 'pyp2024';
$password = 'pyp3992!';

try {
    // PDO를 사용하여 데이터베이스 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("데이터베이스 연결 실패: " . $e->getMessage());
}

// POST 데이터 수신
$user_id = $_POST['user_id'] ?? null;
$password = $_POST['password'] ?? null;

// 입력값 검증
if (empty($user_id) || empty($password)) {
    die("<script>alert('아이디와 비밀번호를 입력해주세요.'); history.back();</script>");
}

try {
    // 사용자 인증
    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $hashedPassword = $stmt->fetchColumn();

    if ($hashedPassword && password_verify($password, $hashedPassword)) {
        // 로그인 성공 -> index.html로 리다이렉트
        echo "<script>alert('로그인 성공!'); window.location.href = 'index.html';</script>";
        exit();
    } else {
        // 로그인 실패
        echo "<script>alert('아이디 또는 비밀번호가 일치하지 않습니다.'); history.back();</script>";
        exit();
    }
} catch (PDOException $e) {
    die("로그인 처리 중 오류 발생: " . $e->getMessage());
}
?>
