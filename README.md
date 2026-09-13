# お問い合わせフォーム

お問い合わせの送信・管理を行うWebアプリケーションです。

一般ユーザーはお問い合わせフォームからお問い合わせを送信でき、管理者は管理画面からお問い合わせの検索・詳細確認・削除・CSV出力などを行うことができます。

また、REST APIを利用して、お問い合わせ情報の取得・登録・更新・削除を行うことができます。

## 環境構築

### リポジトリの取得

```bash
git clone https://github.com/yumi1208/contact-form-app.git
cd contact-form-app
```

### Docker起動

```bash
docker compose up -d --build
```

### Laravel環境構築

```bash
cp .env.example .env
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate:fresh --seed
```

## 使用技術

- PHP 8.x
- Laravel 10.x
- MySQL 8.0
- Laravel Fortify
- Laravel Sail
- Docker
- phpMyAdmin
- PHPUnit
- HTML
- CSS

## 主な機能

### お問い合わせ機能

- お問い合わせフォーム表示
- お問い合わせ内容確認
- お問い合わせ登録
- サンクスページ表示

### 認証機能

- ユーザー登録
- ログイン
- ログアウト

### 管理機能

- お問い合わせ一覧表示
- キーワード検索
- 性別による絞り込み
- お問い合わせ種類による絞り込み
- 日付による絞り込み
- ページネーション
- お問い合わせ詳細表示
- お問い合わせ削除
- CSVエクスポート

### タグ管理機能

- タグ追加
- タグ編集
- タグ削除
- お問い合わせとタグの紐付け

### API機能

- お問い合わせ一覧取得
- お問い合わせ詳細取得
- お問い合わせ登録
- お問い合わせ更新
- お問い合わせ削除
- バリデーション
- ページネーション

## URL

| ページ               | URL                       |
| -------------------- | ------------------------- |
| お問い合わせフォーム | http://localhost/         |
| 管理画面             | http://localhost/admin    |
| ユーザー登録         | http://localhost/register |
| ログイン             | http://localhost/login    |
| phpMyAdmin           | http://localhost:8080     |

## API

| メソッド | エンドポイント               | 内容                 |
| -------- | ---------------------------- | -------------------- |
| GET      | `/api/v1/contacts`           | お問い合わせ一覧取得 |
| GET      | `/api/v1/contacts/{contact}` | お問い合わせ詳細取得 |
| POST     | `/api/v1/contacts`           | お問い合わせ登録     |
| PUT      | `/api/v1/contacts/{contact}` | お問い合わせ更新     |
| DELETE   | `/api/v1/contacts/{contact}` | お問い合わせ削除     |

## データベース

本アプリケーションでは以下のテーブルを使用しています。

- users
- categories
- contacts
- tags
- contact_tag

`categories` と `contacts` は1対多の関係です。

`contacts` と `tags` は多対多の関係になっており、`contact_tag` を中間テーブルとして使用しています。

## 初期データ

以下のコマンドでデータベースを再構築し、初期データを登録できます。

```bash
./vendor/bin/sail artisan migrate:fresh --seed
```

以下のSeederを使用しています。

- UserSeeder
- CategorySeeder
- TagSeeder
- ContactSeeder

## テスト

以下のコマンドでテストを実行できます。

```bash
./vendor/bin/sail artisan test
```

カバレッジを確認する場合：

```bash
./vendor/bin/sail artisan test --coverage
```

現在のテスト結果：

- 30 tests passed
- 91 assertions
- Coverage 78.7%

## ER図

本アプリケーションでは、以下の5つのテーブルでデータを管理しています。

- users
- categories
- contacts
- tags
- contact_tag

`categories` と `contacts` は1対多、`contacts` と `tags` は `contact_tag` を介した多対多の関係です。

ER図は確認テストの要件シートにも記載しています。
