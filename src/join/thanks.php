<?php
session_start();

if (!isset($_SESSION['join'])) {
    header('Location: index.php');
    exit();
}

$nick = $_SESSION['join']['name'] ?? '';
// thanks表示後はセッションのjoinだけ削除
unset($_SESSION['join']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録完了画面</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="thanks-container">
      <h1>登録完了画面</h1>
      <p><?php echo htmlspecialchars($nick, ENT_QUOTES, 'UTF-8'); ?>さんを登録しました</p>
      <button type="button" class="submit" onclick="location.href='../login.php'">LOGINへ</button>
    </div>
</body>
</html>
