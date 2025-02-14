<?php
require('../dbconnect.php');
session_start();

//index.php経由でない場合は登録に戻る
if (!isset($_SESSION['join'])) {
  header('Location: index.php');
  exit();
}

if (!empty($_POST)) {
  $mname = $_SESSION['join']['name'];
  $email = $_SESSION['join']['mail'];
  $motopass = $_SESSION['join']['pass'];
  $pass = password_hash($motopass,PASSWORD_DEFAULT); 
  $image = $_SESSION['join']['image'];
  try {
      $statement = $db->prepare('insert into members (mname,email,pass,picture,created) values(?,?,?,?,now());');
      $statement->bindParam(1, $mname,PDO::PARAM_STR);
      $statement->bindParam(2, $email,PDO::PARAM_STR);
      $statement->bindParam(3, $pass,PDO::PARAM_STR);
      $statement->bindParam(4, $image,PDO::PARAM_STR);
      $statement->execute();
  } catch(PDOException $e) {
      $error['insert'] = $e->getMessage();
  }
  if (empty($error)) {
    header('Location: thanks.php');
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登録確認画面</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="check-container">
      <h1>登録確認画面</h1>
      <p>内容を確認してください</p>
      <form action="" method="post">
        <dl>
          <dt>ニックネーム</dt>
          <dd>
            <?php echo h($_SESSION['join']['name']); ?>
          </dd>
          <dt>メールアドレス</dt>
          <dd>
            <?php echo h($_SESSION['join']['mail']); ?>
          </dd>
          <dt>パスワード</dt>
          <dd>
            表示しません
          </dd>
          <dt>画像データ</dt>
          <dd>
            <img src="../member_image/<?php echo h($_SESSION['join']['image']); ?>" alt="">
          </dd>
        </dl>
        <button type="submit">登録</button>
        <button type="button" class="cancel" 
        onclick="location.href='index.php?mode=redo'">キャンセル</button>
        <?php if (isset($error['insert'])): ?>
        <span class="error"><?php echo $error['insert']; ?></span>
        <?php endif; ?>
        <input type="hidden" name="mode" value="submit">
      </form>
    </div>
</body>
</html>