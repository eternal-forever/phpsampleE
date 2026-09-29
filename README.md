# phpsampleE 修正版

元の phpsample からいいね機能を除いた簡易版のセキュリティ修正版です。

## 主な修正点
- `h()` の二重エスケープを修正 (保存時は生データ、表示時に `h()`)
- ログイン時のパスワード `h()` 問題を修正
- 削除時の権限チェック `WHERE meid=? AND mid=?` 追加
- ファイルアップロード: pathinfo + MIMEチェック + ランダム名 + 2MB制限
- PDO: `utf8mb4` + `ERRMODE_EXCEPTION` + `require_once`
- CSRFトークン導入
- セッション固定化対策 `session_regenerate_id(true)` + タイムアウト + logout時のクッキー削除
- コメント表示を投稿IDでグルーピング
- JS `display: flow` → `block` に修正
- Dockerfile: php:8.0 → 8.2 + mbstring
- bbsFirst.sql: 外部キー + UNIQUE(email) + 不要ないいねテーブル削除
- databasetest.php: 毎回INSERTしない安全なシーダーに変更

## 起動
docker compose up -d
http://localhost:8000
http://localhost:8080 (phpMyAdmin)

## 初期DB
bbsFirst.sql を phpMyAdmin からインポート
