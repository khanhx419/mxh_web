<?php
/**
 * SmmApiService - Lớp helper gọi API SMM Panel (sub6sao.com)
 *
 * API Endpoints:
 *   POST https://sub6sao.com/api/v2
 *   Params: key, action, + các param tùy action
 *
 * Actions:
 *   services  → Lấy danh sách dịch vụ
 *   add       → Tạo đơn hàng (service, link, quantity)
 *   status    → Kiểm tra trạng thái đơn (order)
 *   balance   → Kiểm tra số dư
 */

class SmmApiService
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        $this->apiUrl = env('SMM_API_URL', 'https://sub6sao.com/api/v2');
        $this->apiKey = env('SMM_API_KEY', '');
    }

    /**
     * Gọi API chung
     */
    private function request($params)
    {
        $params['key'] = $this->apiKey;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->apiUrl,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => "cURL Error: {$error}"];
        }

        if ($httpCode !== 200) {
            return ['error' => "HTTP Error: {$httpCode}"];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'JSON Parse Error: ' . json_last_error_msg()];
        }

        return $data;
    }

    /**
     * Lấy danh sách dịch vụ từ web mẹ
     * Response: [{ service, name, type, category, rate, min, max, refill }, ...]
     */
    public function getServices()
    {
        return $this->request(['action' => 'services']);
    }

    /**
     * Tạo đơn hàng trên web mẹ
     *
     * @param int    $serviceId  ID dịch vụ trên web mẹ
     * @param string $link       Link mục tiêu
     * @param int    $quantity   Số lượng
     * @return array Response: { order: int } hoặc { error: string }
     */
    public function addOrder($serviceId, $link, $quantity)
    {
        return $this->request([
            'action' => 'add',
            'service' => $serviceId,
            'link' => $link,
            'quantity' => $quantity
        ]);
    }

    /**
     * Kiểm tra trạng thái đơn hàng trên web mẹ
     *
     * @param int $orderId  Mã đơn trên web mẹ (smm_order_id)
     * @return array Response: { charge, start_count, status, remains, currency }
     */
    public function getOrderStatus($orderId)
    {
        return $this->request([
            'action' => 'status',
            'order' => $orderId
        ]);
    }

    /**
     * Kiểm tra nhiều đơn cùng lúc
     *
     * @param array $orderIds  Mảng các mã đơn
     * @return array
     */
    public function getMultiOrderStatus($orderIds)
    {
        return $this->request([
            'action' => 'status',
            'orders' => implode(',', $orderIds)
        ]);
    }

    /**
     * Kiểm tra số dư tài khoản trên web mẹ
     *
     * @return array Response: { balance, currency }
     */
    public function getBalance()
    {
        return $this->request(['action' => 'balance']);
    }
}
