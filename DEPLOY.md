# 🚀 Hướng Dẫn Deploy Lên cPanel

## Bước 1: Chuẩn Bị Database

1. Đăng nhập **cPanel** → **MySQL Databases**
2. Tạo database mới (VD: `shopacc_db`)
3. Tạo user mới (VD: `shopacc_user`) với password mạnh
4. **Add User to Database** → chọn **ALL PRIVILEGES**
5. Ghi lại 3 thông tin: `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

---

## Bước 2: Upload Code

### Cách 1: File Manager (đơn giản)
1. Nén toàn bộ thư mục dự án thành **`mxh_web.zip`**
2. Upload zip lên **`public_html/`** qua **File Manager**
3. **Extract** tại chỗ
4. Kết quả: `public_html/` chứa `.htaccess`, `public/`, `app/`, `core/`, ...

### Cách 2: Git (nâng cao)
```bash
cd ~/public_html
git clone <your-repo-url> .
```

---

## Bước 3: Cấu Hình `.env`

1. Mở file **`.env.production`** → copy nội dung
2. Tạo file **`.env`** tại `public_html/` (hoặc rename `.env.production` → `.env`)
3. Sửa các giá trị:

```env
APP_URL=https://your-domain.com    # ← Không có trailing slash!
APP_DEBUG=false

DB_DATABASE=cpanel_prefix_shopacc_db
DB_USERNAME=cpanel_prefix_shopacc_user
DB_PASSWORD=your_strong_password

BANK_API_TOKEN=your_real_api_token  # ← Token thật từ thueapibank.vn
```

> ⚠️ **QUAN TRỌNG**: `APP_URL` KHÔNG có `/public` ở cuối. Chỉ cần domain root.

---

## Bước 4: Chạy Migration

Truy cập trình duyệt:
```
https://your-domain.com/database/migration_all.php
```

Hoặc SSH:
```bash
cd ~/public_html
php database/migration_all.php
```

Sau khi chạy xong → **Xóa file `migration_all.php`** để bảo mật!

---

## Bước 5: Tạo Tài Khoản Admin

```sql
-- Chạy trong phpMyAdmin:
INSERT INTO users (username, email, password, role, balance)
VALUES ('admin', 'admin@domain.com', '$2y$10$...hash...', 'admin', 0);
```

Hoặc đăng ký tài khoản bình thường, rồi vào phpMyAdmin đổi `role` thành `admin`.

---

## Bước 6: Cấu Hình Cron Jobs

**cPanel → Cron Jobs** → thêm:

```bash
# Nạp tiền tự động (mỗi 1 phút)
* * * * * php ~/public_html/cron/deposit_cron.php --type=mbbank >> ~/logs/deposit.log 2>&1

# Đồng bộ đơn SMM (mỗi 5 phút)
*/5 * * * * php ~/public_html/cron/smm_sync_orders.php >> ~/logs/smm_orders.log 2>&1

# Đồng bộ dịch vụ SMM (mỗi 6 tiếng)
0 */6 * * * php ~/public_html/cron/smm_sync_services.php >> ~/logs/smm_services.log 2>&1
```

> 💡 Tạo thư mục `~/logs/` trước: `mkdir -p ~/logs`

---

## Bước 7: Kiểm Tra

### Checklist
- [ ] Website load OK (không lỗi 500)
- [ ] CSS/JS load OK (F12 → Network → không có file 404)
- [ ] Đăng ký tài khoản mới OK
- [ ] Đăng nhập OK
- [ ] Trang admin: `/admin` hiển thị OK
- [ ] Nạp tiền: tạo hóa đơn OK
- [ ] Cron job chạy (check `~/logs/deposit.log`)

---

## Xử Lý Lỗi Thường Gặp

### Lỗi 500 Internal Server Error
- Kiểm tra `.env` đã có chưa và DB credentials đúng chưa
- Bật `APP_DEBUG=true` tạm thời để xem lỗi chi tiết
- Kiểm tra PHP version ≥ 7.4

### CSS/JS không load
- Kiểm tra `APP_URL` trong `.env` — phải đúng domain, KHÔNG có trailing slash
- Mở F12 → tab Network → xem URL CSS/JS đang gọi có đúng không

### Lỗi "RewriteEngine not found"
- Bật **mod_rewrite** trong cPanel → **Apache Modules** (hoặc liên hệ hosting)

### Lỗi CSRF / Phiên hết hạn
- Kiểm tra session save path: `session_save_path()` phải writable
- Thử thêm vào `.env`: `SESSION_PATH=/tmp`

---

## Bảo Mật

- ✅ `.htaccess` đã chặn truy cập `.env`, `.sql`, `.git`
- ✅ `APP_DEBUG=false` trên production
- ✅ Xóa `database/migration_all.php` sau khi chạy
- ✅ Đổi password admin mạnh
- ✅ Cài SSL (HTTPS) — cPanel → SSL/TLS → Let's Encrypt
