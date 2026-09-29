<?php
require_once('../dbconnect.php');
session_start();

if (!isset($_SESSION['join'])) {
    header('Location: index.php');
    exit();
}

$error = [];

if (!empty($_POST)) {
    if (!isset($_POST['csrf_token']) || !check_token($_POST['csrf_token'])) {
        $error['insert'] = '不正なリクエストです';
    } else {
        $mname = $_SESSION['join']['name'];
        $email = $_SESSION['join']['mail'];
        $motopass = $_SESSION['join']['pass'];
        $pass = password_hash($motopass, PASSWORD_DEFAULT);
        $image = $_SESSION['join']['image'];

        try {
            $stmt = $db->prepare('INSERT INTO members (mname,email,pass,picture,created) VALUES (?,?,?,?,NOW())');
            $stmt->execute([$mname, $email, $pass, $image]);
            header('Location: thanks.php');
            exit();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error['insert'] = '登録に失敗しました。時間をおいて再度お試しください。';
        }
    }
}

$csrf = generate_token();
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
        <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>">
        <dl>
          <dt>ニックネーム</dt>
          <dd><?php echo h($_SESSION['join']['name']); ?></dd>
          <dt>メールアドレス</dt>
          <dd><?php echo h($_SESSION['join']['mail']); ?></dd>
          <dt>パスワード</dt>
          <dd>表示しません</dd>
          <dt>画像データ</dt>
          <dd>
            <?php
                $pic = basename($_SESSION['join']['image']);
                if (!preg_match('/\.(jpg|jpeg|png|gif)$/i', $pic)) $pic = 'noimage.jpg';
            ?>
            <img src="../member_image/<?php echo h($pic); ?>" alt="">
          </dd>
        </dl>
        <button type="submit">登録</button>
        <button type="button" class="cancel" onclick="location.href='index.php?mode=redo'">キャンセル</button>
        <?php if (isset($error['insert'])): ?>
        <span class="error"><?php echo h($error['insert']); ?></span>
        <?php endif; ?>
      </form>
    </div>
</body>
</html>
