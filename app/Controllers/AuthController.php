<?php

require_once BASE_PATH . '/core/Controller.php';
require_once BASE_PATH . '/app/Middleware/AuthMiddleware.php';

class AuthController extends Controller
{

    /**
     * Hiển thị form đăng nhập
     */
    public function showLogin()
    {
        AuthMiddleware::redirectIfLoggedIn();
        $this->view('auth.login', ['pageTitle' => 'Đăng nhập']);
    }

    /**
     * Xử lý đăng nhập
     */
    public function login()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn. Vui lòng thử lại.');
            redirect('/login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            setFlash('danger', 'Vui lòng nhập đầy đủ thông tin.');
            redirect('/login');
        }

        $userModel = $this->model('User');
        $user = $userModel->findByUsername($username);

        if (!$user || !password_verify($password, $user['password'])) {
            setFlash('danger', 'Tên đăng nhập hoặc mật khẩu không đúng.');
            redirect('/login');
        }

        // Tạo session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_balance'] = $user['balance'];

        setFlash('success', 'Đăng nhập thành công! Chào mừng ' . $user['username']);

        if ($user['role'] === 'admin') {
            redirect('/admin');
        } else {
            redirect('/');
        }
    }

    /**
     * Hiển thị form đăng ký
     */
    public function showRegister()
    {
        AuthMiddleware::redirectIfLoggedIn();
        $this->view('auth.register', ['pageTitle' => 'Đăng ký']);
    }

    /**
     * Xử lý đăng ký
     */
    public function register()
    {
        if (!verifyCsrf()) {
            setFlash('danger', 'Phiên làm việc hết hạn. Vui lòng thử lại.');
            redirect('/register');
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Validate
        $errors = $this->validate($_POST, [
            'username' => 'required|min:3|max:50',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($password !== $confirmPassword) {
            $errors['confirm_password'] = 'Mật khẩu xác nhận không khớp.';
        }

        if (!empty($errors)) {
            setFlash('danger', implode('<br>', $errors));
            redirect('/register');
        }

        $userModel = $this->model('User');

        // Kiểm tra trùng
        if ($userModel->findByUsername($username)) {
            setFlash('danger', 'Tên đăng nhập đã tồn tại.');
            redirect('/register');
        }
        if ($userModel->findByEmail($email)) {
            setFlash('danger', 'Email đã được sử dụng.');
            redirect('/register');
        }

        // Tạo user
        $userModel->register([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'user',
            'balance' => 0
        ]);

        setFlash('success', 'Đăng ký thành công! Vui lòng đăng nhập.');
        redirect('/login');
    }

    /**
     * Đăng xuất
     */
    public function logout()
    {
        session_destroy();
        redirect('/login');
    }
}
