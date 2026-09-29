<?php
session_start();
require_once('../dbconnect.php');

$error = [];

// ファイルバリデーション関数
function is_valid_image(string $filename, string $tmpPath): bool {
    $allowedExt = ['jpg','jpeg','png','gif'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (!in_array($ext, $allowedExt, true)) return false;

    // MIMEチェック
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);
    $allowedMime = ['image/jpeg','image/png','image/gif'];
    return in_array($mime, $allowedMime, true);
}

if (!empty($_POST)) {
    $name = trim($_POST['name'] ?? '');
    $mail = trim($_POST['mail'] ?? '');
    $pass = $_POST['pass'] ?? '';

    // サーバ側バリデーション
    if (mb_strlen($name) < 4 || mb_strlen($name) > 100) {
        $error['name'] = 'ニックネームは4〜100文字で入力してください';
    }
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $error['mail'] = 'メールアドレスの形式が正しくありません';
    }
    if (mb_strlen($pass) < 8 || mb_strlen($pass) > 72) { // bcryptは72バイトまで
        $error['pass'] = 'パスワードは8〜72文字で入力してください';
    }

    // ファイルチェック
    $fileName = $_FILES['image']['name'] ?? '';
    $hasFile = $fileName !== '' && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE;

    if ($hasFile) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $error['image'] = 'ファイルアップロードに失敗しました';
        } elseif ($_FILES['image']['size'] > 2 * 1024 * 1024) {
            $error['image'] = 'ファイルは2MB以内にしてください';
        } elseif (!is_valid_image($fileName, $_FILES['image']['tmp_name'])) {
            $error['image'] = '未対応のファイルタイプです (jpg/png/gifのみ)';
        }
    }

    // 重複チェック
    if (empty($error)) {
        try {
            $member = $db->prepare('SELECT COUNT(*) as cnt FROM members WHERE email=?');
            $member->execute([$mail]);
            $record = $member->fetch();
            if ($record['cnt'] > 0) {
                $error['mail'] = '登録済みのメールアドレスです';
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $error['db'] = 'データベースエラーが発生しました';
        }
    }

    if (empty($error)) {
        // 画像保存
        if (!$hasFile) {
            $image = 'noimage.jpg';
        } else {
            if (DIRECTORY_SEPARATOR == '\\') {//windowsの場合
                //\windowsの実ファイル名はshift-jis
                //内部encodeがutf8なので全角名は文字化けするのであえてshift-jisに変更
                $imagename = mb_convert_encoding($image,"sjis","utf8");
            } else {
                //それ以外
                $imagename = $image;
            }
            if (!move_uploaded_file($_FILES['image']['tmp_name'],'../member_image/'.$imagename)) {
                $error['image'] = '画像の保存に失敗しました';
            }

        }

        if (empty($error)) {
            $_SESSION['join'] = [
                'name' => $name,
                'mail' => $mail,
                'pass' => $pass,
                'image' => $image
            ];
            header('Location: check.php');
            exit();
        }
    }
}

// 再入力
if (isset($_GET['mode']) && $_GET['mode'] === 'redo' && isset($_SESSION['join'])) {
    $_POST = $_SESSION['join'];
    $error['redo'] = true;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <title>会員登録画面</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="login-container">
      <h1>会員登録画面</h1>
      <form action="" method="post" enctype="multipart/form-data">
        <input type="text" placeholder="Nick Name" name="name" minlength="4" maxlength="100" value="<?php echo h($_POST['name'] ?? ''); ?>" required>
        <?php if (isset($error['name'])): ?><span class="error"><?php echo h($error['name']); ?></span><?php endif; ?>

        <input type="email" placeholder="Mail Address" name="mail" value="<?php echo h($_POST['mail'] ?? ''); ?>" required>
        <?php if (isset($error['mail'])): ?><span class="error"><?php echo h($error['mail']); ?></span><?php endif; ?>

        <input type="password" placeholder="Password (8文字以上)" name="pass" minlength="8" maxlength="72" value="<?php echo h($_POST['pass'] ?? ''); ?>" required>
        <?php if (isset($error['pass'])): ?><span class="error"><?php echo h($error['pass']); ?></span><?php endif; ?>

        <input type="file" class="joinFile" name="image" accept=".jpg,.jpeg,.png,.gif">
        <?php if (isset($error['image'])): ?><span class="error"><?php echo h($error['image']); ?></span><?php endif; ?>
        <?php if (isset($error['redo'])): ?><span class="warning">画像を改めて選択してください</span><?php endif; ?>
        <?php if (isset($error['db'])): ?><span class="error"><?php echo h($error['db']); ?></span><?php endif; ?>

        <button type="submit">確認</button>
      </form>
    </div>
</body>
</html>
