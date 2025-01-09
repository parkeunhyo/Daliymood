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
$user_id = $_POST['user_id'] ?? null; // 아이디
$password = $_POST['password'] ?? null; // 비밀번호
$password_confirm = $_POST['password_confirm'] ?? null; // 비밀번호 확인
$name = $_POST['name'] ?? null; // 이름
$phone = $_POST['phone'] ?? null; // 전화번호

// 입력값 검증
if (empty($user_id) || empty($password) || empty($password_confirm) || empty($name) || empty($phone)) {
    die("모든 필드를 입력해주세요.");
}

if ($password !== $password_confirm) {
    die("비밀번호가 일치하지 않습니다.");
}

try {
    // 아이디 중복 확인
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $userExists = $stmt->fetchColumn();

    if ($userExists) {
        die("이미 사용 중인 아이디입니다.");
    }

    // 비밀번호 해싱
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // 데이터 삽입
    $stmt = $pdo->prepare("INSERT INTO users (user_id, password, name, phone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $hashedPassword, $name, $phone]);

    // 성공 메시지와 함께 login.html로 리다이렉트
    echo "<script>alert('회원가입이 성공적으로 완료되었습니다. 로그인 페이지로 이동합니다.');</script>";
    echo "<script>window.location.href = 'login.html';</script>";
    exit();
} catch (PDOException $e) {
    die("회원가입 처리 중 오류 발생: " . $e->getMessage());
}
?>
