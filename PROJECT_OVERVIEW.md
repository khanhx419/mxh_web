# 📘 ShopAcc VN — Tài Liệu Tổng Quan Dự Án

> **Tên dự án:** ShopAcc VN (mxh_web)  
> **Mô tả:** Website bán tài khoản game, dịch vụ MXH (buff like/follow/...), tích hợp nạp tiền tự động, vòng quay may mắn, túi mù (blind box), hệ thống điểm xanh, cờ vua.  
> **Tech Stack:** PHP thuần (Custom MVC), MySQL, XAMPP/cPanel  
> **Domain production:** metaultra.shop  
> **Cập nhật lần cuối:** 2026-04-07

---

## 📁 Cấu Trúc Thư Mục

```
mxh_web/
├── .env                          # Biến môi trường (DB, API keys, ...)
├── .env.production               # Config cho production (cPanel)
├── .htaccess                     # Redirect all → public/, force HTTPS
├── database.sql                  # SQL export backup
│
├── public/                       # ★ Document Root (entry point)
│   ├── index.php                 # ★ Entry point - Router + tất cả routes
│   ├── .htaccess                 # Rewrite mọi request → index.php
│   ├── css/style.css             # CSS chính (85KB)
│   ├── js/app.js                 # JS chính (16KB)
│   └── uploads/                  # Upload ảnh sản phẩm
│
├── core/                         # Framework core (tự viết)
│   ├── Router.php                # Hệ thống routing (GET/POST, params {id})
│   ├── Controller.php            # Base Controller (view, json, model, validate)
│   └── Model.php                 # Base Model (CRUD, findAll, findWhere, raw query)
│
├── config/
│   ├── app.php                   # Define APP_NAME, APP_URL, APP_DEBUG
│   └── database.php              # getDatabaseConnection() → PDO singleton
│
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php         # Login/Register/Logout
│   │   ├── HomeController.php         # Trang chủ
│   │   ├── User/                      # Controllers cho user frontend
│   │   │   ├── ShopController.php     # Shop acc game + dịch vụ MXH
│   │   │   ├── BankingController.php  # Nạp tiền (bank, thẻ cào)
│   │   │   ├── OrderController.php    # Xem đơn hàng
│   │   │   ├── ProfileController.php  # Trang cá nhân
│   │   │   ├── PageController.php     # Search, leaderboard, events, green points, guide, contact
│   │   │   ├── LuckyWheelController.php  # Vòng quay may mắn
│   │   │   ├── MysteryBagController.php  # Túi mù (blind box)
│   │   │   └── ChessController.php       # Game cờ vua
│   │   └── Admin/                     # Controllers cho admin panel
│   │       ├── DashboardController.php    # Admin dashboard
│   │       ├── CategoryController.php     # CRUD danh mục
│   │       ├── ProductController.php      # CRUD sản phẩm (acc game)
│   │       ├── ServiceController.php      # CRUD dịch vụ MXH
│   │       ├── OrderController.php        # Quản lý đơn hàng
│   │       ├── UserController.php         # Quản lý users
│   │       ├── InvoiceController.php      # Xem hóa đơn nạp tiền
│   │       ├── LuckyWheelController.php   # Cấu hình vòng quay
│   │       ├── MysteryBagController.php   # CRUD túi mù + items + bulk import
│   │       ├── EventController.php        # CRUD sự kiện
│   │       └── SettingsController.php     # Cài đặt chung (bank, SMM, Telegram)
│   │
│   ├── Models/                    # 14 Models
│   │   ├── User.php
│   │   ├── Category.php
│   │   ├── Product.php
│   │   ├── Service.php
│   │   ├── Order.php
│   │   ├── Invoice.php
│   │   ├── Transaction.php
│   │   ├── Setting.php
│   │   ├── LuckyWheelPrize.php
│   │   ├── MysteryBag.php
│   │   ├── DailyCheckin.php
│   │   ├── GreenPoint.php
│   │   ├── Event.php
│   │   └── CardList.php
│   │
│   ├── Services/                  # External API services
│   │   ├── SmmApiService.php      # Gọi API SMM Panel (sub6sao.com)
│   │   └── TelegramService.php    # Gửi thông báo qua Telegram Bot
│   │
│   ├── Helpers/
│   │   └── helpers.php            # Hàm tiện ích toàn cục
│   │
│   └── Middleware/
│       └── AuthMiddleware.php     # Kiểm tra login & admin
│
├── views/                         # Giao diện PHP
│   ├── layouts/
│   │   ├── app.php                # Layout user (header, nav, footer)
│   │   └── admin.php              # Layout admin panel
│   ├── auth/                      # Login, Register
│   ├── home/                      # Trang chủ
│   ├── user/                      # 19 views cho user
│   ├── admin/                     # 10 thư mục admin views
│   └── errors/                    # 404, 500
│
├── cron/                          # Cron jobs
│   ├── deposit_cron.php           # Nạp tiền tự động (multi-bank + thẻ cào)
│   ├── smm_sync_orders.php        # Đồng bộ trạng thái đơn SMM
│   └── smm_sync_services.php      # Đồng bộ danh sách dịch vụ SMM
│
└── database/
    ├── migration_all.php          # Tạo toàn bộ 18 bảng + seed data
    └── seed_checkin.php           # Seed dữ liệu check-in
```

---

## ⚙️ Kiến Trúc MVC (Custom)

### Request Flow
```
Browser → .htaccess → public/index.php → Router::dispatch()
                                              ↓
                                    Controller@method($params)
                                              ↓
                                    Model (PDO queries)
                                              ↓
                                    View (PHP template + layout)
```

### Base Controller (`core/Controller.php`)
| Method | Mô tả |
|--------|--------|
| `view($view, $data, $layout)` | Render view với layout (`app` hoặc `admin`) |
| `viewOnly($view, $data)` | Render view không layout |
| `json($data, $statusCode)` | Trả JSON response + exit |
| `model($name)` | Tạo instance Model theo tên class |
| `validate($data, $rules)` | Validate input: `required`, `min:N`, `max:N`, `email`, `numeric` |

### Base Model (`core/Model.php`)
| Method | Mô tả |
|--------|--------|
| `findAll($orderBy)` | Lấy tất cả records |
| `findById($id)` | Tìm theo ID |
| `findWhere($conditions, $orderBy, $limit)` | Tìm theo điều kiện `['key' => 'value']` |
| `findOneWhere($conditions)` | Tìm 1 record theo điều kiện |
| `create($data)` | Insert record, trả về `lastInsertId` |
| `update($id, $data)` | Update record theo ID |
| `delete($id)` | Xóa record theo ID |
| `count($conditions)` | Đếm records |
| `raw($sql, $params)` | Thực thi raw query |
| `getDb()` | Lấy PDO instance |

### Router (`core/Router.php`)
| Method | Mô tả |
|--------|--------|
| `get($path, $callback)` | Đăng ký route GET |
| `post($path, $callback)` | Đăng ký route POST |
| `dispatch()` | Phân phối request → controller |
| Hỗ trợ params `{id}` → truyền vào method |

---

## 🗄️ Database Schema (18 bảng)

### Bảng Nền Tảng

| Bảng | Mô tả | Cột quan trọng |
|------|--------|-----------------|
| `users` | Người dùng | `username`, `email`, `password`, `role` (admin/user), `balance`, `green_points_total`, `chess_score` |
| `categories` | Danh mục SP/DV | `name`, `type` (game/social), `icon`, `status` |
| `products` | Tài khoản game | `category_id`, `title`, `price`, `account_info`, `image`, `status` (available/sold) |
| `services` | Dịch vụ MXH | `category_id`, `name`, `price_per_1000`, `min/max_quantity`, `smm_service_id`, `rate_original`, `refill` |
| `orders` | Đơn hàng | `user_id`, `order_type` (product/service), `product_id`/`service_id`, `quantity`, `target_link`, `total_price`, `status`, `smm_order_id`, `smm_status`, `account_data` |
| `transactions` | Lịch sử giao dịch | `user_id`, `type` (deposit/purchase/refund), `amount`, `balance_after`, `description` |

### Bảng Mở Rộng

| Bảng | Mô tả | Cột quan trọng |
|------|--------|-----------------|
| `invoices` | Hóa đơn nạp tiền | `user_id`, `trans_id` (VD: NAP59), `amount`, `pay`, `method`, `status` (0=pending, 1=done, 2=expired), `tid` (bank TxID) |
| `settings` | Cấu hình key-value | `name`, `value` |
| `lucky_wheel_prizes` | Phần thưởng vòng quay | `name`, `type` (money/product/nothing), `value`, `probability`, `color`, `status` |
| `lucky_wheel_history` | Lịch sử quay | `user_id`, `prize_id`, `prize_name`, `prize_value` |
| `mystery_bags` | Túi mù | `name`, `description`, `price`, `image`, `status` |
| `mystery_bag_items` | Items trong túi mù | `bag_id`, `name`, `value`, `content` (username/password), `probability`, `status` (1=chưa phát, 0=đã phát) |
| `mystery_bag_history` | Lịch sử mở túi | `user_id`, `bag_id`, `item_id`, `item_name`, `item_content` |
| `daily_checkin` | Lịch sử điểm danh | `user_id`, `day_number` (1-7), `cycle_start`, `free_spins_earned` |
| `user_free_spins` | Lượt quay miễn phí | `user_id`, `free_spins`, `last_checkin_date`, `cycle_start`, `current_day` |
| `events` | Sự kiện | `title`, `start_date`, `end_date`, `reward_type`, `reward_value`, `status` |
| `green_points` | Lịch sử điểm xanh | `user_id`, `points`, `reason`, `reference_type`, `reference_id` |
| `contact_messages` | Liên hệ | `name`, `email`, `subject`, `message`, `status` (new/read/replied) |
| `card_lists` | Nạp thẻ cào | `user_id`, `type`, `serial`, `code`, `amount`, `request_id`, `status` (Processing/Completed/Cancelled) |

---

## 🧩 Models — Chi Tiết Hàm

### `User` (table: `users`)
| Method | Mô tả |
|--------|--------|
| `findByUsername($username)` | Tìm user theo username |
| `findByEmail($email)` | Tìm user theo email |
| `register($data)` | Tạo user mới (auto hash password bcrypt) |
| `updateBalance($userId, $amount)` | Cộng/trừ số dư (dùng SQL `+ ?`) |
| `getBalance($userId)` | Lấy số dư hiện tại |
| `incrementField($userId, $field, $amount)` | Cộng field (`balance`, `total_deposit`, `balance_1`, `chess_score`, `green_points_total`) |

### `Product` (table: `products`)
| Method | Mô tả |
|--------|--------|
| `getAvailable($categoryId)` | Lấy SP còn hàng (JOIN categories), filter theo category |
| `getDetail($id)` | Chi tiết 1 SP (JOIN categories) |
| `getAllWithCategory()` | Tất cả SP cho admin |
| `countAvailable()` | Đếm SP còn hàng |

### `Service` (table: `services`)
| Method | Mô tả |
|--------|--------|
| `getActive($categoryId)` | DV đang hoạt động (JOIN categories) |
| `getDetail($id)` | Chi tiết 1 DV |
| `getAllWithCategory()` | Tất cả DV cho admin |
| `findBySmmId($smmServiceId)` | Tìm DV theo SMM service ID |

### `Order` (table: `orders`)
| Method | Mô tả |
|--------|--------|
| `createProductOrder($userId, $productId, $totalPrice)` | Tạo đơn mua acc game (status = completed) |
| `createServiceOrder(...)` | Tạo đơn dịch vụ SMM (status = pending, có smm_order_id) |
| `getPendingSmmOrders()` | Đơn SMM đang chờ (cho cron sync) |
| `updateSmmStatus($id, ...)` | Cập nhật trạng thái SMM |
| `getUserOrders($userId)` | Đơn hàng user (JOIN products, services, categories) |
| `getAllWithDetails()` | Tất cả đơn cho admin |
| `getDetail($id)` | Chi tiết 1 đơn |
| `totalRevenue()` | Tổng doanh thu (không tính cancelled) |

### `Invoice` (table: `invoices`)
| Method | Mô tả |
|--------|--------|
| `getUserInvoices($userId)` | Hóa đơn của user |
| `getPending()` | Hóa đơn đang chờ (cho cron) |
| `createInvoice($userId, $amount, $method)` | Tạo HĐ nạp tiền (trans_id = PREFIX + userId) |
| `findByTransId($transId)` | Tìm HĐ theo mã GD nội bộ |
| `isTransactionProcessed($bankTxId)` | Kiểm tra bank TxID đã xử lý chưa (chống nạp trùng) |

### `LuckyWheelPrize` (table: `lucky_wheel_prizes`)
| Method | Mô tả |
|--------|--------|
| `getActivePrizes()` | Phần thưởng đang hoạt động |
| `getTotalProbability()` | Tổng weight probability |
| `getPrizesWithPercentages()` | Prizes + phần trăm tính sẵn |
| `spin()` | Quay thưởng (weighted random) |
| `logHistory($userId, $prize)` | Ghi lịch sử quay |

### `MysteryBag` (table: `mystery_bags`)
| Method | Mô tả |
|--------|--------|
| `getActiveBags()` | Túi mù đang hoạt động |
| `getItems($bagId)` | Tất cả items của 1 túi |
| `getAvailableItems($bagId)` | Items chưa bán (status=1) |
| `open($bagId)` | Random lấy 1 item, đánh dấu đã phát (status=0) |
| `logHistory(...)` | Ghi lịch sử mở túi |
| `logHistoryCustom(...)` | Ghi lịch sử custom (không cần item từ DB) |

### `DailyCheckin` (table: `daily_checkin`)
| Method | Mô tả |
|--------|--------|
| `getUserSpinInfo($userId)` | Lấy thông tin lượt quay free (từ `user_free_spins`) |
| `hasCheckedInToday($userId)` | Kiểm tra đã điểm danh hôm nay chưa |
| `getCurrentCycleCheckins($userId)` | Lịch sử điểm danh trong chu kỳ 7 ngày |
| `checkin($userId)` | Thực hiện điểm danh (+spins, +green points, reset chu kỳ khi đủ 7) |
| `useFreeSpin($userId)` | Trừ 1 lượt quay free |
| `getFreeSpins($userId)` | Lấy số lượt quay free còn lại |

### `GreenPoint` (table: `green_points`)
| Method | Mô tả |
|--------|--------|
| `add($userId, $points, $reason, ...)` | Cộng điểm xanh + update users.green_points_total |
| `getUserTotal($userId)` | Tổng điểm xanh |
| `getHistory($userId, $limit)` | Lịch sử điểm xanh |
| `getTopUsers($limit)` | Top users theo điểm xanh |

### `Category` (table: `categories`)
| Method | Mô tả |
|--------|--------|
| `getGameCategories()` | Danh mục type=game |
| `getSocialCategories()` | Danh mục type=social |
| `getActive()` | Tất cả danh mục active |

### `Event` (table: `events`)
| Method | Mô tả |
|--------|--------|
| `getActive()` | Sự kiện đang diễn ra |
| `getAll()` | Tất cả sự kiện |
| `getUpcoming()` | Sự kiện sắp tới (limit 5) |

### `Setting` (table: `settings`)
| Method | Mô tả |
|--------|--------|
| `get($name, $default)` | Lấy giá trị setting |
| `set($name, $value)` | Tạo/cập nhật setting |

### `Transaction` (table: `transactions`)
| Method | Mô tả |
|--------|--------|
| `log($userId, $type, $amount, $balanceAfter, $description)` | Ghi log giao dịch |
| `getUserTransactions($userId)` | Lịch sử GD của user |

### `CardList` (table: `card_lists`)
| Method | Mô tả |
|--------|--------|
| `createCard($userId, $type, $serial, $code, $amount)` | Tạo yêu cầu nạp thẻ |
| `getProcessing()` | Thẻ đang xử lý |
| `getUserCards($userId, $limit)` | Lịch sử nạp thẻ user |

---

## 🌐 Services (API bên ngoài)

### `SmmApiService` — API SMM Panel (sub6sao.com)
| Method | Mô tả |
|--------|--------|
| `getServices()` | Lấy danh sách dịch vụ từ web mẹ |
| `addOrder($serviceId, $link, $quantity)` | Tạo đơn hàng trên web mẹ → trả `{order: int}` |
| `getOrderStatus($orderId)` | Kiểm tra trạng thái 1 đơn |
| `getMultiOrderStatus($orderIds)` | Kiểm tra nhiều đơn |
| `getBalance()` | Kiểm tra số dư web mẹ |

**Config:** `SMM_API_URL`, `SMM_API_KEY`, `SMM_PRICE_MARKUP` (% markup giá)

### `TelegramService` — Telegram Bot API
| Method | Mô tả |
|--------|--------|
| `isConfigured()` | Check đã cấu hình bot token + chat ID? |
| `sendMessage($text, $parseMode)` | Gửi tin nhắn text |
| `notifyDeposit(...)` | Thông báo nạp tiền thành công |
| `notifyOrder(...)` | Thông báo đơn hàng mới |
| `notifyCustom($title, $body)` | Thông báo tùy chỉnh |

**Config:** `TELEGRAM_BOT_TOKEN`, `TELEGRAM_CHAT_ID`

---

## 🔧 Helper Functions (`app/Helpers/helpers.php`)

| Hàm | Mô tả |
|------|--------|
| `env($key, $default)` | Đọc biến từ `.env` (static cache) |
| `redirect($url)` | Redirect (prefix APP_URL) |
| `url($path)` | Tạo URL đầy đủ |
| `asset($path)` | URL cho assets (css, js, img) |
| `e($string)` | Escape HTML (chống XSS) |
| `setFlash($type, $message)` | Lưu flash message vào session |
| `getFlash()` | Lấy + xóa flash message |
| `isLoggedIn()` | Check đã login? |
| `isAdmin()` | Check role admin? |
| `currentUser()` | Lấy thông tin user hiện tại từ session |
| `formatMoney($amount)` | Format VNĐ: `100.000đ` |
| `formatDate($date)` | Format ngày: `dd/mm/yyyy HH:ii` |
| `csrfToken()` / `csrfField()` | Generate CSRF token / hidden input |
| `verifyCsrf()` | Validate CSRF token (auto regenerate) |
| `orderStatusLabel($status)` | Badge HTML cho trạng thái đơn hàng |
| `app_setting($key, $default)` | Lấy setting từ DB (static cache) |

---

## 🔐 Middleware

### `AuthMiddleware`
| Method | Mô tả |
|--------|--------|
| `requireLogin()` | Chặn nếu chưa đăng nhập → redirect `/login` |
| `requireAdmin()` | Chặn nếu không phải admin → redirect `/` |
| `redirectIfLoggedIn()` | Redirect nếu đã login (dùng cho trang login/register) |

---

## 🗺️ Routes — Danh Sách Đầy Đủ

### Public
| Method | URL | Controller@Method |
|--------|-----|-------------------|
| GET | `/` | `HomeController@index` |
| GET | `/login` | `AuthController@showLogin` |
| POST | `/login` | `AuthController@login` |
| GET | `/register` | `AuthController@showRegister` |
| POST | `/register` | `AuthController@register` |
| GET | `/logout` | `AuthController@logout` |

### User — Shop
| Method | URL | Controller@Method |
|--------|-----|-------------------|
| GET | `/shop` | `User/ShopController@index` |
| GET | `/shop/games` | `User/ShopController@games` |
| GET | `/shop/services` | `User/ShopController@services` |
| GET | `/product/{id}` | `User/ShopController@productDetail` |
| GET | `/service/{id}` | `User/ShopController@serviceDetail` |
| POST | `/order/product/{id}` | `User/ShopController@buyProduct` |
| POST | `/order/service/{id}` | `User/ShopController@buyService` |

### User — Banking
| Method | URL | Controller@Method |
|--------|-----|-------------------|
| GET | `/banking` | `User/BankingController@index` |
| POST | `/banking/create` | `User/BankingController@createInvoice` |
| POST | `/banking/check-status` | `User/BankingController@checkStatus` |
| GET | `/banking/history` | `User/BankingController@history` |
| POST | `/banking/card-deposit` | `User/BankingController@cardDeposit` |

### User — Features
| Method | URL | Controller@Method |
|--------|-----|-------------------|
| GET | `/my-orders` | `User/OrderController@index` |
| GET | `/profile` | `User/ProfileController@index` |
| GET | `/search` | `User/PageController@search` |
| GET | `/leaderboard` | `User/PageController@leaderboard` |
| GET | `/events` | `User/PageController@events` |
| GET | `/green-points` | `User/PageController@greenPoints` |
| POST | `/green-points/exchange` | `User/PageController@exchangeGreenPoints` |
| GET | `/guide` | `User/PageController@guide` |
| GET | `/contact` | `User/PageController@contact` |
| POST | `/contact/send` | `User/PageController@submitContact` |
| GET | `/colors` | `User/PageController@colors` |
| GET | `/lucky-wheel` | `User/LuckyWheelController@index` |
| POST | `/lucky-wheel/spin` | `User/LuckyWheelController@spin` |
| GET | `/mystery-bag` | `User/MysteryBagController@index` |
| POST | `/mystery-bag/open/{id}` | `User/MysteryBagController@open` |
| POST | `/mystery-bag/checkin` | `User/MysteryBagController@checkin` |
| GET | `/chess` | `User/ChessController@index` |
| POST | `/chess/record-win` | `User/ChessController@recordWin` |

### Admin
| Method | URL | Controller@Method |
|--------|-----|-------------------|
| GET | `/admin` | `Admin/DashboardController@index` |
| GET/POST | `/admin/categories/*` | `Admin/CategoryController` — CRUD |
| GET/POST | `/admin/products/*` | `Admin/ProductController` — CRUD |
| GET/POST | `/admin/services/*` | `Admin/ServiceController` — CRUD |
| GET/POST | `/admin/orders/*` | `Admin/OrderController` — list, show, updateStatus |
| GET/POST | `/admin/users/*` | `Admin/UserController` — list, show, updateBalance, addSpins |
| GET | `/admin/invoices` | `Admin/InvoiceController@index` |
| GET/POST | `/admin/lucky-wheel/*` | `Admin/LuckyWheelController` — cấu hình prizes |
| GET/POST | `/admin/mystery-bag/*` | `Admin/MysteryBagController` — CRUD bags + items + bulk import |
| GET/POST | `/admin/events/*` | `Admin/EventController` — CRUD |
| GET/POST | `/admin/settings/*` | `Admin/SettingsController` — general + deposit settings |

---

## ⏰ Cron Jobs

### `cron/deposit_cron.php` — Nạp Tiền Tự Động
```bash
# Chạy mỗi 1-2 phút
* * * * * php /path/cron/deposit_cron.php --type=mbbank >> /path/logs/deposit.log 2>&1
```
- Hỗ trợ nhiều bank: `mbbank`, `vietcombank`, `acb`, `momo`, `thesieure`, `card`
- Flow: Gọi API bank → match nội dung CK với `trans_id` trong invoices → cộng tiền → log giao dịch
- Tự hủy hóa đơn quá 30 phút
- Hỗ trợ commission (nếu user có referrer_id)
- Nạp thẻ cào: gọi API partner check thẻ → cộng tiền (trừ phí)

### `cron/smm_sync_orders.php` — Đồng Bộ Đơn SMM
- Lấy đơn SMM đang pending → gọi API `getOrderStatus` → cập nhật trạng thái

### `cron/smm_sync_services.php` — Đồng Bộ Dịch Vụ SMM
- Gọi API `getServices` → cập nhật/tạo mới dịch vụ trong DB

---

## 🔑 Biến Môi Trường (.env)

| Key | Mô tả |
|-----|--------|
| `APP_NAME` | Tên app (hiển thị trên web) |
| `APP_URL` | URL gốc (local: `http://localhost/mxh_web/public`) |
| `APP_DEBUG` | Hiển thị lỗi chi tiết (true/false) |
| `DB_HOST/PORT/DATABASE/USERNAME/PASSWORD` | Kết nối MySQL |
| `UPLOAD_PATH` | Thư mục upload |
| `BANK_API_URL` | URL API bank (web2m / thueapibank) |
| `BANK_API_TOKEN` | Token API bank |
| `SMM_API_URL` | URL API SMM Panel |
| `SMM_API_KEY` | API key SMM Panel |
| `SMM_PRICE_MARKUP` | % markup giá bán so với giá gốc |
| `TELEGRAM_BOT_TOKEN` | Token Telegram Bot |
| `TELEGRAM_CHAT_ID` | Chat ID nhận thông báo |
| `CARD_API_URL` | URL API nạp thẻ cào |
| `CARD_PARTNER_ID` / `CARD_PARTNER_KEY` | Credentials thẻ cào |

---

## 🧮 Settings Quan Trọng (DB `settings`)

| Key | Mô tả | Default |
|-----|--------|---------|
| `bank_prefix` | Prefix nội dung CK | `NAP` |
| `bank_acc_name` | Tên TK ngân hàng | — |
| `bank_acc_number` | STK ngân hàng | — |
| `bank_name` | Tên ngân hàng | MBBank |
| `site_notice` | Thông báo trên trang chủ | — |
| `wheel_spin_cost` | Giá 1 lượt quay (VNĐ) | 10000 |
| `checkin_spins_per_day` | Số lượt quay free mỗi ngày điểm danh | 1 |
| `checkin_bonus_day7` | Bonus lượt quay ngày 7 | 3 |
| `checkin_green_points` | Điểm xanh mỗi lần điểm danh | 5 |
| `deposit_discount` | % khuyến mãi nạp tiền | 0 |
| `comm_percent` | % hoa hồng giới thiệu | 10 |

---

## 🔄 Luồng Nghiệp Vụ Chính

### 1. Nạp Tiền
```
User nhập số tiền → createInvoice (trans_id = "NAP" + userId)
→ User chuyển khoản đúng nội dung
→ [Cron hoặc User bấm Check] → Gọi API bank
→ Match nội dung CK → Cộng tiền balance → Log transaction → Notify Telegram
```

### 2. Mua Acc Game
```
User chọn product → buyProduct → CSRF check → Check balance
→ Trừ tiền → Đánh dấu product sold → Tạo order (completed)
→ Copy account_info vào order.account_data → Log transaction
```

### 3. Mua Dịch Vụ MXH
```
User chọn service → buyService → CSRF check → Check balance
→ Trừ tiền → Gọi API SMM addOrder → Tạo order (pending + smm_order_id)
→ Log transaction → [Cron sync SMM status]
```

### 4. Vòng Quay May Mắn
```
Điểm danh 7 ngày → Nhận lượt quay free (+ green points)
Hoặc trả phí wheel_spin_cost
→ spin() (weighted random) → Cộng phần thưởng → Log history
```

### 5. Túi Mù (Blind Box)
```
User mở túi → Trả phí → open() (random 1 item available)
→ Đánh dấu item status=0 → Hiển thị account info → Log history
```

### 6. Đổi Điểm Xanh
```
Tích lũy từ: điểm danh, giao dịch, sự kiện
→ 100 điểm xanh = 10,000đ → Cộng balance
```

---

## 📌 Quy Ước Code

- **Session**: `user_id`, `username`, `user_role`, `user_balance`, `csrf_token`
- **CSRF**: Mọi form POST phải có `csrfField()`, controller check `verifyCsrf()`
- **View path**: Dùng dấu `.` thay `/` → `user.banking` → `views/user/banking.php`
- **Flash messages**: `setFlash('success|danger|warning|info', $msg)` → hiển thị bởi layout
- **Password**: `password_hash(bcrypt)` / `password_verify()`
- **Money format**: `number_format($amount, 0, ',', '.') . 'đ'`
- **Date format**: `d/m/Y H:i`
