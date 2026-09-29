<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modal Test</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function reply_open(id) {
            var rep = document.getElementById("post"+id);
            var parent = rep.parentNode;
            var isOpen = rep.style.display === "block";
            rep.style.display = isOpen ? "none" : "block";
            parent.style.display = isOpen ? "none" : "block";
        }
    </script>
</head>
<body>
    <div class="board-container">
        <div class="mess">
            <form action="" method="post">
                <textarea name="message" rows="5" placeholder="今の気分は？"></textarea>
                <div class="mess-actions"><button type="submit">投稿</button></div>
            </form>
        </div>
        <div class="post">
            <div class="post-header">
                <img src="member_image/noimage.jpg" alt="" width="48" height="48">
                <span>投稿者: User1</span>
            </div>
            <div class="post-content">これはサンプルの投稿内容です。</div>
            <div class="post-actions">
                <button>いいね</button>
                <button onclick="reply_open(4);">コメント</button>
                <div class="modal">
                    <div class="replys" id="post4">
                        <form action="" method="post">
                            <textarea name="reply" rows="5" placeholder="コメント">中身のサンプル</textarea>
                            <button type="submit">書き込み</button>
                            <button type="button" onclick="reply_open(4);return false;">キャンセル</button>
                            <input type="hidden" name="hid" value="4">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
