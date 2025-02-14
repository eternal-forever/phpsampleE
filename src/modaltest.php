<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/style.css">
    <script>
        function reply_open(id) {
            var rep = document.getElementById("post"+id);
            rep.style.display = rep.style.display == "block" ? "none" : "flow";
            var parent = rep.parentNode;
            parent.style.display = parent.style.display == "block" ? "none" : "flow";
        }
    </script>
</head>
<body>
    <div class="board-container">
        <div class="mess">
            <form action="" method="post">
                <textarea name="message" rows="5" placeholder="今の気分は？"></textarea>
                <div class="mess-actions">
                    <button type="submit">投稿</button>
                </div>
            </form>
        </div>
        <div class="post">
            <div class="post-header">
                <img src="member_image/noimage.jpg" alt="" width="48" height="48">
                <span>投稿者: User1</span>
            </div>
            <div class="post-content">
                これはサンプルの投稿内容です。
            </div>
            <div class="post-actions">
                <button>いいね</button>
                <button onclick="reply_open(4);">コメント</button>
                <div class="modal">
                    <div class="replys" id="post4">
                        <form action="" method="post">
                            <textarea name="reply" rows="10" placeholder="コメント">中身のサンプル</textarea>
                            <button type="submit">書き込み</button>
                            <button onclick="reply_open(4);return false;">キャンセル</button>
                            <input type="hidden" name="4">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>