<?php
require('dbconnect.php');
session_start();

if (!empty($_POST)) {
  $mail = h($_POST['mail']);
  $pass =  h($_POST['pass']);
  try {
    if ($mail != '' && $pass != '') {
      $statement = $db->prepare('select * from members where email=?;');
      $statement->bindParam(1, $mail,PDO::PARAM_STR);
      $statement->execute();
      $record = $statement->fetch(PDO::FETCH_BOTH);
    }
    if (!empty($record)) {
      if (password_verify($pass,$record['pass'])) {
        $_SESSION['id'] = $record['mid'];
        $_SESSION['time'] = time();
  
        header('Location: index.php');
        exit();
      } else {
        $error['login'] = "認証できませんでした";
      }
    } else {
      $error['login'] = "認証できませんでした";
    }
  } catch(PDOException $e) {
    $error['access'] = "アクセスできませんでした";
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/854a6d07f3.js" crossorigin="anonymous"></script>
    <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"> -->
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <!-- <?php echo "/" . $mail ?? 'null' . "/"; ?>
  <?php echo "/" . $pass ?? 'null' . "/"; ?> -->
  <div class="login-container">
      <h1>ログイン画面</h1>
      <p><a href="join/index.php">ユーザでない方は登録へ</a></p>
      <form action="" method="post">
        <input type="text" placeholder="Mail Address" name="mail" size="100" value="<?php echo $mail ?? ''; ?>" required>
        <input type="password" placeholder="Password" name="pass" size="20" value="<?php echo $pass ?? ''; ?>" required>
        <?php if (isset($error['access'])): ?>
        <span class="error"><?php echo $error['access']; ?></span>
        <?php endif; ?>
        <?php if (isset($error['login'])): ?>
        <span class="error"><?php echo $error['login']; ?></span>
        <?php endif; ?>
        <button type="submit">ログイン</button>
      </form>
    </div>
</body>
</html>