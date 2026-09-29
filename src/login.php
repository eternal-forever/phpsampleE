<?php
require_once('dbconnect.php');
session_start();

$mail = '';
$error = [];

if (!empty($_POST)) {
    $mail = trim($_POST['mail'] ?? '');
    $pass = $_POST['pass'] ?? ''; // パスワードは生のまま扱う

    if ($mail === '' || $pass === '') {
        $error['login'] = 'メールアドレスとパスワードを入力してください';
    } else {
        try {
            $stmt = $db->prepare('SELECT * FROM members WHERE email = ?');
            $stmt->execute([$mail]);
            $record = $stmt->fetch();

            if ($record && password_verify($pass, $record['pass'])) {
                // セッション固定化対策
                session_regenerate_id(true);
                $_SESSION['id'] = (int)$record['mid'];
                $_SESSION['time'] = time();

                header('Location: index.php');
                exit();
            } else {
                $error['login'] = '認証できませんでした';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error['access'] = 'アクセスできませんでした';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <div class="login-container">
      <h1>ログイン画面</h1>
      <p><a href="join/index.php">ユーザでない方は登録へ</a></p>
      <form action="" method="post">
        <input type="text" placeholder="Mail Address" name="mail" value="<?php echo h($mail); ?>" required>
        <input type="password" placeholder="Password" name="pass" value="" required>
        <?php if (isset($error['access'])): ?>
        <span class="error"><?php echo h($error['access']); ?></span>
        <?php endif; ?>
        <?php if (isset($error['login'])): ?>
        <span class="error"><?php echo h($error['login']); ?></span>
        <?php endif; ?>
        <button type="submit">ログイン</button>
      </form>
    </div>
</body>
</html>
