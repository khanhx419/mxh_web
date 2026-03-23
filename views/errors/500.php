<?php
/**
 * 500 Error Page
 */
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>500 - Lỗi máy chủ</title>
    <style>
        body { font-family: Arial, sans-serif; background: #1a1a2e; color: #eee; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .container { text-align: center; }
        h1 { font-size: 80px; color: #e94560; margin: 0; }
        p { font-size: 18px; color: #aaa; }
        a { color: #6c63ff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h1>500</h1>
        <p>Đã xảy ra lỗi máy chủ. Vui lòng thử lại sau.</p>
        <p><a href="<?= APP_URL ?? '/' ?>">← Về trang chủ</a></p>
    </div>
</body>
</html>
