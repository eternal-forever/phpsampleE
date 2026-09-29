<?php
session_start();
require_once('dbconnect.php');

// ログイン必須
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

// セッションタイムアウト (1時間)
if (isset($_SESSION['time']) && ($_SESSION['time'] + 3600 < time())) {
    header('Location: logout.php');
    exit();
}
$_SESSION['time'] = time();

$mid = (int)$_SESSION['id'];
$error = [];

// POST処理
if (!empty($_POST)) {
    // CSRFチェック
    if (!check_token($_POST['csrf_token'] ?? '')) {
        $error['insert'] = '不正なリクエストです';
    } else {
        // メッセージ書き込み
        if (isset($_POST['message'])) {
            $message = trim($_POST['message']);
            if ($message !== '') {
                if (mb_strlen($message) > 255) {
                    $error['insert'] = 'メッセージは255文字以内にしてください';
                } else {
                    try {
                        $stmt = $db->prepare('INSERT INTO messages (message, mid, mecre, deleted) VALUES (?, ?, NOW(), 0)');
                        $stmt->execute([$message, $mid]);
                    } catch (PDOException $e) {
                        error_log($e->getMessage());
                        $error['insert'] = '書き込みに失敗しました';
                    }
                }
            }
        // コメント書き込み
        } elseif (isset($_POST['reply'])) {
            $reply = trim($_POST['reply']);
            $meid = (int)($_POST['hid'] ?? 0);
            if ($reply !== '' && $meid > 0) {
                if (mb_strlen($reply) > 255) {
                    $error['insert'] = 'コメントは255文字以内にしてください';
                } else {
                    try {
                        // 対象メッセージが存在し、削除されていないか確認
                        $chk = $db->prepare('SELECT meid FROM messages WHERE meid=? AND deleted=0');
                        $chk->execute([$meid]);
                        if ($chk->fetch()) {
                            $stmt = $db->prepare('INSERT INTO comments (meid, mid, comment, cocre) VALUES (?, ?, ?, NOW())');
                            $stmt->execute([$meid, $mid, $reply]);
                        } else {
                            $error['insert'] = '対象のメッセージが存在しません';
                        }
                    } catch (PDOException $e) {
                        error_log($e->getMessage());
                        $error['insert'] = '書き込みに失敗しました';
                    }
                }
            }
        // メッセージ削除 (権限チェックあり)
        } elseif (isset($_POST['delmess'])) {
            $meid = (int)$_POST['delmess'];
            try {
                $stmt = $db->prepare('UPDATE messages SET deleted=1 WHERE meid=? AND mid=?');
                $stmt->execute([$meid, $mid]);
                if ($stmt->rowCount() === 0) {
                    $error['insert'] = '削除権限がありません';
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $error['insert'] = '削除に失敗しました';
            }
        }
    }
}

try {
    // メッセージ一覧
    $postsStmt = $db->query(
        'SELECT s1.mid, s1.meid, s1.message, mb.picture, mb.mname, s1.mecre, ' .
        '(SELECT COUNT(*) FROM comments WHERE meid=s1.meid) AS comcnt ' .
        'FROM messages AS s1 ' .
        'INNER JOIN members mb ON s1.mid=mb.mid ' .
        'WHERE s1.deleted=0 ' .
        'ORDER BY s1.meid DESC'
    );
    $posts = $postsStmt->fetchAll();

    // コメント一覧を投稿IDごとにグルーピング
    $commentsStmt = $db->query(
        'SELECT co.cid, co.comment, co.meid, co.mid, co.cocre, mb.mname ' .
        'FROM comments co ' .
        'INNER JOIN members mb ON mb.mid=co.mid ' .
        'INNER JOIN messages me ON co.meid=me.meid ' .
        'WHERE me.deleted=0 ' .
        'ORDER BY co.meid DESC, co.cid ASC'
    );
    $commentsRaw = $commentsStmt->fetchAll();
    $commentsByMeid = [];
    foreach ($commentsRaw as $c) {
        $commentsByMeid[$c['meid']][] = $c;
    }

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error['select'] = '読み込みに失敗しました';
    $posts = [];
    $commentsByMeid = [];
}

$csrf = generate_token();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>掲示板</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
    <div class="board-container">
        <div class="exit"><a href="logout.php" onclick="return confirm('logoutします');">ログアウト</a></div>
        <div class="mess">
            <?php if (!empty($error['insert'])) : ?>
            <p class="error"><?php echo h($error['insert']); ?></p>
            <?php endif; ?>
            <?php if (!empty($error['select'])) : ?>
            <p class="error"><?php echo h($error['select']); ?></p>
            <?php endif; ?>
            <form action="" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>">
                <textarea name="message" rows="5" placeholder="今の気分は？"></textarea>
                <div class="mess-actions">
                    <button type="submit">投稿</button>
                </div>
            </form>
        </div>
        <?php foreach($posts as $post) : ?>
        <?php
            // 画像名のサニタイズ (ディレクトリトラバーサル対策)
            $pic = $post['picture'] ?? 'noimage.jpg';
            $pic = basename($pic);
            if ($pic === '' || !preg_match('/\.(jpg|jpeg|png|gif)$/i', $pic)) {
                $pic = 'noimage.jpg';
            }
        ?>
        <div class="post">
            <div class="post-header">
                <img src="member_image/<?php echo h($pic); ?>" alt="" width="48" height="48">
                <span>投稿者: <?php echo h($post['mname']); ?></span>
                <span><time datetime="<?php echo h($post['mecre']); ?>"><?php echo h($post['mecre']); ?></time></span>
                <div class="post-info">
                    <span>
                    <?php if ((int)$post['mid'] === $mid): ?>
                    <form action="" method="post" onsubmit="return confirm('削除してよろしいですか？');">
                        <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>">
                        <button type="submit"><i class="fa-solid fa-trash warning"></i></button>
                        <input type="hidden" name="delmess" value="<?php echo h($post['meid']); ?>">
                    </form>
                    <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="post-content">
                <?php echo nl2br(h($post['message'])); ?>
            </div>
            <div class="post-actions">
                <button onclick="reply_open(<?php echo (int)$post['meid']; ?>);">コメント</button>
                <div class="modal" id="modal<?php echo (int)$post['meid']; ?>">
                    <div class="replys" id="post<?php echo (int)$post['meid']; ?>">
                        <form action="" method="post">
                            <input type="hidden" name="csrf_token" value="<?php echo h($csrf); ?>">
                            <textarea name="reply" rows="10" placeholder="コメントを入力してください"></textarea>
                            <button type="submit">書き込み</button>
                            <button type="button" onclick="reply_open(<?php echo (int)$post['meid']; ?>);return false;">キャンセル</button>
                            <input type="hidden" name="hid" value="<?php echo (int)$post['meid']; ?>">
                        </form>
                    </div>
                </div>
            </div>
            <div class="comments">
                <?php if (!empty($commentsByMeid[$post['meid']])): ?>
                    <?php foreach($commentsByMeid[$post['meid']] as $comment): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <span>コメント者: <?php echo h($comment['mname']); ?></span>
                            <span><time datetime="<?php echo h($comment['cocre']); ?>"><?php echo h($comment['cocre']); ?></time></span>
                        </div>
                        <div class="comment-content">
                            <?php echo nl2br(h($comment['comment'])); ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <script>
        function reply_open(id) {
            var modal = document.getElementById("modal"+id);
            var rep = document.getElementById("post"+id);
            if (!modal || !rep) return;
            // modal と replys の表示をトグル
            var isOpen = modal.style.display === "block";
            modal.style.display = isOpen ? "none" : "block";
            rep.style.display = isOpen ? "none" : "block";
        }
        // モーダル背景クリックで閉じる
        document.addEventListener('click', function(e){
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
                var inner = e.target.querySelector('.replys');
                if (inner) inner.style.display = 'none';
            }
        });
    </script>
</body>
</html>
