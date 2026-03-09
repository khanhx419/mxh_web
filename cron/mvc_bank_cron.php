<?php
/**
 * Cron Job - Gọi bank.php để xử lý nạp tiền tự động
 *
 * Crontab recommended (mỗi 1-2 phút):
 *   * * * * * php /path/to/cron/mvc_bank_cron.php >> /path/to/logs/bank_cron.log 2>&1
 *
 * Script này đơn giản gọi bank.php ở thư mục gốc để thực hiện:
 *   1. Hủy hoá đơn quá hạn
 *   2. Gọi API ngân hàng
 *   3. Khớp giao dịch với invoice pending
 *   4. Cộng tiền tự động
 */

$bankScript = __DIR__ . '/../bank.php';

if (!file_exists($bankScript)) {
    echo "[ERROR] bank.php not found at: {$bankScript}\n";
    exit(1);
}

// Include bank.php trực tiếp (nó đã tự bootstrap)
require_once $bankScript;
