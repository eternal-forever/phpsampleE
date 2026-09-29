<?php
// dbconnect.php 共通DB接続 + 共通関数

$dsn = 'mysql:host=db;dbname=bbs;charset=utf8mb4';
try {
    $db = new PDO($dsn, 'testuser', 'testpass', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    error_log($e->getMessage());
    // 本番では詳細を出さない
    echo "データベース接続に失敗しました";
    exit();
}

// XSS対策: 出力時に使う
function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// CSRFトークン
function generate_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function check_token(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
