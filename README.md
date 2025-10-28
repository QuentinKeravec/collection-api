# 📚 コレクション管理 API — Laravel 11

個人のコレクション（アニメ・マンガ・映画・シリーズ・本など）を管理するための **Laravel 製 REST API** です。  
タグ機能・お気に入り機能・ローカル画像の自動コピーをサポートしています。  
フロントエンドは React（`collection-client`）で接続できます。

---

## ⚙️ 技術スタック

- **Laravel 11**
- **PHP 8.2+**
- **SQLite** または **MySQL**
- **Storage (public ディスク)** による画像保存
- **Seeder (DemoSeeder)** によるデモデータ生成

---

## 🧩 主な機能

- アイテム（作品）の CRUD 操作
- タグ機能（多対多リレーション）
- お気に入り機能（ユーザーとアイテムの中間テーブル）
- ローカルストレージに画像を保存・削除
- デモユーザーとサンプルデータを自動生成
- React フロントエンドから利用可能な REST API

---

## 📦 セットアップ

    bash
    git clone https://github.com/QuentinKeravec/collection-api.git
    cd collection-api
    composer install
    cp .env.example .env
    php artisan key:generate

.env にデータベースを設定します。
SQLite の場合は次のように設定できます：

    DB_CONNECTION=sqlite
    DB_DATABASE=/absolute/path/to/database/database.sqlite

---

## 💾 デモデータの生成

php artisan migrate:fresh --seed --seeder=DemoSeeder

このコマンドで以下が自動生成されます：

- デモユーザー：`demo@example.com` / `password`
- サンプルアイテム（Fullmetal Alchemist, One Piece, Breaking Bad など）
- 関連するタグ（例：action epic、drame、aventure など）
- ランダムなお気に入り登録
- `database/seeders/images/` の画像を `storage/app/public/items/` にコピー

初回のみストレージリンクを作成してください：

    php artisan storage:link

## 🖼️ 画像の仕組み

- 画像ファイルは `storage/app/public/items/` に保存されます
- データベースには相対パス（例：`items/BreakingBad.png`）を保存
- 画像が存在しない場合は `public/images/no-image.png` にフォールバック

## 🔗 API レスポンス例

    {
      "id": 3,
      "title": "Breaking Bad",
      "type": "serie",
      "year": 2008,
      "author": "Vince Gilligan",
      "description": "Un professeur de chimie de lycée chez...",
      "image_url": "/storage/items/BreakingBad.png",
      "tags": ["thriller", "drame"],
      "is_favorite": true
    }

## 🧠 主な API ルート

| メソッド   | エンドポイント               | 説明          |
| ------ | --------------------- | ----------- |
| GET    | `/api/items`          | アイテム一覧を取得   |
| GET    | `/api/items/{id}`     | 詳細を取得       |
| POST   | `/api/items`          | アイテムを作成     |
| PUT    | `/api/items/{id}`     | アイテムを更新     |
| DELETE | `/api/items/{id}`     | アイテムを削除     |
| GET    | `/api/tags`           | タグ一覧を取得     |
| POST   | `/api/favorites/{id}` | お気に入りを追加／削除 |

## 💻 フロントエンド（collection-client）

React + Vite + TailwindCSS 製のフロントエンドと連携します。
.env に API エンドポイントを指定してください。

例：

    VITE_API_BASE_URL=http://localhost:8000/api

起動：

    npm install
    npm run dev

## 👤 デモユーザー

| メールアドレス            | パスワード      |
| ------------------     | ---------- |
| `demo@example.com`     | `password` |

## 🧰 よく使うコマンド
| 操作          | コマンド                                                   |
| ----------- | ------------------------------------------------------ |
| データベースをリセット | `php artisan migrate:fresh`                            |
| デモデータを生成    | `php artisan db:seed --class=DemoSeeder`               |
| リセット＋シード一括  | `php artisan migrate:fresh --seed --seeder=DemoSeeder` |
| ストレージリンク作成  | `php artisan storage:link`                             |
| 開発サーバー起動    | `php artisan serve`                                    |
