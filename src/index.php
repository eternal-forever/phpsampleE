<?php
session_start();
require('dbconnect.php');

//login.php経由でない場合はLOGINに戻る
if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

//POSTされた場合
if (!empty($_POST)) {
    //メッセージの書き込み
    if (!empty($_POST['message'])) {
        $message = h($_POST['message']);
        $mid = $_SESSION['id'];
        if ($message!='') {
            try {
                $statement = $db->prepare('insert into messages (message,mid,mecre,deleted) values(?,?,now(),false);');
                $statement->bindParam(1, $message,PDO::PARAM_STR);
                $statement->bindParam(2, $mid,PDO::PARAM_INT);
                $statement->execute();
            } catch(PDOException $e) {
                $error['insert'] = "書き込みに失敗しました。";
            }
        }
    //コメント書き込み
    } elseif (!empty($_POST['reply'])) {
        $reply = h($_POST['reply']);
        $meid = $_POST['hid'];
        $mid = $_SESSION['id'];
        if ($reply!='') {
            try {
                $statement = $db->prepare('insert into comments (meid,mid,comment,cocre) values(?,?,?,now());');
                $statement->bindParam(1, $meid,PDO::PARAM_INT);
                $statement->bindParam(2, $mid,PDO::PARAM_INT);
                $statement->bindParam(3, $reply,PDO::PARAM_STR);
                $statement->execute();
            } catch(PDOException $e) {
                $error['insert'] = "書き込みに失敗しました。";
            }
        }
    //メッセージ削除
    } elseif (!empty($_POST['delmess'])) {
        $meid = $_POST['delmess'];
        try {
            $statement = $db->prepare('update messages set deleted=true where meid=?;');
            $statement->bindParam(1, $meid,PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e) {
            $error['insert'] = "削除に失敗しました。";
        }
    }
}
 
try {
    //全体のメッセージの一覧
    $posts = $db->query('select s1.mid,s1.meid,s1.message,mb.picture,mb.mname,s1.mecre,'.
        'COALESCE((select count(*) from comments as s2 where s1.meid=s2.meid group by meid),0) as comcnt '.
        'from messages as s1 '.
        'inner join members mb on s1.mid=mb.mid ' .
        'where s1.deleted=false '.
        'order by s1.meid desc;'
    );

    //全体のコメントの一覧
    $comments = $db->query('select co.cid,co.comment,co.meid,co.mid,co.cocre,mb.mname from (comments co '.
        'inner join messages me on co.meid=me.meid )'.
        'inner join members mb on mb.mid=co.mid '.
        'where me.deleted=false '.
        'order by meid desc,cid desc;'
    );
    $comment = $comments->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error['select'] = $e->getMessage();
}

$commentRow = 0; //コメントの行番号
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
            <p class="error">
            <?php echo $error['insert'];?>
            </p>
            <?php endif; ?>
            <?php if (!empty($error['select'])) : ?>
            <p class="error">
            <?php echo $error['select'];?>
            </p>
            <?php endif; ?>
            <form action="" method="post">
                <textarea name="message" rows="5" placeholder="今の気分は？"></textarea>
                <div class="mess-actions">
                    <button type="submit">投稿</button>
                </div>
            </form>
        </div>
        <?php
        foreach($posts as $post) :
        ?>
        <div class="post">
            <div class="post-header">
                <img src="member_image/<?php echo h($post['picture']); ?>" alt="" width="48" height="48">
                <span>投稿者: <?php echo h($post['mname']); ?></span>
                <span><time datetime="<?php echo h($post['mecre']); ?>"><?php echo h($post['mecre']); ?></time></span>
                <div class="post-info">
                    <span>
                    <?php if ($post['mid'] == $_SESSION['id']): ?>    
                    <form action="" method="post" onsubmit="return confirm('削除してよろしいですか？');">
                    <button type="submit"><i class="fa-solid fa-trash warning"></i></button>
                    <input type="hidden" name="delmess" value="<?php echo $post['meid'];?>">
                    </form>
                    <?php endif; ?>
                    </span>
                </div>
            </div>
            <div class="post-content">
                <?php echo h($post['message']); ?>
            </div>
            <div class="post-actions">
                <button onclick="reply_open(<?php echo h($post['meid']); ?>);">コメント</button>
                <div class="modal">
                    <div class="replys" id="post<?php echo h($post['meid']); ?>">
                        <form action="" method="post">
                            <textarea name="reply" rows="10" placeholder="コメントを入力してください"></textarea>
                            <button type="submit">書き込み</button>
                            <button onclick="reply_open(<?php echo ($post['meid']); ?>);return false;">キャンセル</button>
                            <input type="hidden" name="hid" value="<?php echo h($post['meid']); ?>">
                        </form>
                    </div>
                </div>
            </div>
            <div class="comments">
                <?php for($c = 0; $c < $post['comcnt']; $c++): ?>
                <div class="comment">
                    <div class="comment-header">
                        <span>コメント者: <?php echo h($comment[$commentRow]['mname']); ?></span>
                        <span><time datetime="<?php echo h($comment[$commentRow]['cocre']); ?>"><?php echo h($comment[$commentRow]['cocre']); ?></time></span>
                    </div>
                    <div class="comment-content">
                        <?php echo h($comment[$commentRow]['comment']); ?>
                    </div>
                    <div class="comment-actions">
                    </div>
                </div>
                <?php
                    $commentRow++;
                endfor;
                ?>
            </div>
        </div>
        <?php
        endforeach;
        ?>
    </div>
    <script src="js/ajax.js"></script>
    <script>
        function reply_open(id) {
            var rep = document.getElementById("post"+id);
            rep.style.display = rep.style.display == "block" ? "none" : "flow";
            var parent = rep.parentNode;
            parent.style.display = parent.style.display == "block" ? "none" : "flow";
        }
    </script>
</body>
</html>
