<?php

/**
 * Auth Middleware
 * Kiểm tra quyền truy cập
 */
class AuthMiddleware
{

    /**
     * Yêu cầu đăng nhập
     */
    public static function requireLogin()
    {
        if (!isLoggedIn()) {
            setFlash('warning', 'Vui lòng đăng nhập để tiếp tục.');
            redirect('/login');
        }
    }

    /**
     * Yêu cầu quyền Admin
     */
    public static function requireAdmin()
    {
        self::requireLogin();
        if (!isAdmin()) {
            setFlash('danger', 'Bạn không có quyền truy cập trang này.');
            redirect('/');
        }
    }

    /**
     * Chuyển hướng nếu đã đăng nhập
     */
    public static function redirectIfLoggedIn()
    {
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('/admin');
            } else {
                redirect('/');
            }
        }
    }
}
