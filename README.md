# TechSavvy Auth

Dịch vụ xác thực Laravel 12 + [Laravel Passport](https://laravel.com/docs/passport): OAuth2 (password grant, refresh token), luồng **Authorization Code + PKCE** với token đặt trong **cookie HttpOnly**, và đăng nhập web (session) cho màn `/oauth/authorize`.

## Yêu cầu

- PHP **^8.2** (extensions: `openssl`, `pdo`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`…)
- [Composer](https://getcomposer.org/)
- Node.js + npm (nếu build frontend Vite trong repo)
- SQLite (mặc định) hoặc MySQL/PostgreSQL

## Cài đặt nhanh

### 1. Mã nguồn và dependency PHP

```bash
git clone <repository-url> techsavvy-auth
cd techsavvy-auth
composer install
```

### 2. Môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Chỉnh `.env` cho phù hợp:

- **`APP_URL`**: URL gốc của auth server (ví dụ `http://localhost:8000`).
- **Database**: mặc định SQLite — tạo file nếu chưa có:

  ```bash
  touch database/database.sqlite
  ```

  Hoặc cấu hình `DB_*` cho MySQL/PostgreSQL.

- **Session**: mặc định `SESSION_DRIVER=database` — cần chạy migrate (bước 3) để có bảng `sessions`.
- **Passport / CORS / cookie OAuth** (khi dùng PKCE + SPA): xem mục [Biến môi trường quan trọng](#biến-môi-trường-quan-trọng).

### 3. Database và Passport

```bash
php artisan migrate
php artisan passport:install
```

Lệnh `passport:install` tạo khóa mã hóa và (tùy phiên bản) các client mẫu. Giữ **client secret** của client dùng cho password grant / API nội bộ ở nơi an toàn.

Nếu gặp lỗi *Personal access client not found for 'users' user provider*, tạo client personal access (bắt buộc khi code gọi `User::createToken()` / `PassportTokenService`):

```bash
php artisan passport:client --personal --name="Personal access client" --provider=users
```

### 4. OAuth clients bổ sung

- **Password grant** (dùng với `POST /api/v1/auth/login/password`, `register`, `refresh`): tạo client kiểu password qua Passport:

  ```bash
  php artisan passport:client --password --name="Backend or mobile"
  ```

- **Authorization Code + PKCE (SPA, public client)**:

  ```bash
  php artisan oauth:client:create "SPA" \
    --grant=authorization_code \
    --public \
    --redirect=https://your-spa.example.com/auth/callback
  ```

  (Lệnh `oauth:client:create` được định nghĩa trong [`routes/console.php`](routes/console.php).)

### 5. Frontend assets (tùy chọn)

```bash
npm install
npm run build   # hoặc npm run dev khi phát triển
```

### 6. Chạy ứng dụng

```bash
php artisan serve
```

Truy cập theo `APP_URL` (ví dụ `http://127.0.0.1:8000`).

## Biến môi trường quan trọng

| Biến | Ý nghĩa |
|------|--------|
| `PASSPORT_AUTHORIZE_GUARD` | Guard session cho `/oauth/authorize` — mặc định `web`. |
| `CORS_ALLOWED_ORIGINS` | Danh sách origin (phân tách bằng dấu phẩy) được phép gọi API kèm cookie/credentials. |
| `CORS_SUPPORTS_CREDENTIALS` | Bật CORS credentials (cần cho SPA + `fetch(..., { credentials: 'include' })`). |
| `OAUTH_ACCESS_TOKEN_COOKIE` / `OAUTH_REFRESH_TOKEN_COOKIE` | Tên cookie HttpOnly sau `POST /api/v1/oauth/pkce/token`. |
| `OAUTH_TOKEN_COOKIE_SECURE` | `true` trên HTTPS production; local HTTP có thể đặt `false`. |
| `OAUTH_TOKEN_COOKIE_SAMESITE` | `lax` (mặc định) hoặc `none` khi SPA khác site (kèm `Secure=true`). |

Chi tiết thêm trong [`.env.example`](.env.example).

## Kiểm thử

```bash
php artisan test
```

## Luồng API (tóm tắt)

| Mục đích | Endpoint / ghi chú |
|----------|-------------------|
| Đăng ký / login password / refresh (JSON token) | `POST /api/v1/auth/register`, `.../login/password`, `.../refresh` |
| PKCE: đổi `code` → token **chỉ trong cookie HttpOnly** | `POST /api/v1/oauth/pkce/token` |
| Thông tin user (Bearer hoặc cookie access đã cấu hình middleware) | `GET /api/v1/auth/me` |
| Đăng xuất (revoke + xóa cookie OAuth) | `POST /api/v1/auth/logout` |
| OAuth2 chuẩn (authorize / token) | `GET /oauth/authorize`, `POST /oauth/token` (Passport) |
| Đăng nhập web (session) cho PKCE | `GET/POST /login` trên auth server |

Luồng PKCE: mở `/oauth/authorize` với `code_challenge` (S256) → user đăng nhập tại `/login` nếu chưa có session → redirect về SPA với `code` → SPA gọi `POST /api/v1/oauth/pkce/token` với `credentials: 'include'`.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
