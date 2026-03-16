<?php

/**
 * TelegramService - Gửi thông báo qua Telegram Bot API
 */
class TelegramService
{
    private $botToken;
    private $chatId;

    public function __construct()
    {
        $this->botToken = env('TELEGRAM_BOT_TOKEN', '');
        $this->chatId = env('TELEGRAM_CHAT_ID', '');
    }

    /**
     * Kiểm tra Telegram đã được cấu hình chưa
     */
    public function isConfigured()
    {
        return !empty($this->botToken) && !empty($this->chatId);
    }

    /**
     * Gửi tin nhắn text qua Telegram
     */
    public function sendMessage($text, $parseMode = 'HTML')
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        $data = [
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => true
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("[TelegramService] cURL Error: {$error}");
            return false;
        }

        $result = json_decode($response, true);
        return ($result['ok'] ?? false) === true;
    }

    /**
     * Thông báo nạp tiền thành công
     */
    public function notifyDeposit($userId, $username, $amount, $txId, $newBalance = null)
    {
        $amountFormatted = number_format($amount, 0, ',', '.') . 'đ';
        $balanceFormatted = $newBalance !== null ? number_format($newBalance, 0, ',', '.') . 'đ' : 'N/A';
        $time = date('d/m/Y H:i:s');

        $text = "💰 <b>NẠP TIỀN THÀNH CÔNG</b>\n\n"
            . "👤 User: <b>{$username}</b> (ID: {$userId})\n"
            . "💵 Số tiền: <b>{$amountFormatted}</b>\n"
            . "🏦 Mã GD: <code>{$txId}</code>\n"
            . "💳 Số dư mới: <b>{$balanceFormatted}</b>\n"
            . "🕐 Thời gian: {$time}";

        return $this->sendMessage($text);
    }

    /**
     * Thông báo đơn hàng mới
     */
    public function notifyOrder($userId, $username, $orderType, $amount)
    {
        $amountFormatted = number_format($amount, 0, ',', '.') . 'đ';
        $time = date('d/m/Y H:i:s');

        $text = "🛒 <b>ĐƠN HÀNG MỚI</b>\n\n"
            . "👤 User: <b>{$username}</b> (ID: {$userId})\n"
            . "📦 Loại: {$orderType}\n"
            . "💵 Giá trị: <b>{$amountFormatted}</b>\n"
            . "🕐 Thời gian: {$time}";

        return $this->sendMessage($text);
    }

    /**
     * Thông báo tùy chỉnh
     */
    public function notifyCustom($title, $body)
    {
        $text = "🔔 <b>{$title}</b>\n\n{$body}";
        return $this->sendMessage($text);
    }
}
