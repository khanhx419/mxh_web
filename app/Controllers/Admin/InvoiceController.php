<?php
require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class InvoiceController extends Controller
{
    public function __construct() { AuthMiddleware::requireAdmin(); }

    public function index()
    {
        $db = getDatabaseConnection();
        $stmt = $db->query("
            SELECT i.*, u.username FROM invoices i 
            JOIN users u ON i.user_id = u.id 
            ORDER BY i.created_at DESC LIMIT 50
        ");
        $invoices = $stmt->fetchAll();

        $this->view('admin.invoices.index', [
            'pageTitle' => 'Quản lý Nạp tiền',
            'invoices' => $invoices
        ], 'admin');
    }
}
