<?php
try {
    $dsn = 'mysql:host=db;dbname=bbs;charset=utf8';
    $db = new PDO($dsn, 'testuser', 'testpass');
    // echo "接続に成功しました";
} catch (PDOException $e) {
    echo "接続に失敗しました";
    echo $e->getMessage();
    exit();
}

//htmlspecialcharsのショートカット
function h($value) {
    return htmlspecialchars( $value, ENT_QUOTES);
}
?>